<?php

namespace Database\Seeders;

use App\Support\CmsContentSeeder;
use App\Support\CmsSchema;
use App\Support\CmsSeedReport;
use Illuminate\Database\Seeder;

class WebsiteContentSeeder extends Seeder
{
    public function run(CmsContentSeeder $seeder): void
    {
        $statistics = $seeder->seed(array_keys(CmsSchema::COLLECTIONS), progress: fn ($message) => $this->command?->line($message));
        $this->call(SeoSeeder::class);
        CmsSeedReport::write($statistics, $this->command);
    }
}
