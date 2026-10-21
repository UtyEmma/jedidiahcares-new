<?php

namespace App\Http\Controllers;

use App\Support\PageContentSchema;
use App\Support\WebsiteContent;
use App\Support\WebsiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class WebsitePageController extends Controller
{
    public function __construct(private WebsiteContent $content)
    {
        $this->content->forPage(PageContentSchema::requestPath(request()));
    }

    public function index(Request $request): Response
    {
        $page = config('website.pages')[$request->route('pageKey')];
        [$collection, $field] = $page['description'];
        $description = data_get($this->content->read($collection), $field);

        $copyKey = $request->route('pageKey') === 'reports' ? 'impact' : $request->route('pageKey');

        return $this->render($request, $page['component'], data_get($this->content->read('pages'), $copyKey.'.title', $page['title']), $description);
    }

    public function detail(Request $request, string $slug): Response
    {
        $collection = $request->route('collection');
        $entry = $this->content->find($collection, $slug);
        if (! $entry) {
            if ($target = $this->content->redirectedSlug($collection, $slug)) {
                return redirect($this->content->detailPath($collection, $target), 301);
            }

            return $this->notFound($request);
        }

        $article = $collection === 'stories'
            ? ['headline' => $entry['title'], 'datePublished' => $entry['publishedAt']]
            : null;

        $description = ($entry['seoDescription'] ?? '') ?: $entry['summary'];
        $person = null;
        if ($collection === 'people') {
            $description = ($entry['seoDescription'] ?? '') ?: Str::limit(Str::squish($entry['biography'] ?: $entry['summary']), 160);
            $person = ['name' => $entry['title'], 'jobTitle' => $entry['role'], 'description' => $description];
            if (! empty($entry['email'])) {
                $person['email'] = $entry['email'];
            }
            if (! empty($entry['socialLinks'])) {
                $person['sameAs'] = array_values($entry['socialLinks']);
            }
            if (! empty($entry['coverImage'])) {
                $person['image'] = parse_url($entry['coverImage'], PHP_URL_PATH);
            }
        }

        return $this->render($request, config('website.details')[$collection], ($entry['seoTitle'] ?? '') ?: $entry['title'], $description, ['slug' => $slug], article: $article, person: $person);
    }

    public function projectDonation(Request $request, string $projectSlug): Response
    {
        if (! $this->content->find('projects', $projectSlug)) {
            return $this->notFound($request);
        }

        return $this->render($request, 'project-donation/ProjectDonationPage', 'Support our work', data_get($this->content->read('interface'), 'support.intro'), ['projectSlug' => $projectSlug], indexable: false);
    }

    public function policy(Request $request): Response
    {
        $slug = $request->route('policySlug');
        $definition = config('website.policies')[$slug];
        $policy = $this->content->find('policies', $slug);
        $description = $policy['summary'] ?? data_get($this->content->read('interface'), "policy.pages.{$slug}.intro");

        return $this->render($request, $definition['component'], $policy['title'] ?? $definition['title'], $description, indexable: (bool) $policy);
    }

    public function notFound(Request $request): Response
    {
        return $this->render($request, 'not-found/NotFoundPage', 'Page not found', null, indexable: false, status: 404);
    }

    private function render(Request $request, string $component, string $title, ?string $description, array $props = [], bool $indexable = true, ?array $article = null, int $status = 200, ?array $person = null): Response
    {
        $seo = $this->content->metadata($request->getPathInfo(), $title, $description, $indexable, $article, $person);
        $content = $this->content->payload($request);

        return Inertia::render($component, [...$props, 'seo' => $seo, 'content' => $content])
            ->withViewData(['seo' => $seo, 'siteBrand' => $content['site']['brand'], 'siteScripts' => app(WebsiteSettings::class)->scripts()])
            ->toResponse($request)
            ->setStatusCode($status);
    }
}
