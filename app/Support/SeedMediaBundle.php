<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/** The versioned seed bundle is separate from private, editable CMS uploads. */
class SeedMediaBundle
{
    private ?array $manifest = null;

    public function __construct(private SeedMediaDownloader $downloader) {}

    public function directory(): string
    {
        return database_path('seeders/assets');
    }

    public function manifestPath(): string
    {
        return database_path('seeders/data/media-manifest.json');
    }

    public function assets(string $collection, array $record): array
    {
        $assets = [];
        $photo = $collection === 'programmes' ? (CmsSchema::seed('media')[$record['photoId'] ?? ''] ?? null) : null;
        $local = $record['coverImage'] ?? $record['logo'] ?? $photo['src'] ?? null;
        if ($record['seedImage'] ?? $record['imageSourceUrl'] ?? $local) {
            $assets[] = [
                'collection' => 'cover', 'url' => $record['imageSourceUrl'] ?? null, 'local' => $local,
                'seedFile' => $record['seedImage'] ?? null,
                'properties' => [
                    'alt' => $record['coverAlt'] ?? $photo['alt'] ?? $record['title'] ?? '',
                    'sourcePage' => $record['sourceUrl'] ?? null,
                    ...($photo ? Arr::only($photo, ['credit', 'generated', 'representative']) : []),
                ],
            ];
        }
        if ($record['path'] ?? null) {
            $assets[] = ['collection' => 'document', 'local' => $record['path'], 'seedFile' => $record['seedDocument'] ?? null];
        }
        foreach ([...($record['photos'] ?? []), ...($record['media'] ?? [])] as $item) {
            $assets[] = [
                'collection' => 'gallery', 'id' => $item['id'] ?? null, 'url' => $item['src'] ?? null,
                'seedFile' => $item['seedFile'] ?? null,
                'properties' => Arr::only($item, ['alt', 'caption', 'credit', 'consentApproved', 'visible', 'transcript']) + ['visible' => true, 'sourcePage' => $record['sourceUrl'] ?? null],
            ];
        }

        return $assets;
    }

    /** Keep the original import identity when a URL is replaced by a bundled file. */
    public function key(array $asset): string
    {
        return hash('sha256', $asset['collection'].':'.($asset['id'] ?? $asset['url'] ?? $asset['local'] ?? $asset['seedFile']));
    }

    /** Normal database seeding never performs a network request. */
    public function path(array $asset): string
    {
        $file = $this->manifest()['files'][$this->key($asset)] ?? null;
        $relative = ($asset['seedFile'] ?? null) ?: ($file['path'] ?? null);
        if ($relative) {
            $path = $this->checkedPath($relative, $asset['collection']);
            if ($file && $file['path'] === $relative && ! hash_equals($file['sha256'], hash_file('sha256', $path))) {
                throw new RuntimeException('The bundled file checksum has changed. Rebuild the seed bundle or supply an explicit seedFile.');
            }

            return $path;
        }
        if ($local = $this->publicFile($asset['local'] ?? $asset['url'] ?? null)) {
            $this->downloader->validateFile($local, $asset['collection']);

            return $local;
        }

        throw new RuntimeException('No local seed file is available. Run cms:bundle-seed-media, or supply a file in database/seeders/assets.');
    }

