<?php

namespace App\Filament\Resources\Story\Pages;

use App\Filament\Resources\Pages\EditContentRecord;
use App\Filament\Resources\Story\StoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

class EditStory extends EditContentRecord
{
    protected static string $resource = StoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
