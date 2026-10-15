<?php

namespace App\Filament\Resources\Programme\Pages;

use App\Filament\Resources\Programme\ProgrammeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgramme extends ListRecords
{
    protected static string $resource = ProgrammeResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
