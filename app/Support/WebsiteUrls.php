<?php

namespace App\Support;

class WebsiteUrls
{
    /** Resolve paths against the canonical site URL, retaining absolute asset/CDN URLs. */
    public static function absolute(string $value, string $base): string
    {
        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        $base = rtrim($base, '/');
        if (str_starts_with($value, '//')) {
            return (parse_url($base, PHP_URL_SCHEME) ?: 'https').':'.$value;
        }

        $path = '/'.ltrim($value, '/');
        $basePath = rtrim(parse_url($base, PHP_URL_PATH) ?? '', '/');
        // Relative named routes may already contain the application's installation path.
        if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath.'/'))) {
            $base = substr($base, 0, -strlen($basePath));
        }

        return $base.$path;
    }
}
