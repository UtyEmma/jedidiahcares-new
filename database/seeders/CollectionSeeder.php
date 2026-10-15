<?php

namespace Database\Seeders;

use App\Support\CmsContentSeeder;
use App\Support\CmsSeedReport;
use Illuminate\Database\Seeder;

abstract class CollectionSeeder extends Seeder
{
    public const COLLECTION = '';

    public function run(CmsContentSeeder $seeder): void
    {
        $statistics = $seeder->seed([static::COLLECTION], progress: fn ($message) => $this->command?->line($message));
        CmsSeedReport::write($statistics, $this->command);
    }
}
