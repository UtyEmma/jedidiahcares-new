<?php

namespace App\Support;

use App\Models\CmsMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/** Publish already-generated image files; no transformations run during website requests. */
class CmsPublicImages
{
    public const DISK = 'cms_images';

    public function path(CmsMedia $media, string $conversion = ''): string
    {
        $directory = $this->directory($media);
        $name = Str::substr(Str::slug(pathinfo($media->file_name, PATHINFO_FILENAME)) ?: 'image', 0, 180);
        $extension = match ($media->mime_type) {
            'image/jpeg' => 'jpg', 'image/png' => 'png', default => 'webp',
        };

        return $conversion === 'thumb'
            ? $directory.'/thumbnails/'.$name.'-thumb.webp'
            : $directory.'/'.$name.'.'.$extension;
    }

    public function url(CmsMedia $media, string $conversion = ''): ?string
    {
        if (! in_array($conversion, ['', 'thumb'], true) || ! in_array($media->mime_type, CmsImageOptimizer::MIME_TYPES, true) || ! $media->isPublic()) {
            return null;
        }
        $path = $this->path($media, $conversion);
        $disk = Storage::disk(self::DISK);

        return $disk->exists($path) ? $disk->url($path) : null;
    }

    public function syncOwner(Model $owner): void
    {
        foreach ($owner->media()->get() as $media) {
            $this->syncTree($media);
        }
    }

    public function syncTree(CmsMedia $media): void
    {
        $this->sync($media);
        // A video's poster inherits the gallery/parent media's eligibility.
        foreach ($media->media()->get() as $child) {
            $this->syncTree($child);
        }
    }

    public function sync(CmsMedia $media): bool
    {
        return $this->withLock($media, function () use ($media): bool {
            // Re-read after acquiring the lock so a later hide/delete cannot be undone by a stale callback.
            $current = CmsMedia::find($media->getKey());
            $disk = Storage::disk(self::DISK);
            if (! $current || ! in_array($current->mime_type, CmsImageOptimizer::MIME_TYPES, true) || ! $current->isPublic()) {
                $disk->deleteDirectory($this->directory($media));

                return false;
            }
            if (! is_file($current->getPath())) {
                $disk->deleteDirectory($this->directory($media));
                throw new RuntimeException('The stored source image is missing for media #'.$media->id.'.');
            }
            $files = [$this->path($current) => $current->getPath()];
            if ($current->hasGeneratedConversion('thumb') && is_file($current->getPath('thumb'))) {
                $files[$this->path($current, 'thumb')] = $current->getPath('thumb');
            }
            foreach ($files as $destination => $source) {
                $this->copy($source, $disk->path($destination));
            }
            foreach (array_diff($disk->allFiles($this->directory($current)), array_keys($files)) as $obsolete) {
                $disk->delete($obsolete);
            }

            return true;
        });
    }

    public function remove(CmsMedia $media): void
    {
        $this->withLock($media, fn () => Storage::disk(self::DISK)->deleteDirectory($this->directory($media)));
    }

    private function directory(CmsMedia $media): string
    {
        if (! Str::isUuid($media->uuid)) {
            throw new RuntimeException('A valid media UUID is required for image publication.');
        }

        return $media->uuid;
    }

    private function withLock(CmsMedia $media, callable $callback): mixed
    {
        if (config('filesystems.disks.'.self::DISK.'.driver') !== 'local') {
            throw new RuntimeException('Public CMS images require a local filesystem disk.');
        }
        $directory = storage_path('framework/cms-image-locks');
        File::ensureDirectoryExists($directory, 0700);
        $lock = fopen($directory.'/'.$this->directory($media).'.lock', 'c+');
        if (! $lock) {
            throw new RuntimeException('The image publication lock could not be opened.');
        }
        try {
            if (! flock($lock, LOCK_EX)) {
                throw new RuntimeException('The image publication lock could not be acquired.');
            }

            return $callback();
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function copy(string $source, string $destination): void
    {
        if (is_file($destination) && hash_equals(hash_file('sha256', $source), hash_file('sha256', $destination))) {
            return;
        }
        File::ensureDirectoryExists(dirname($destination));
        $temporary = tempnam(dirname($destination), '.publishing-');
        try {
            if (! copy($source, $temporary) || ! hash_equals(hash_file('sha256', $source), hash_file('sha256', $temporary))) {
                throw new RuntimeException('The public image copy could not be verified.');
            }
            chmod($temporary, 0644);
            if (! rename($temporary, $destination)) {
                throw new RuntimeException('The public image file could not be stored.');
            }
        } finally {
            if (is_file($temporary)) {
                unlink($temporary);
            }
        }
    }
}
