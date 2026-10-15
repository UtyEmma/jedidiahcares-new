<?php

namespace App\Console\Commands;

use App\Models\CmsMedia;
use App\Support\CmsImageOptimizer;
use Illuminate\Console\Command;
use Throwable;

class OptimizeCmsImages extends Command
{
    protected $signature = 'cms:optimize-images
        {--dry-run : List images without changing files or records}
        {--id=* : Process only these media IDs}';

    protected $description = 'Convert existing CMS images to resized WebP, keeping private originals as backups';

    public function handle(CmsImageOptimizer $optimizer): int
    {
        $query = CmsMedia::whereIn('mime_type', CmsImageOptimizer::MIME_TYPES)
            ->when($this->option('id'), fn ($query, $ids) => $query->whereIn('id', $ids));
        $changed = 0;
        $skipped = 0;
        $failures = 0;
        $saved = 0;
        foreach ($query->lazyById(50) as $media) {
            try {
                if ($optimizer->isCurrent($media)) {
                    $skipped++;

                    continue;
                }
                if ($this->option('dry-run')) {
                    $this->line('Would optimize #'.$media->id.': '.$media->file_name);
                    $changed++;

                    continue;
                }
                $before = $media->size;
                if ($optimizer->optimize($media)) {
                    $changed++;
                    $saved += $before - $media->size;
                    $this->line('Optimized #'.$media->id.': '.$media->file_name);
                }
            } catch (Throwable $exception) {
                $failures++;
                $this->error('Image #'.$media->id.': '.$exception->getMessage());
            }
        }
        $verb = $this->option('dry-run') ? 'eligible' : 'optimized';
        $this->info("{$changed} images {$verb}; {$skipped} already optimized; {$failures} failures.");
        if (! $this->option('dry-run')) {
            $this->line('Image storage change: '.number_format($saved / 1024 / 1024, 2).' MiB saved, excluding backups.');
            $this->line('Original backups: '.storage_path('app/private/image-optimization-backups'));
        }

        return $failures ? self::FAILURE : self::SUCCESS;
    }
}
