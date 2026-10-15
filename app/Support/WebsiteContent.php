<?php

namespace App\Support;

use App\Models\CmsMedia;
use App\Models\ContentEntry;
use App\Models\SeoSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WebsiteContent
{
    private array $files = [];

    public function read(string $name): array
    {
        if (isset(CmsSchema::COLLECTIONS[$name])) {
            return $this->published($name);
        }
        if ($name === 'impact') {
            return ['metrics' => $this->published('metrics'), 'reports' => $this->published('reports')];
        }
        if (! in_array($name, ['site', 'pages', 'interface', 'navigation', 'routes', 'media', 'redirects', 'policies'], true)) {
            throw new InvalidArgumentException('Unknown website content collection.');
        }
        if (isset($this->files[$name])) {
            return $this->files[$name];
        }
        $result = $name === 'site' ? app(WebsiteSettings::class)->site() : CmsSchema::seed($name);
        if ($name === 'site' && ($global = $this->seoSettings()['global'] ?? null)) {
            $result['seo']['url'] = $global->site_url ?: $result['seo']['url'];
            $result['seo']['description'] = $global->description ?: $result['seo']['description'];
            $result['seo']['socialImage'] = $global->social_image ?: $result['seo']['socialImage'];
            $result['seo']['editorialApproved'] = $global->indexing_enabled ?? $result['seo']['editorialApproved'];
        }
        if ($name === 'media') {
            foreach ($result as &$photo) {
                foreach (['src', 'smallSrc'] as $key) {
                    if (isset($photo[$key]) && str_starts_with($photo[$key], '/')) {
                        $photo[$key] = asset(ltrim($photo[$key], '/'));
                    }
                }
            }
            unset($photo);
        }

        return $this->files[$name] = $result;
    }

    private function seoSettings(): array
    {
        return $this->files['seo-settings'] ??= SeoSetting::all()->keyBy('path')->all();
    }

    public function published(string $name): array
    {
        if ($name === 'policies') {
            return array_values(array_filter($this->read('policies'), fn (array $entry) => ($entry['isPublished'] ?? false) && ($entry['publicationState'] ?? '') === 'approved'));
        }
        if (! isset(CmsSchema::COLLECTIONS[$name])) {
            throw new InvalidArgumentException('Unknown collection.');
        }
        $entries = ContentEntry::published()->where('collection', $name)->with('media')->orderBy('sort_order')->orderBy('title')->get();
        if ($name === 'galleries') {
            $entries = $entries->where('is_active', true);
        }
        if ($name === 'metrics') {
            $entries = $entries->filter(fn ($entry) => data_get($entry->data, 'verified') && filled(data_get($entry->data, 'source')) && filled(data_get($entry->data, 'period')));
        }
        if ($name === 'reports') {
            $entries = $entries->filter(fn ($entry) => $entry->hasMedia('document'));
        }

        return $entries->map(fn ($entry) => $this->serialize($entry))->values()->all();
    }

    private function serialize(ContentEntry $entry): array
    {
        $data = Arr::only($entry->data ?? [], [...CmsSchema::FIELDS[$entry->collection], 'seoTitle', 'seoDescription']);
        $result = [...$data, 'id' => $entry->id, 'slug' => $entry->slug, 'title' => $entry->title, 'summary' => $entry->summary ?? '', 'publicationState' => 'approved', 'isPublished' => true];
        if (isset(config('website.details')[$entry->collection])) {
            $result['detailUrl'] = $this->detailPath($entry->collection, $entry->slug);
        }
        if ($entry->collection === 'projects') {
            $result['donationUrl'] = route('donations.project', ['projectSlug' => $entry->slug], absolute: false);
        }
        $cover = $entry->getFirstMedia('cover');
        if ($cover && $cover->isPublic()) {
            $result['coverImage'] = $cover->getUrl();
            $result['coverAlt'] = ($result['coverAlt'] ?? '') ?: $cover->getCustomProperty('alt', $entry->title);
            if ($entry->collection === 'partners') {
                $result['logo'] = $cover->getUrl();
            }
        }
        if ($entry->collection === 'reports' && ($document = $entry->getFirstMedia('document'))) {
            $result['path'] = $document->getUrl();
            $result['size'] = round($document->size / 1024).' KB';
        }
        if ($entry->collection === 'people') {
            if (filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
                $result['email'] = $data['email'];
            } else {
                unset($result['email']);
            }
            $socials = is_array($data['socialLinks'] ?? null) ? $data['socialLinks'] : [];
            $socials = array_filter(Arr::only($socials, array_keys(CmsSchema::PEOPLE_SOCIAL_PLATFORMS)), fn ($url) => is_string($url) && filter_var($url, FILTER_VALIDATE_URL) && parse_url($url, PHP_URL_SCHEME) === 'https');
            if ($socials) {
                $result['socialLinks'] = $socials;
            } else {
                unset($result['socialLinks']);
            }
        }
        if ($entry->collection === 'projects') {
            $result['programmeIds'] ??= [];
            $result['intendedActivities'] ??= [];
            if (is_numeric($data['fundingTarget'] ?? null) && (float) $data['fundingTarget'] > 0) {
                $result['fundingTarget'] = (float) $data['fundingTarget'];
                $result['fundsRaised'] = (float) ($data['fundsRaised'] ?? 0);
                $result['fundingCurrency'] = $data['fundingCurrency'] ?? 'NGN';
            } else {
                unset($result['fundingTarget'], $result['fundsRaised'], $result['fundingCurrency']);
            }
            $result['reportPaths'] = ContentEntry::published()->where('collection', 'reports')->where(fn ($query) => $query->whereIn('id', $data['reportIds'] ?? [])->orWhere('data->projectId', $entry->id))->with('media')->get()->filter(fn ($report) => $report->hasMedia('document'))->map(fn ($report) => $report->getFirstMediaUrl('document'))->values()->all();
        }
        if ($entry->collection === 'programmes') {
            $design = collect(CmsSchema::seed('programmes'))->firstWhere('id', $entry->id) ?? [];
            $result['photoId'] ??= $design['photoId'] ?? null;
            foreach (['icon' => 'heart', 'illustration' => 'education', 'number' => ''] as $key => $fallback) {
                $result[$key] = $design[$key] ?? $fallback;
            }
            $result += ['sections' => [], 'activities' => [], 'relatedProjectIds' => [], 'number' => '', 'icon' => 'heart', 'illustration' => 'education', 'originalThematicName' => $entry->title];
        }
        if ($entry->collection === 'stories') {
            $result['blocks'] = array_map(fn ($block) => ['type' => $block['type'], ...($block['data'] ?? Arr::except($block, ['type']))], $data['blocks'] ?? []);
        }
        if ($entry->collection === 'metrics') {
            $result['value'] = (float) ($data['value'] ?? 0);
        }
        if ($entry->collection === 'events') {
            $result['sections'] ??= [];
            $result['programmeIds'] ??= [];
            foreach (['startDate', 'endDate'] as $key) {
                if (! empty($result[$key])) {
                    $result[$key] = Carbon::parse($result[$key], 'UTC')->toIso8601String();
                }
            }
        }
        if ($entry->collection === 'galleries') {
            $result += ['isActive' => $entry->is_active, 'media' => []];
        }

        return $result;
    }

    public function find(string $name, string $slug): ?array
    {
        foreach ($this->published($name) as $entry) {
            if ($entry['slug'] === $slug) {
                return $entry;
            }
        }

        return null;
    }

    public function redirectedSlug(string $collection, string $slug): ?string
    {
        $id = DB::table('content_redirects')->where('collection', $collection)->where('slug', $slug)->value('entry_id');

        return $id ? ContentEntry::published()->whereKey($id)->value('slug') : null;
    }

    public function detailPath(string $collection, string $slug): string
    {
        return route($collection.'.show', ['slug' => $slug], absolute: false);
    }

    public function payload(Request $request): array
    {
        $payload = [
            'now' => (int) floor(microtime(true) * 1000),
            'site' => $this->read('site'), 'navigation' => $this->read('navigation'), 'routes' => $this->read('routes'),
            'pages' => $this->read('pages'), 'ui' => $this->read('interface'), 'media' => $this->read('media'),
        ];
        foreach (['programmes', 'projects', 'stories', 'events', 'people', 'partners', 'policies', 'faq', 'metrics', 'reports', 'galleries'] as $name) {
            $payload[$name] = [];
        }
        // Programmes feed the navigation and related-content links; FAQs feed shared sections.
        $collections = ['programmes', 'faq'];
        $page = $request->route('pageKey') ?? $request->route('collection') ?? ($request->route('policySlug') ? 'policies' : '');
        $dependencies = [
            'home' => ['projects', 'stories', 'events', 'metrics', 'partners'],
            'projects' => ['projects', 'stories'], 'programmes' => ['projects'],
            'reports' => ['reports', 'metrics', 'projects'], 'stories' => ['stories'], 'events' => ['events'],
            'team' => ['people'], 'people' => ['people'], 'partner' => ['partners'], 'policies' => ['policies'], 'gallery' => ['galleries'],
        ];
        if ($request->route('projectSlug')) {
            $collections[] = 'projects';
        }
        foreach (array_unique([...$collections, ...($dependencies[$page] ?? [])]) as $name) {
            $payload[$name] = $this->published($name);
        }
        $payload['selectedGallery'] = null;
        if ($page === 'gallery' && count($payload['galleries'])) {
            $requested = $request->query('gallery');
            $requested = is_string($requested) ? $requested : '';
            $gallery = collect($payload['galleries'])->firstWhere('slug', $requested)
                ?? collect($payload['galleries'])->firstWhere('slug', $this->redirectedSlug('galleries', $requested))
                ?? $payload['galleries'][0];
            $entry = ContentEntry::findOrFail($gallery['id']);
            $gallery['media'] = $entry->getMedia('gallery')->filter(fn (CmsMedia $media) => $media->isPublic())->map(function (CmsMedia $media): array {
                $item = ['id' => $media->uuid, 'type' => str_starts_with($media->mime_type, 'image/') ? 'image' : 'video', 'src' => $media->getUrl(), 'caption' => $media->getCustomProperty('caption', ''), 'alt' => $media->getCustomProperty('alt', ''), 'credit' => $media->getCustomProperty('credit', ''), 'mimeType' => $media->mime_type];
                if ($media->hasGeneratedConversion('thumb')) {
                    $item['thumbnail'] = $media->getUrl('thumb');
                }
                if ($media->hasMedia('poster')) {
                    $item['poster'] = $media->getFirstMediaUrl('poster');
                }
                if ($media->hasMedia('captions')) {
                    $item['captions'] = $media->getFirstMediaUrl('captions');
                }
                $item['transcript'] = $media->getCustomProperty('transcript', '');

                return $item;
            })->values()->all();
            $payload['selectedGallery'] = $gallery;
        }

        return $payload;
    }

    public function metadata(string $path, string $title, ?string $description = null, bool $indexable = true, ?array $article = null, ?array $person = null): array
    {
        $site = $this->read('site');
        $settings = $this->seoSettings();
        $page = $settings[$path] ?? null;
        $base = rtrim($site['seo']['url'], '/');
        $canonical = $page?->canonical_url ?: WebsiteUrls::absolute($path, $base);
        $approved = ($site['seo']['editorialApproved'] ?? false) && $indexable && ($page?->indexable ?? true);
        $title = $page?->title ?: $title;
        $description = $page?->description ?: ($description ?: $site['seo']['description']);
        // A compact title suffix keeps browser/search titles readable; identity uses the full name.
        $suffix = ($settings['global']->title ?? null) ?: $site['brand']['shortName'];
        $image = $page?->social_image ?: ($person['image'] ?? $site['seo']['socialImage']);
        $image = WebsiteUrls::absolute($image, $base);
        if ($person && isset($person['image'])) {
            $person['image'] = WebsiteUrls::absolute($person['image'], $base);
        }
        $structuredData = match (true) {
            $person !== null => ['@type' => 'ProfilePage', 'url' => $canonical, 'mainEntity' => [
                '@type' => 'Person', '@id' => $canonical.'#person', ...$person, 'url' => $canonical,
                'affiliation' => ['@type' => 'Organization', 'name' => $site['brand']['publicName'], 'url' => $base],
            ]],
            $article !== null => ['@type' => 'Article', ...$article, 'url' => $canonical],
            default => ['@type' => 'Organization', 'name' => $site['brand']['publicName'], 'alternateName' => [$site['brand']['shortName'], 'Jedidiah Cares Global Outreach'], 'url' => $base, 'logo' => WebsiteUrls::absolute($site['brand']['logoPath'], $base),
                ...array_filter(['email' => $site['contact']['email'], 'telephone' => $site['contact']['phone'], 'address' => $site['contact']['address']]),
                'sameAs' => array_values(array_filter($site['social'], fn ($url) => is_string($url) && filter_var($url, FILTER_VALIDATE_URL))),
            ],
        };

        return [
            'title' => $title.' | '.$suffix, 'description' => $description, 'canonical' => $canonical,
            'robots' => $approved ? 'index, follow' : 'noindex, nofollow', 'type' => $article ? 'article' : 'website',
            'image' => $image, 'structuredData' => $approved ? ['@context' => 'https://schema.org', ...$structuredData] : null,
        ];
    }

    public function sitemapUrls(): array
    {
        $site = $this->read('site');
        if (! ($site['seo']['editorialApproved'] ?? false)) {
            return [];
        }

        $paths = [];
        foreach (config('website.pages') as $name => $page) {
            $paths[$page['uri']] = route($name, absolute: false);
        }
        foreach (array_keys(config('website.details')) as $collection) {
            foreach ($this->published($collection) as $entry) {
                $path = $this->detailPath($collection, $entry['slug']);
                $paths[$path] = $path;
            }
        }
        foreach (array_keys(config('website.policies')) as $slug) {
            if ($this->find('policies', $slug)) {
                $paths['/'.$slug] = route($slug, absolute: false);
            }
        }

        $settings = $this->seoSettings();
        $base = rtrim($site['seo']['url'], '/');
        $urls = [];
        foreach ($paths as $settingsPath => $path) {
            if (($settings[$settingsPath]->indexable ?? true) === false) {
                continue;
            }
            $url = ($settings[$settingsPath]->canonical_url ?? null) ?: WebsiteUrls::absolute($path, $base);
            if (parse_url($url, PHP_URL_HOST) === parse_url($base, PHP_URL_HOST)) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }
}
