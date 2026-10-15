<?php

namespace App\Filament\Resources\Story\Pages;

use App\Filament\Resources\Story\StoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStory extends ListRecords
{
    protected static string $resource = StoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
