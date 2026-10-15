<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SeoSetting extends Model
{
    protected $fillable = ['path', 'title', 'description', 'site_url', 'canonical_url', 'social_image', 'indexing_enabled', 'indexable'];

    protected function casts(): array
    {
        return ['indexing_enabled' => 'boolean', 'indexable' => 'boolean'];
    }

    public static function pageLabels(): array
    {
        $labels = ['global' => 'Site-wide SEO'];
        foreach (config('website.pages') as $page) {
            $labels[$page['uri']] = $page['title'];
        }
        foreach (config('website.policies') as $slug => $page) {
            $labels['/'.$slug] = $page['title'];
        }

        return $labels;
    }

    protected static function booted(): void
    {
        static::saving(function (self $setting): void {
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
}
