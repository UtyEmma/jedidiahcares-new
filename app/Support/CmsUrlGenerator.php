<?php

namespace App\Support;

use DateTimeInterface;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class CmsUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        return route('cms.media', ['media' => $this->media->uuid, 'conversion' => $this->conversion?->getName() ?? 'original']);
    }

    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        // Every request checks current publication and staff access, including previews.
        return $this->getUrl();
    }
}
