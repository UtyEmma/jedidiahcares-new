<?php

namespace App\Filament\Widgets;

use App\Models\ContentEntry;
use App\Support\CmsSchema;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Published', ContentEntry::published()->whereIn('collection', array_keys(CmsSchema::COLLECTIONS))->count())->description('Published content records')->color('success'),
            Stat::make('Drafts', ContentEntry::where('status', 'draft')->whereIn('collection', array_keys(CmsSchema::COLLECTIONS))->count())->description('Ready to continue editing')->color('warning'),
            Stat::make('Active galleries', ContentEntry::published()->where('collection', 'galleries')->where('is_active', true)->count())->description('Gallery tabs on the website'),
        ];
    }
}
