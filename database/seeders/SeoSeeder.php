<?php

namespace Database\Seeders;

use App\Models\SeoSetting;
use Illuminate\Database\Seeder;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (array_keys(SeoSetting::pageLabels()) as $path) {
            SeoSetting::firstOrCreate(['path' => $path], ['indexable' => true]);
        }
    }
}
