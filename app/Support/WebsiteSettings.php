<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;

class WebsiteSettings
{
    public const SOCIAL_PLATFORMS = ['facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'x' => 'X', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'];

    public static function defaults(): array
    {
        $site = CmsSchema::seed('site');

        return [
            'site_title' => $site['brand']['publicName'],
            'short_name' => $site['brand']['shortName'],
            'tagline' => $site['brand']['tagline'],
            'site_description' => $site['seo']['description'],
            'primary_color' => '#072266', 'accent_color' => '#F0EB6F',
            'contact_email' => $site['contact']['email'], 'contact_phone' => $site['contact']['phone'],
            'contact_address' => $site['contact']['address'],
            ...collect(self::SOCIAL_PLATFORMS)->mapWithKeys(fn ($label, $key) => ['social_'.$key => $site['social'][$key] ?? ''])->all(),
        ];
    }

    public static function rules(string $prefix = ''): array
    {
        return [
            $prefix.'site_title' => 'required|string|max:255', $prefix.'short_name' => 'required|string|max:100',
            $prefix.'tagline' => 'nullable|string|max:300', $prefix.'site_description' => 'required|string|max:500',
            $prefix.'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            $prefix.'accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            $prefix.'contact_email' => 'nullable|email:rfc|max:254',
            $prefix.'contact_phone' => ['nullable', 'string', 'max:50', 'regex:/^[+0-9() .-]+$/'],
            $prefix.'contact_address' => 'nullable|string|max:1000',
            ...collect(self::SOCIAL_PLATFORMS)->mapWithKeys(fn ($label, $key) => [$prefix.'social_'.$key => 'nullable|url:https|max:2048'])->all(),
        ];
    }

    public static function defaultRobots(): string
    {
        return "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /livewire\nDisallow: /fluppy\nDisallow: /up\n\nSitemap: {site_url}/sitemap.xml\n";
    }

    public function record(): ?SiteSetting
    {
        // Setup, migrations and a first deployment can boot before this table exists.
        return Schema::hasTable('site_settings') ? SiteSetting::with('media')->where('key', 'site')->first() : null;
    }

    public function forEditing(): SiteSetting
    {
        return SiteSetting::firstOrCreate(['key' => 'site'], [
            'values' => self::defaults(), 'robots_txt' => self::defaultRobots(), 'scripts_enabled' => true,
        ]);
    }

    public function site(): array
    {
        $site = CmsSchema::seed('site');
        $record = $this->record();
        $values = array_replace(self::defaults(), $record?->values ?? []);
        $site['footer']['text'] = trim(str_replace($site['brand']['tagline'], $values['tagline'] ?? '', $site['footer']['text']));
        $site['brand'] = array_replace($site['brand'], [
            'publicName' => $values['site_title'], 'wordmarkName' => $values['site_title'],
            'shortName' => $values['short_name'], 'tagline' => $values['tagline'] ?? '',
            'primaryColor' => $values['primary_color'], 'accentColor' => $values['accent_color'],
        ]);
        foreach (['logo' => 'logoPath', 'footer-logo' => 'footerLogoPath', 'favicon' => 'faviconPath'] as $collection => $key) {
            $site['brand'][$key] = $record?->getFirstMediaUrl($collection) ?: ($site['brand'][$key] ?? $site['brand']['logoPath']);
        }
        $site['seo']['description'] = $values['site_description'];
        $site['seo']['socialImage'] = $record?->getFirstMediaUrl('social-image') ?: $site['seo']['socialImage'];
        $site['seo']['editorialApproved'] = true;
        foreach (['email' => 'emailVerified', 'phone' => 'phoneVerified', 'address' => 'locationVerified'] as $key => $verified) {
            $site['contact'][$key] = $values['contact_'.$key] ?? '';
            $site['contact'][$verified] = filled($site['contact'][$key]);
        }
        foreach (array_keys(self::SOCIAL_PLATFORMS) as $key) {
            $site['social'][$key] = $values['social_'.$key] ?? '';
        }

        return $site;
    }

    public function scripts(): array
    {
        $record = $this->record();

        return $record?->scripts_enabled ? ['header' => $record->header_scripts ?? '', 'footer' => $record->footer_scripts ?? ''] : ['header' => '', 'footer' => ''];
    }

    public function robots(string $siteUrl, bool $indexingEnabled): string
    {
        $body = $this->record()?->robots_txt ?: self::defaultRobots();
        $body = str_replace('{site_url}', rtrim($siteUrl, '/'), str_replace(["\r\n", "\r"], "\n", $body));
        if (! $indexingEnabled) {
            return "User-agent: *\nDisallow: /\n";
        }

        return rtrim($body)."\n";
    }
}
