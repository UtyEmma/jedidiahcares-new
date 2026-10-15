<?php

namespace App\Filament\Resources\Report\Pages;

use App\Filament\Resources\Pages\EditContentRecord;
use App\Filament\Resources\Report\ReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

class EditReport extends EditContentRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
