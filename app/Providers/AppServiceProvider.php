<?php

namespace App\Providers;

use App\Models\CmsMedia;
use App\Models\ContentEntry;
use App\Models\SeoSetting;
use App\Models\SiteSetting;
use App\Models\User;
use App\Policies\CmsMediaPolicy;
use App\Policies\ContentEntryPolicy;
use App\Policies\SeoSettingPolicy;
use App\Policies\SiteSettingPolicy;
use App\Policies\UserPolicy;
use App\Support\CmsImageOptimizer;
use BladeUI\Icons\Factory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(SeoSetting::class, SeoSettingPolicy::class);
        Gate::policy(SiteSetting::class, SiteSettingPolicy::class);
        app(Factory::class)->add('ph', ['path' => resource_path('icons'), 'prefix' => 'ph']);
        Gate::policy(ContentEntry::class, ContentEntryPolicy::class);
        Gate::policy(CmsMedia::class, CmsMediaPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        // Fires after local/chunked uploads are copied, before thumbnail generation.
        Event::listen(MediaHasBeenAddedEvent::class, function (MediaHasBeenAddedEvent $event): void {
            $media = $event->media;
            if (! $media instanceof CmsMedia || ! in_array($media->mime_type, CmsImageOptimizer::MIME_TYPES, true)) {
                return;
            }
            try {
                app(CmsImageOptimizer::class)->optimize($media, backup: false, regenerateThumbnail: false);
            } catch (Throwable $exception) {
                Log::warning('CMS image optimization failed.', ['media_uuid' => $media->uuid, 'exception' => $exception]);
                $field = match ($media->collection_name) {
                    'poster' => 'posterUpload', 'gallery' => 'photoUpload',
                    'logo' => 'logoUpload', 'footer-logo' => 'footerLogoUpload', 'favicon' => 'faviconUpload', 'social-image' => 'socialImageUpload',
                    default => 'coverUpload'
                };
                $media->forceDelete();
                throw ValidationException::withMessages(['data.'.$field => 'This image could not be processed. Use a valid JPEG, PNG or WebP up to 10 MB with smaller image dimensions.']);
            }
        });
    }
}
