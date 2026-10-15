<?php

namespace App\Filament\Resources\Metric\Pages;

use App\Filament\Resources\Metric\MetricResource;
use App\Filament\Resources\Pages\EditContentRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

class EditMetric extends EditContentRecord
{
    protected static string $resource = MetricResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
