<?php

namespace App\Filament\Resources\Metric\Pages;

use App\Filament\Resources\Metric\MetricResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMetric extends ListRecords
{
    protected static string $resource = MetricResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
