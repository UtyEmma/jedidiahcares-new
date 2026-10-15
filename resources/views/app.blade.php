<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#072266">
        <link rel="icon" href="{{ asset('icon.webp') }}" type="image/webp">
        @viteReactRefresh
        @vite('resources/js/app.tsx')
        <x-inertia::head>
            <title>{{ $seo['title'] }}</title>
            <meta data-inertia="description" name="description" content="{{ $seo['description'] }}">
            <meta data-inertia="robots" name="robots" content="{{ $seo['robots'] }}">
            <link data-inertia="canonical" rel="canonical" href="{{ $seo['canonical'] }}">
            <meta data-inertia="og-title" property="og:title" content="{{ $seo['title'] }}">
            <meta data-inertia="og-description" property="og:description" content="{{ $seo['description'] }}">
            <meta data-inertia="og-url" property="og:url" content="{{ $seo['canonical'] }}">
            <meta data-inertia="og-type" property="og:type" content="{{ $seo['type'] }}">
            <meta data-inertia="og-image" property="og:image" content="{{ $seo['image'] }}">
            <meta data-inertia="twitter-card" name="twitter:card" content="summary_large_image">
            <meta data-inertia="twitter-title" name="twitter:title" content="{{ $seo['title'] }}">
            <meta data-inertia="twitter-description" name="twitter:description" content="{{ $seo['description'] }}">
            <meta data-inertia="twitter-image" name="twitter:image" content="{{ $seo['image'] }}">
            @if ($seo['structuredData'])
                <script data-inertia="structured-data" type="application/ld+json">{!! json_encode($seo['structuredData'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) !!}</script>
            @endif
        </x-inertia::head>
    </head>
    <body>
        <x-inertia::app />
    </body>
</html>
