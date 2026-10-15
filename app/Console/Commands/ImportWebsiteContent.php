<?php

namespace App\Console\Commands;

use App\Support\CmsContentSeeder;
use App\Support\CmsSchema;
use App\Support\CmsSeedReport;
use Database\Seeders\SeoSeeder;
use Database\Seeders\SiteSettingSeeder;
use Illuminate\Console\Command;

class ImportWebsiteContent extends Command
{
    protected $signature = 'cms:import-content
        {--collection=* : Import only these collection keys and their dependencies}
        {--without-media : Seed records without importing local media files}';

    protected $description = 'Seed PHP data and official website collections without overwriting CMS edits';

    public function handle(CmsContentSeeder $seeder): int
    {
        $collections = $this->option('collection') ?: array_keys(CmsSchema::COLLECTIONS);
        if ($invalid = array_diff($collections, array_keys(CmsSchema::COLLECTIONS))) {
            $this->error('Unknown collection(s): '.implode(', ', $invalid));

            return self::FAILURE;
        }
        $statistics = $seeder->seed($collections, ! $this->option('without-media'), fn ($message) => $this->line($message));
        (new SeoSeeder)->run();
        app()->call([new SiteSettingSeeder, 'run']);
        CmsSeedReport::write($statistics, $this);

        return self::SUCCESS;
    }
}
