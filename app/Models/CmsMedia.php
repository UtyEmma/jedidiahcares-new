<?php

namespace App\Models;

use App\Support\CmsSchema;
use App\Support\PageContentSchema;
use Illuminate\Support\Facades\Auth;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CmsMedia extends Media implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('poster')->useDisk('cms')->singleFile()->acceptsFile(fn (File $file) => in_array($file->mimeType, ['image/jpeg', 'image/png', 'image/webp'], true) && $file->size <= 10 * 1024 * 1024);
        $this->addMediaCollection('captions')->useDisk('cms')->singleFile()->acceptsFile(fn (File $file) => str_ends_with(strtolower($file->name), '.vtt') && in_array($file->mimeType, ['text/vtt', 'text/plain'], true) && $file->size <= 1024 * 1024);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && str_starts_with($media->mime_type ?? '', 'image/')) {
            $this->addMediaConversion('thumb')->fit(Fit::Max, config('cms-media.thumbnail_size'), config('cms-media.thumbnail_size'))->quality(config('cms-media.thumbnail_quality'))->format('webp')->nonQueued();
        }
    }

    protected static function booted(): void
    {
        static::saving(function (self $media): void {
            if (Auth::check()) {
                $media->setCustomProperty('updatedBy', Auth::id());
            }
        });
    }

    public function isPublic(): bool
    {
        $owner = $this->model;
        if ($owner instanceof self) {
            return $owner->isPublic();
        }
        if ($owner instanceof SiteSetting) {
            return $owner->key === 'site' && in_array($this->collection_name, ['logo', 'footer-logo', 'favicon', 'social-image'], true);
        }
        if ($owner instanceof SeoSetting) {
            $slots = app(PageContentSchema::class)->imageSlots($owner->path);
            $slot = $slots[substr($this->collection_name, strlen('page-'))] ?? null;

            return str_starts_with($this->collection_name, 'page-') && $slot
                && filled(data_get($owner->content, $slot['alt'], data_get(app(PageContentSchema::class)->sources(), $slot['alt'])));
        }
        if (! $owner instanceof ContentEntry || ! array_key_exists($owner->collection, CmsSchema::COLLECTIONS) || $owner->trashed() || $owner->status !== 'published') {
            return false;
        }
        if ($this->collection_name === 'gallery') {
            return $owner->collection === 'galleries' && $owner->is_active && $this->getCustomProperty('visible', false) && $this->getCustomProperty('consentApproved', false) && filled($this->getCustomProperty('caption')) && (! str_starts_with($this->mime_type, 'image/') || filled($this->getCustomProperty('alt')));
        }
        if ($owner->collection === 'galleries' && ! $owner->is_active) {
            return false;
        }
        if ($owner->collection === 'people' && ! data_get($owner->data, 'portraitConsent', false)) {
            return false;
        }
        if ($owner->collection === 'partners' && ! data_get($owner->data, 'mediaRightsApproved', false)) {
            return false;
        }

        return true;
    }
}
