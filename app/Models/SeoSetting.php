<?php

namespace App\Models;

use App\Support\CmsImageOptimizer;
use App\Support\PageContentSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SeoSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['path', 'title', 'description', 'site_url', 'canonical_url', 'social_image', 'indexing_enabled', 'indexable', 'content'];

    protected function casts(): array
    {
        return ['indexing_enabled' => 'boolean', 'indexable' => 'boolean', 'content' => 'array'];
    }

    public static function pageLabels(): array
    {
        $labels = ['global' => 'Site-wide SEO'];
        foreach (config('website.pages') as $key => $page) {
            $labels[$page['uri']] = $key === 'home' ? 'Home' : $page['title'];
        }
        foreach (config('website.policies') as $slug => $page) {
            $labels['/'.$slug] = $page['title'];
        }

        return $labels;
    }

    protected static function booted(): void
    {
        static::saving(function (self $setting): void {
            app(PageContentSchema::class)->validate($setting->path, $setting->content ?? []);
            Validator::make($setting->attributesToArray(), [
                'path' => ['required', Rule::in(array_keys(self::pageLabels())), Rule::unique('seo_settings')->ignore($setting->id)],
                'title' => 'nullable|string|max:255', 'description' => 'nullable|string|max:500',
                'site_url' => 'nullable|url:https|max:2048', 'canonical_url' => 'nullable|url:https|max:2048',
                'indexing_enabled' => 'nullable|boolean', 'indexable' => 'boolean',
                'social_image' => ['nullable', 'string', 'max:2048', function ($attribute, $value, $fail): void {
                    if (! str_starts_with($value, 'https://') && ! (str_starts_with($value, '/') && ! str_starts_with($value, '//'))) {
                        $fail('Use an HTTPS image URL or a local path beginning with /.');
                    }
                    if (str_starts_with($value, 'https://') && ! filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('Enter a valid HTTPS image URL.');
                    }
                }],
            ])->validate();
            if ($setting->exists && $setting->isDirty('path')) {
                throw ValidationException::withMessages(['path' => 'The page address is fixed.']);
            }
            // A page SEO record cannot change site-wide settings, even via a crafted form.
            if ($setting->path !== 'global') {
                $setting->site_url = null;
                $setting->indexing_enabled = null;
            }
            if (Auth::check()) {
                $setting->updated_by = Auth::id();
            }
        });
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function registerMediaCollections(): void
    {
        foreach (array_keys(app(PageContentSchema::class)->imageSlots($this->path ?? '')) as $slot) {
            $this->addMediaCollection('page-'.$slot)->useDisk('cms')->singleFile()
                ->acceptsFile(fn (File $file) => in_array($file->mimeType, CmsImageOptimizer::MIME_TYPES, true) && $file->size <= config('cms-media.max_upload_bytes'));
        }
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if ($media && in_array($media->mime_type, CmsImageOptimizer::MIME_TYPES, true)) {
            $this->addMediaConversion('thumb')->fit(Fit::Max, config('cms-media.thumbnail_size'), config('cms-media.thumbnail_size'))
                ->quality(config('cms-media.thumbnail_quality'))->format('webp')->nonQueued();
        }
    }
}
