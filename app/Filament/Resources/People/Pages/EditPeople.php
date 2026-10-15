<?php

namespace App\Filament\Resources\People\Pages;

use App\Filament\Resources\Pages\EditContentRecord;
use App\Filament\Resources\People\PeopleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

class EditPeople extends EditContentRecord
{
    protected static string $resource = PeopleResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
