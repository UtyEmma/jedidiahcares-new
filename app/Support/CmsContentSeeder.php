<?php

namespace App\Support;

use App\Models\ContentEntry;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CmsContentSeeder
{
    public function __construct(private CmsSeedCatalog $catalog, private SeedMediaBundle $bundle) {}

    public function seed(array $collections, bool $withMedia = true, ?callable $progress = null): array
    {
        $records = $this->catalog->select($collections);
        $statistics = ['created' => 0, 'preserved' => 0, 'media' => 0, 'failures' => []];
        $entries = [];
        $created = [];
        // Resolve every ID first, then validate relationships in a second pass.
        DB::transaction(function () use ($records, &$entries, &$created, &$statistics): void {
            foreach ($records as $collection => $items) {
                foreach ($items as $id => $record) {
                    $history = $this->history($collection, $id);
                    if ($history) {
                        $entry = ContentEntry::withTrashed()->find($history->entry_id);
                    } else {
                        $entry = ContentEntry::withTrashed()->where('collection', $collection)
                            ->where(fn ($query) => $query->whereKey($id)->orWhere('slug', $record['slug']))->first();
                        if (! $entry) {
                            $redirectId = DB::table('content_redirects')->where('collection', $collection)->where('slug', $record['slug'])->value('entry_id');
                            $entry = $redirectId ? ContentEntry::withTrashed()->find($redirectId) : null;
                        }
                        if (! $entry) {
                            $entry = ContentEntry::create([
                                'id' => $id, 'collection' => $collection, 'slug' => $record['slug'],
                                'title' => $record['title'] ?? $record['question'] ?? $record['label'],
                                'summary' => $record['summary'] ?? '',
                                'sort_order' => $record['sortOrder'] ?? array_search($id, array_keys($items), true),
                                'status' => $this->status($record), 'is_active' => $record['isActive'] ?? true,
                                'published_at' => $record['publishedAt'] ?? $record['sourcePublishedAt'] ?? null,
                                'data' => Arr::except($this->data($collection, $record), array_keys(CmsSeedCatalog::RELATIONSHIPS)),
                            ]);
                            $created[$collection][$id] = $record;
                            $statistics['created']++;
                        }
                        DB::table('cms_seed_imports')->insert([
                            'collection' => $collection, 'source_id' => $id, 'entry_id' => $entry->id,
                            'media_keys' => '[]', 'created_at' => now(), 'updated_at' => now(),
                        ]);
                    }
                    $entries[$collection][$id] = $entry;
                    if (! isset($created[$collection][$id])) {
                        $statistics['preserved']++;
                    }
                }
            }
            foreach ($created as $collection => $items) {
                foreach ($items as $id => $record) {
                    $data = $this->data($collection, $record);
                    foreach (CmsSeedCatalog::RELATIONSHIPS as $field => $target) {
                        if (! isset($data[$field])) {
                            continue;
                        }
                        $resolve = fn (string $sourceId) => ($entries[$target][$sourceId] ?? null)?->id ?? $this->history($target, $sourceId)?->entry_id ?? $sourceId;
                        $data[$field] = is_array($data[$field]) ? array_map($resolve, $data[$field]) : $resolve($data[$field]);
                    }
                    $entries[$collection][$id]->update(['data' => $data]);
                }
            }
        });

        if ($withMedia) {
            foreach ($records as $collection => $items) {
                foreach ($items as $id => $record) {
                    $entry = $entries[$collection][$id];
                    if (! $entry || $entry->trashed() || $entry->status === 'archived') {
                        continue;
                    }
                    foreach ($this->bundle->assets($collection, $record) as $asset) {
                        $key = $this->bundle->key($asset);
                        $history = $this->history($collection, $id);
                        $completed = json_decode($history->media_keys ?? '[]', true, 512, JSON_THROW_ON_ERROR);
                        if (in_array($key, $completed, true)) {
                            continue;
                        }
                        try {
                            if ($this->importMedia($entry, $asset, $key)) {
                                $statistics['media']++;
                                $progress && $progress('Imported media: '.$entry->title);
                            }
                            // Remember skipped existing covers and imported assets, even after staff remove them.
                            DB::table('cms_seed_imports')->where('id', $history->id)->update([
                                'media_keys' => json_encode([...$completed, $key], JSON_THROW_ON_ERROR), 'updated_at' => now(),
                            ]);
                        } catch (Throwable $exception) {
                            $message = $entry->title.' — '.($asset['url'] ?? $asset['local'] ?? $asset['seedFile']).': '.$exception->getMessage();
                            $statistics['failures'][] = $message;
                            $progress && $progress('Unavailable media: '.$entry->title);
                        }
                    }
                }
            }
        }

        return $statistics;
    }

    private function history(string $collection, string $id): ?object
    {
        return DB::table('cms_seed_imports')->where('collection', $collection)->where('source_id', $id)->first();
    }

    private function status(array $record): string
    {
        return ($record['isPublished'] ?? false) && ($record['publicationState'] ?? '') === 'approved' ? 'published' : (($record['publicationState'] ?? '') === 'archived' ? 'archived' : 'draft');
    }

    private function data(string $collection, array $record): array
    {
        $data = Arr::only($record, [...CmsSchema::FIELDS[$collection], 'seoTitle', 'seoDescription']);
        if (blank($data['coverAlt'] ?? null)) {
            foreach ($this->bundle->assets($collection, $record) as $asset) {
                if ($asset['collection'] === 'cover') {
                    $data['coverAlt'] = $asset['properties']['alt'];
                    break;
                }
            }
        }

        return $data;
    }

    private function importMedia(ContentEntry $entry, array $asset, string $key): bool
    {
        if ($entry->getMedia($asset['collection'])->contains(fn ($media) => $media->getCustomProperty('seedKey') === $key)) {
            return false;
        }
        if ($asset['collection'] !== 'gallery' && $entry->hasMedia($asset['collection'])) {
            return false;
        }
        $source = $asset['url'] ?? $asset['local'] ?? $asset['seedFile'];
        $path = $this->bundle->path($asset);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        $extension = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'video/mp4' => 'mp4', 'video/webm' => 'webm', 'application/pdf' => 'pdf'][$mime];
        $properties = ($asset['properties'] ?? []) + ['seedKey' => $key, 'sourceUrl' => $source, 'seedFile' => Str::after($path, base_path().DIRECTORY_SEPARATOR), 'sourceSha256' => hash_file('sha256', $path)];
        $media = $entry->addMedia($path)->preservingOriginal()->usingName($properties['caption'] ?? $entry->title)
            ->usingFileName(Str::slug($entry->slug).'-'.substr($key, 0, 12).'.'.$extension)
            ->withCustomProperties($properties)->toMediaCollection($asset['collection'], 'cms');
        $entry->unsetRelation('media');

        return $media->exists;
    }
}
