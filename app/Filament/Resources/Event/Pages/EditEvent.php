<?php

namespace App\Filament\Resources\Event\Pages;

use App\Filament\Resources\Event\EventResource;
use App\Filament\Resources\Pages\EditContentRecord;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

class EditEvent extends EditContentRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
