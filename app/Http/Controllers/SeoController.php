<?php

namespace App\Http\Controllers;

use App\Support\WebsiteContent;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(WebsiteContent $content): Response
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($content->sitemapUrls() as $url) {
            $xml .= '  <url><loc>'.htmlspecialchars($url, ENT_XML1 | ENT_QUOTES, 'UTF-8')."</loc></url>\n";
        }
        $xml .= "</urlset>\n";

        return response($xml)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(WebsiteContent $content): Response
    {
        $site = $content->read('site');
        $rule = ($site['seo']['editorialApproved'] ?? false) ? 'Allow: /' : 'Disallow: /';
        $body = "User-agent: *\n{$rule}\nSitemap: ".rtrim($site['seo']['url'], '/')."/sitemap.xml\n";
        $body .= "Disallow: /admin\nDisallow: /media/\n";

        return response($body)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
