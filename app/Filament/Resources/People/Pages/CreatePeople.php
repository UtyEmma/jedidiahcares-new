<?php

namespace App\Filament\Resources\People\Pages;

use App\Filament\Resources\Pages\CreateContentRecord;
use App\Filament\Resources\People\PeopleResource;
use Illuminate\Support\Str;

class CreatePeople extends CreateContentRecord
{
    protected static string $resource = PeopleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['collection'] = 'people';
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
