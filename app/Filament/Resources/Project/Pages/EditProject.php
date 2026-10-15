<?php

namespace App\Filament\Resources\Project\Pages;

use App\Filament\Resources\Pages\EditContentRecord;
use App\Filament\Resources\Project\ProjectResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

class EditProject extends EditContentRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
