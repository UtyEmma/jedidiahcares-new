<?php

namespace App\Providers;

use App\Models\CmsMedia;
use App\Models\ContentEntry;
use App\Models\SeoSetting;
use App\Models\User;
use App\Policies\CmsMediaPolicy;
use App\Policies\ContentEntryPolicy;
use App\Policies\SeoSettingPolicy;
use App\Policies\UserPolicy;
use BladeUI\Icons\Factory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        app(Factory::class)->add('ph', ['path' => resource_path('icons'), 'prefix' => 'ph']);
        Gate::policy(ContentEntry::class, ContentEntryPolicy::class);
        Gate::policy(CmsMedia::class, CmsMediaPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
    }
}