    /** Prepare source files explicitly; unlike seed(), this operation may download. */
    public function bundle(array $records, ?callable $progress = null): array
    {
        File::ensureDirectoryExists($this->directory());
        $manifest = $this->manifest();
        $statistics = ['bundled' => 0, 'preserved' => 0, 'failures' => []];
        foreach ($records as $collection => $items) {
            foreach ($items as $record) {
                foreach ($this->assets($collection, $record) as $asset) {
                    $key = $this->key($asset);
                    $source = $asset['url'] ?? $asset['local'] ?? $asset['seedFile'];
                    $identity = ['collection' => $collection, 'record' => $record['id'], 'title' => $record['title'], 'mediaCollection' => $asset['collection'], 'source' => $source];
                    try {
                        if (isset($manifest['files'][$key]) && (blank($asset['seedFile'] ?? null) || $manifest['files'][$key]['path'] === $asset['seedFile'])) {
                            try {
                                $this->path($asset);
                                $statistics['preserved']++;

                                continue;
                            } catch (RuntimeException) {
                                // Restore an incomplete or damaged bundle from its original source.
                            }
                        }
                        if (filled($asset['seedFile'] ?? null)) {
                            $path = $this->checkedPath($asset['seedFile'], $asset['collection']);
                        } elseif ($path = $this->publicFile($asset['local'] ?? $asset['url'] ?? null)) {
                            $this->downloader->validateFile($path, $asset['collection']);
                        } elseif (is_string($source) && str_starts_with($source, 'https://')) {
                            $path = $this->downloader->download($source, $asset['collection']);
                        } else {
                            throw new RuntimeException('The source file does not exist.');
                        }
                        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
                        $extension = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'video/mp4' => 'mp4', 'video/webm' => 'webm', 'application/pdf' => 'pdf'][$mime];
                        $slug = Str::slug($record['slug']);
                        $relative = filled($asset['seedFile'] ?? null) ? $asset['seedFile'] : ($asset['collection'] === 'gallery'
                            ? $collection.'/'.$slug.'/'.Str::slug($asset['id'] ?? substr($key, 0, 16)).'.'.$extension
                            : $collection.'/'.$slug.'.'.$extension);
                        $destination = $this->directory().'/'.$relative;
                        File::ensureDirectoryExists(dirname($destination));
                        if (realpath($destination) !== realpath($path)) {
                            File::copy($path, $destination);
                        }
                        chmod($destination, 0644);
                        $manifest['files'][$key] = [...$identity, 'path' => $relative, 'mimeType' => $mime, 'bytes' => filesize($destination), 'sha256' => hash_file('sha256', $destination)];
                        unset($manifest['missing'][$key]);
                        $statistics['bundled']++;
                        $progress && $progress('Bundled: '.$relative);
                    } catch (Throwable $exception) {
                        // A stale manifest must not claim that a missing/corrupt file is usable.
                        unset($manifest['files'][$key]);
                        $manifest['missing'][$key] = [...$identity, 'reason' => $exception->getMessage()];
                        $statistics['failures'][] = $record['title'].' — '.$source.': '.$exception->getMessage();
                        $progress && $progress('Unavailable: '.$record['title']);
                    }
                }
            }
        }
        $temporary = tempnam(dirname($this->manifestPath()), 'media-manifest-');
        try {
            File::put($temporary, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);
            File::move($temporary, $this->manifestPath());
            chmod($this->manifestPath(), 0644);
            $this->manifest = $manifest;
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }

        return $statistics;
    }

    private function manifest(): array
    {
        return $this->manifest ??= is_file($this->manifestPath())
            ? json_decode(File::get($this->manifestPath()), true, 512, JSON_THROW_ON_ERROR)
            : ['version' => 1, 'files' => [], 'missing' => []];
    }

    private function checkedPath(string $relative, string $collection): string
    {
        $root = realpath($this->directory());
        $path = realpath($this->directory().'/'.$relative);
        if (! $root || ! $path || ! str_starts_with($path, $root.DIRECTORY_SEPARATOR) || ! is_file($path)) {
            throw new RuntimeException('The bundled file must exist inside database/seeders/assets.');
        }
        $this->downloader->validateFile($path, $collection);

        return $path;
    }

    private function publicFile(?string $relative): ?string
    {
        if (! $relative || ! str_starts_with($relative, '/')) {
            return null;
        }
        $root = realpath(public_path());
        $path = realpath(public_path(ltrim($relative, '/')));

        return $root && $path && str_starts_with($path, $root.DIRECTORY_SEPARATOR) && is_file($path) ? $path : null;
    }
}
