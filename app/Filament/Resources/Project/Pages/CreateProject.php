<?php

namespace App\Filament\Resources\Project\Pages;

use App\Filament\Resources\Pages\CreateContentRecord;
use App\Filament\Resources\Project\ProjectResource;
use Illuminate\Support\Str;

class CreateProject extends CreateContentRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['collection'] = 'projects';
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
