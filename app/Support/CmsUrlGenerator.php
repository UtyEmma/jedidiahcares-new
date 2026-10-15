<?php

namespace App\Support;

use DateTimeInterface;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class CmsUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        $publicUrl = app(CmsPublicImages::class)->url($this->media, $this->conversion?->getName() ?? '');
        if ($publicUrl) {
            return $publicUrl;
        }

        return route('cms.media', ['media' => $this->media->uuid, 'conversion' => $this->conversion?->getName() ?? 'original']);
    }

    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        // Drafts retain authenticated previews; published images use saved public files.
        return $this->getUrl();
    }
}
