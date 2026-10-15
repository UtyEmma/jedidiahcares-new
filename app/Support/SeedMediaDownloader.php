<?php

namespace App\Support;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

/** Downloads only the organisation's public media; cached files are never public. */
class SeedMediaDownloader
{
    private const HOSTS = ['jedidiahcares.org', 'www.jedidiahcares.org', 'admin.jedidiahcares.org'];

    public function download(string $url, string $collection): string
    {
        $this->validateUrl($url);
        $directory = storage_path('app/private/cms-seed-cache');
        File::ensureDirectoryExists($directory, 0700);
        $cached = $directory.'/'.hash('sha256', $url);
        $limit = match ($collection) {
            'document' => 20 * 1024 * 1024,
            'gallery' => 100 * 1024 * 1024,
            default => 10 * 1024 * 1024,
        };
        if (is_file($cached)) {
            try {
                $this->validateFile($cached, $collection);

                return $cached;
            } catch (RuntimeException) {
                unlink($cached);
            }
        }

        $temporary = tempnam($directory, 'download-');
        try {
            $current = $url;
            for ($redirects = 0; $redirects <= 3; $redirects++) {
                $this->validateUrl($current);
                $response = Http::connectTimeout(10)->timeout(60)->withOptions([
                    'allow_redirects' => false,
                    'sink' => $temporary,
                    'on_headers' => function (ResponseInterface $response) use ($limit): void {
                        if ((int) $response->getHeaderLine('Content-Length') > $limit) {
                            throw new RuntimeException('The remote file exceeds the upload size limit.');
                        }
                    },
                    'progress' => function ($total, $downloaded) use ($limit): void {
                        if ($total > $limit || $downloaded > $limit) {
                            throw new RuntimeException('The remote file exceeds the upload size limit.');
                        }
                    },
                ])->get($current);
                if ($response->redirect()) {
                    $location = $response->header('Location');
                    if (! $location || $redirects === 3) {
                        throw new RuntimeException('The remote file has an invalid redirect.');
                    }
                    $current = (string) UriResolver::resolve(new Uri($current), new Uri($location));

                    continue;
                }
                if (! $response->successful()) {
                    throw new RuntimeException('Remote server returned HTTP '.$response->status().'.');
                }
                $this->validateFile($temporary, $collection);
                File::move($temporary, $cached);
                chmod($cached, 0600);

                return $cached;
            }
        } catch (Throwable $exception) {
            // Do not log response bodies: the old website sends unrelated page settings.
            throw new RuntimeException($exception->getMessage(), previous: $exception);
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }

        throw new RuntimeException('The remote file could not be downloaded.');
    }

    public function validateFile(string $path, string $collection): void
    {
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        $size = filesize($path);
        $image = in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true);
        $valid = match ($collection) {
            'document' => $mime === 'application/pdf' && $size <= 20 * 1024 * 1024,
            'gallery' => ($image && $size <= 10 * 1024 * 1024) || (in_array($mime, ['video/mp4', 'video/webm'], true) && $size <= 100 * 1024 * 1024),
            default => $image && $size <= 10 * 1024 * 1024,
        };
        if (! $size || ! $valid || ($image && @getimagesize($path) === false)) {
            throw new RuntimeException('The file has an unsupported actual type, invalid image data or excessive size.');
        }
    }

    private function validateUrl(string $url): void
    {
        $parts = parse_url($url);
        if (! $parts || ($parts['scheme'] ?? '') !== 'https' || ! in_array(strtolower($parts['host'] ?? ''), self::HOSTS, true) || isset($parts['port']) || isset($parts['user']) || isset($parts['pass'])) {
            throw new RuntimeException('Seed media must use an HTTPS URL on the organisation’s website.');
        }
    }
}
