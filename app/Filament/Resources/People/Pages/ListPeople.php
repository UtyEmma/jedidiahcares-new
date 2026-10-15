<?php

namespace App\Filament\Resources\People\Pages;

use App\Filament\Resources\People\PeopleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPeople extends ListRecords
{
    protected static string $resource = PeopleResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
