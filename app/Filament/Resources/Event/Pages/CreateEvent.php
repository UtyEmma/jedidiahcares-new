<?php

namespace App\Filament\Resources\Event\Pages;

use App\Filament\Resources\Event\EventResource;
use App\Filament\Resources\Pages\CreateContentRecord;
use Illuminate\Support\Str;

class CreateEvent extends CreateContentRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['collection'] = 'events';
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
