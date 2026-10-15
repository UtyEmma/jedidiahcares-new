<?php

namespace Database\Seeders;

use App\Support\WebsiteSettings;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(WebsiteSettings $settings): void
    {
        $settings->forEditing();
    }
}
