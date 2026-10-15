<?php

namespace App\Filament\Resources\Programme\Pages;

use App\Filament\Resources\Pages\EditContentRecord;
use App\Filament\Resources\Programme\ProgrammeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

class EditProgramme extends EditContentRecord
{
    protected static string $resource = ProgrammeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
