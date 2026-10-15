<?php

namespace App\Models;

use App\Support\CmsSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ContentEntry extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = ['created_by', 'updated_by', 'published_by'];

    protected function casts(): array
    {
        return ['data' => 'array', 'is_active' => 'boolean', 'published_at' => 'datetime', 'sort_order' => 'integer'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $entry): void {
            $entry->id ??= (string) Str::uuid();
            $entry->data ??= [];
            $entry->slug = $entry->slug ?: Str::slug($entry->title);
            if ($entry->exists && $entry->isDirty('collection')) {
                throw ValidationException::withMessages(['collection' => 'A record cannot be moved to another collection.']);
            }
            CmsSchema::validate($entry);
            if (Auth::check()) {
                $entry->updated_by = Auth::id();
                if (! $entry->exists) {
                    $entry->created_by = Auth::id();
                }
            }
            if ($entry->status === 'published' && (! $entry->exists || $entry->isDirty('status'))) {
                $entry->published_at ??= now();
                $entry->published_by = Auth::id();
            }
            if ($entry->isDirty('slug') && $entry->exists) {
                if (DB::table('content_redirects')->where('collection', $entry->collection)->where('slug', $entry->slug)->where('entry_id', '!=', $entry->id)->exists()) {
                    throw ValidationException::withMessages(['slug' => 'This address is reserved by another record.']);
                }
            }
        });
        static::updated(function (self $entry): void {
            if ($entry->wasChanged('slug') && $entry->getOriginal('published_at')) {
                DB::table('content_redirects')->updateOrInsert(
                    ['collection' => $entry->collection, 'slug' => $entry->getOriginal('slug')],
                    ['entry_id' => $entry->id],
                );
            }
        });
        static::deleted(function (self $entry): void {
            $fields = match ($entry->collection) {
                'programmes' => ['programmeId', 'programmeIds'],
                'projects' => ['projectId', 'relatedProjectIds'],
                'events' => ['eventId'],
                'reports' => ['reportIds'],
                default => [],
            };
            if (! $fields) {
                return;
            }
            foreach (self::withTrashed()->whereKeyNot($entry->id)->cursor() as $related) {
                $data = $related->data ?? [];
                $changed = false;
                foreach ($fields as $field) {
                    if (($data[$field] ?? null) === $entry->id) {
                        $data[$field] = null;
                        $changed = true;
                    } elseif (is_array($data[$field] ?? null) && in_array($entry->id, $data[$field], true)) {
                        $data[$field] = array_values(array_diff($data[$field], [$entry->id]));
                        $changed = true;
                    }
                }
                if ($changed) {
                    DB::table('content_entries')->where('id', $related->id)->update(['data' => json_encode($data, JSON_THROW_ON_ERROR), 'updated_at' => now(), 'updated_by' => Auth::id()]);
                }
            }
        });
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function registerMediaCollections(): void
    {
        $images = ['image/jpeg', 'image/png', 'image/webp'];
        foreach (['cover', 'poster'] as $name) {
            $this->addMediaCollection($name)->useDisk('cms')->singleFile()->acceptsFile(fn (File $file) => in_array($file->mimeType, $images, true) && $file->size <= 10 * 1024 * 1024);
        }
        $this->addMediaCollection('gallery')->useDisk('cms')->acceptsFile(fn (File $file) => (in_array($file->mimeType, $images, true) && $file->size <= 10 * 1024 * 1024) || (in_array($file->mimeType, ['video/mp4', 'video/webm'], true) && $file->size <= 100 * 1024 * 1024));
        $this->addMediaCollection('document')->useDisk('cms')->singleFile()->acceptsFile(fn (File $file) => $file->mimeType === 'application/pdf' && $file->size <= 20 * 1024 * 1024);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && str_starts_with($media->mime_type ?? '', 'image/')) {
            $this->addMediaConversion('thumb')->width(640)->height(480)->format('webp')->nonQueued();
        }
    }
}
