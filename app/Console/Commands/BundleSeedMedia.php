<?php

namespace App\Console\Commands;

use App\Support\CmsSchema;
use App\Support\CmsSeedCatalog;
use App\Support\SeedMediaBundle;
use Illuminate\Console\Command;

class BundleSeedMedia extends Command
{
    protected $signature = 'cms:bundle-seed-media
        {--collection=* : Bundle these collections and their dependencies}';

    protected $description = 'Collect default images into grouped local folders for offline database seeding';

    public function handle(CmsSeedCatalog $catalog, SeedMediaBundle $bundle): int
    {
        $collections = $this->option('collection') ?: array_keys(CmsSchema::COLLECTIONS);
        if ($invalid = array_diff($collections, array_keys(CmsSchema::COLLECTIONS))) {
            $this->error('Unknown collection(s): '.implode(', ', $invalid));

            return self::FAILURE;
        }
        $statistics = $bundle->bundle($catalog->select($collections), fn ($message) => $this->line($message));
        $this->info("Bundled {$statistics['bundled']} files; preserved {$statistics['preserved']} existing bundled files.");
        $this->line('Files: '.$bundle->directory());
        if ($statistics['failures']) {
            foreach ($statistics['failures'] as $failure) {
                $this->warn($failure);
            }
            $this->warn('Unavailable sources are recorded in media-manifest.json. No substitute images were created.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
