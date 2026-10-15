<?php

namespace App\Filament\Resources\Report\Pages;

use App\Filament\Resources\Report\ReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReport extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
