<?php

namespace App\Support;

use Illuminate\Console\Command;

class CmsSeedReport
{
    public static function write(array $statistics, ?Command $command): void
    {
        if (! $command) {
            return;
        }
        $command->info("Created {$statistics['created']} records; preserved {$statistics['preserved']} existing records; imported {$statistics['media']} media files.");
        if ($statistics['failures']) {
            $command->warn('Some local source files were unavailable. Supply the missing seed files or run cms:bundle-seed-media, then repeat the seeder; completed imports are preserved.');
            foreach ($statistics['failures'] as $failure) {
                $command->warn($failure);
            }
        }
    }
}
