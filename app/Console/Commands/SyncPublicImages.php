<?php

namespace App\Console\Commands;

use App\Models\CmsMedia;
use App\Support\CmsImageOptimizer;
use App\Support\CmsPublicImages;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Conversions\FileManipulator;
use Throwable;

class SyncPublicImages extends Command
{
    protected $signature = 'cms:sync-public-images {--dry-run : Show eligible images without writing files}';

    protected $description = 'Save published images and thumbnails as direct public files; remove ineligible and orphaned copies';

    public function handle(CmsPublicImages $publisher): int
    {
        $published = 0;
        $private = 0;
        $failures = 0;
        $disk = Storage::disk(CmsPublicImages::DISK);
        foreach (CmsMedia::whereIn('mime_type', CmsImageOptimizer::MIME_TYPES)->lazyById(50) as $media) {
            try {
                if ($this->option('dry-run')) {
                    $media->isPublic() ? $published++ : $private++;

                    continue;
                }
                if (is_file($media->getPath()) && (! $media->hasGeneratedConversion('thumb') || ! is_file($media->getPath('thumb')))) {
                    app(FileManipulator::class)->createDerivedFiles($media, ['thumb']);
                }
                $publisher->sync($media) ? $published++ : $private++;
            } catch (Throwable $exception) {
                $failures++;
                $this->error('Image #'.$media->id.': '.$exception->getMessage());
            }
        }
        $orphaned = 0;
        foreach ($disk->directories() as $directory) {
            if (Str::isUuid($directory) && ! CmsMedia::where('uuid', $directory)->exists()) {
                if (! $this->option('dry-run')) {
                    $disk->deleteDirectory($directory);
                }
                $orphaned++;
            }
        }
        $verb = $this->option('dry-run') ? 'eligible for public files' : 'published as files';
        $this->info("{$published} images {$verb}; {$private} private; {$orphaned} orphaned directories; {$failures} failures.");

        return $failures ? self::FAILURE : self::SUCCESS;
    }
}
