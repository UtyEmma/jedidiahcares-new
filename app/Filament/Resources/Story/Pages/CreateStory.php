<?php

namespace App\Filament\Resources\Story\Pages;

use App\Filament\Resources\Pages\CreateContentRecord;
use App\Filament\Resources\Story\StoryResource;
use Illuminate\Support\Str;

class CreateStory extends CreateContentRecord
{
    protected static string $resource = StoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['collection'] = 'stories';
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
