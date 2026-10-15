<?php

namespace App\Support;

use App\Models\CmsMedia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\ImageDriver;
use Spatie\Image\Image;
use Spatie\MediaLibrary\Conversions\FileManipulator;
use Throwable;

class CmsImageOptimizer
{
    public const MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    public function policy(): string
    {
        return hash('sha256', json_encode(['version' => 1, ...config('cms-media')], JSON_THROW_ON_ERROR));
    }

    public function isCurrent(CmsMedia $media): bool
    {
        if ($media->mime_type !== 'image/webp' || $media->getCustomProperty('optimization.policy') !== $this->policy()) {
            return false;
        }
        $path = $media->getPath();

        return is_file($path) && hash_equals($media->getCustomProperty('optimization.sha256', ''), hash_file('sha256', $path));
    }

    /** Convert the stored copy; source uploads and the seed bundle are never edited. */
    public function optimize(CmsMedia $media, bool $backup = true, bool $regenerateThumbnail = true): bool
    {
        if (! in_array($media->mime_type, self::MIME_TYPES, true) || $this->isCurrent($media)) {
            return false;
        }
        if (config('filesystems.disks.'.$media->disk.'.driver') !== 'local') {
            throw new RuntimeException('CMS image optimization requires the configured local media disk.');
        }
        $source = $media->getPath();
        $mime = is_file($source) ? (new \finfo(FILEINFO_MIME_TYPE))->file($source) : false;
        $dimensions = is_file($source) ? @getimagesize($source) : false;
        if (! in_array($mime, self::MIME_TYPES, true) || ! $dimensions || filesize($source) > config('cms-media.max_upload_bytes')) {
            throw new RuntimeException('The image is missing, invalid or larger than the 10 MB upload limit.');
        }
        $pixels = $dimensions[0] * $dimensions[1];
        $requiredMemory = $pixels * 8 + filesize($source) * 2 + 24 * 1024 * 1024;
        $memoryLimit = $this->memoryLimit();
        if ($pixels > config('cms-media.max_pixels') || ($memoryLimit > 0 && $requiredMemory > $memoryLimit - memory_get_usage(true))) {
            throw new RuntimeException('This image has too many pixels to process on this server. Reduce its dimensions and upload it again.');
        }
        if (! function_exists('imagewebp') || ! (gd_info()['WebP Support'] ?? false)) {
            throw new RuntimeException('PHP GD with WebP support is required to process images.');
        }

        $temporaryDirectory = storage_path('app/private/image-optimization');
        File::ensureDirectoryExists($temporaryDirectory, 0700);
        $output = tempnam($temporaryDirectory, 'webp-');
        $rollback = null;
        $staged = null;
        $targetWritten = false;
        $before = $media->getAttributes();
        $fileName = pathinfo($media->file_name, PATHINFO_FILENAME).'.webp';
        $target = dirname($source).DIRECTORY_SEPARATOR.$fileName;
        try {
            Image::useImageDriver(ImageDriver::Gd)->loadFile($source)
                ->fit(Fit::Max, config('cms-media.max_width'), config('cms-media.max_height'))
                ->quality(config('cms-media.quality'))->format('webp')->save($output);
            clearstatcache(true, $output);
            $result = @getimagesize($output);
            if (! $result || ($result['mime'] ?? '') !== 'image/webp' || filesize($output) === 0) {
                throw new RuntimeException('The image could not be encoded as WebP.');
            }

            $originalHash = hash_file('sha256', $source);
            if ($backup) {
                $directory = storage_path('app/private/image-optimization-backups/'.$media->uuid);
                File::ensureDirectoryExists($directory, 0700);
                $rollback = $directory.'/'.$originalHash.'-'.$media->file_name;
                if (! is_file($rollback)) {
                    File::copy($source, $rollback);
                    chmod($rollback, 0600);
                    File::put($rollback.'.json', json_encode($before, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
                    chmod($rollback.'.json', 0600);
                }
            } else {
                $rollback = tempnam($temporaryDirectory, 'rollback-');
                File::copy($source, $rollback);
            }
            if (! is_file($rollback) || ! hash_equals($originalHash, hash_file('sha256', $rollback))) {
                throw new RuntimeException('The image backup could not be verified.');
            }
            $staged = tempnam(dirname($target), 'webp-');
            File::copy($output, $staged);
            chmod($staged, 0644);

            $media->getConnection()->transaction(function () use ($media, $staged, $target, $fileName, $mime, $dimensions, $result, $originalHash, $output, $regenerateThumbnail, &$targetWritten): void {
                if (! rename($staged, $target)) {
                    throw new RuntimeException('The optimized image could not be stored.');
                }
                $targetWritten = true;
                $media->file_name = $fileName;
                $media->mime_type = 'image/webp';
                $media->size = filesize($output);
                $media->setCustomProperty('optimization', [
                    'policy' => $this->policy(), 'quality' => config('cms-media.quality'),
                    'width' => $result[0], 'height' => $result[1], 'sha256' => hash_file('sha256', $output),
                    'source_mime' => $mime, 'source_width' => $dimensions[0], 'source_height' => $dimensions[1], 'source_sha256' => $originalHash,
                ]);
                $headers = array_filter($media->getCustomHeaders(), fn ($key) => ! in_array(strtolower($key), ['contenttype', 'content-type', 'contentlength', 'content-length'], true), ARRAY_FILTER_USE_KEY);
                $media->setCustomHeaders([...$headers, 'ContentType' => 'image/webp']);
                // The file is already moved; bypass Spatie's automatic filename-move observer.
                $media->saveQuietly();
                if ($regenerateThumbnail) {
                    app(FileManipulator::class)->createDerivedFiles($media, ['thumb']);
                }
            });
            if ($source !== $target && is_file($source)) {
                try {
                    unlink($source);
                } catch (Throwable $cleanupError) {
                    // The database now references the valid WebP; retain a leftover source rather than rolling it back.
                    Log::warning('Old CMS image copy could not be removed.', ['media_uuid' => $media->uuid, 'exception' => $cleanupError]);
                }
            }

            return true;
        } catch (Throwable $exception) {
            if ($rollback && is_file($rollback)) {
                File::copy($rollback, $source);
            }
            if ($targetWritten && $source !== $target && is_file($target)) {
                unlink($target);
            }
            $media->setRawAttributes($before, true);

            throw $exception;
        } finally {
            foreach ([$output, $staged, $backup ? null : $rollback] as $temporary) {
                if ($temporary && is_file($temporary)) {
                    unlink($temporary);
                }
            }
        }
    }

    private function memoryLimit(): int
    {
        $value = trim(ini_get('memory_limit'));
        if ($value === '-1') {
            return -1;
        }
        $factor = match (strtolower(substr($value, -1))) {
            'g' => 1024 ** 3, 'm' => 1024 ** 2, 'k' => 1024, default => 1,
        };

        return (int) $value * $factor;
    }
}
