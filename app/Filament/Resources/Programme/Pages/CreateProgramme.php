<?php

namespace App\Filament\Resources\Programme\Pages;

use App\Filament\Resources\Pages\CreateContentRecord;
use App\Filament\Resources\Programme\ProgrammeResource;
use Illuminate\Support\Str;

class CreateProgramme extends CreateContentRecord
{
    protected static string $resource = ProgrammeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['collection'] = 'programmes';
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
