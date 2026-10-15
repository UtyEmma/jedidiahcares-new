<?php

use App\Http\Controllers\LegacyRedirectController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\WebsitePageController;
use App\Support\CmsSchema;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/media/{media}/{conversion?}', MediaController::class)->name('cms.media');

foreach (config('website.pages') as $key => $page) {
    Route::get($page['uri'], [WebsitePageController::class, 'index'])
        ->defaults('pageKey', $key)->name($key);
}

foreach (config('website.details') as $collection => $component) {
    $prefix = config("website.detail_paths.{$collection}", $collection);
    Route::get("/{$prefix}/{slug}", [WebsitePageController::class, 'detail'])
        ->defaults('collection', $collection)->name("{$collection}.show");
}

Route::get('/donations/{projectSlug}', [WebsitePageController::class, 'projectDonation'])->name('donations.project');

foreach (config('website.policies') as $slug => $policy) {
    Route::get('/'.$slug, [WebsitePageController::class, 'policy'])
        ->defaults('policySlug', $slug)->name($slug);
}

foreach (CmsSchema::seed('redirects') as $index => $redirect) {
    Route::get(str_replace(':slug', '{slug}', $redirect['from']), LegacyRedirectController::class)
        ->defaults('redirectRoute', $redirect['route'])->name('legacy.'.$index);
}

Route::fallback([WebsitePageController::class, 'notFound']);
