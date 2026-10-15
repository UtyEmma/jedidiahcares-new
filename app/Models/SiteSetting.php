<?php

namespace App\Models;

use App\Support\CmsImageOptimizer;
use App\Support\WebsiteSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SiteSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['key', 'values', 'header_scripts', 'footer_scripts', 'robots_txt', 'scripts_enabled'];

    protected $hidden = ['header_scripts', 'footer_scripts'];

    protected function casts(): array
    {
        return ['values' => 'array', 'scripts_enabled' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $setting): void {
            Validator::make([
                ...$setting->attributesToArray(),
                'header_scripts' => $setting->header_scripts,
                'footer_scripts' => $setting->footer_scripts,
            ], [
                'key' => ['required', Rule::in(['site']), Rule::unique('site_settings')->ignore($setting->id)],
                'values' => ['required', 'array:'.implode(',', array_keys(WebsiteSettings::defaults()))],
                ...WebsiteSettings::rules('values.'),
                'header_scripts' => 'nullable|string|max:100000',
                'footer_scripts' => 'nullable|string|max:100000',
                'robots_txt' => ['nullable', 'string', 'max:100000', 'not_regex:/[\x00-\x08\x0B\x0C\x0E-\x1F]/'],
                'scripts_enabled' => 'required|boolean',
            ])->validate();
            if (Auth::check()) {
                abort_unless(Auth::user()->canAccessCms() && Auth::user()->role === 'admin', 403);
                $setting->updated_by = Auth::id();
            }
        });
    }

    public function registerMediaCollections(): void
    {
        foreach (['logo', 'footer-logo', 'favicon', 'social-image'] as $name) {
            $this->addMediaCollection($name)->useDisk('cms')->singleFile()
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
