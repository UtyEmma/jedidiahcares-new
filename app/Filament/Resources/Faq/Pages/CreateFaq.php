<?php

namespace App\Filament\Resources\Faq\Pages;

use App\Filament\Resources\Faq\FaqResource;
use App\Filament\Resources\Pages\CreateContentRecord;
use Illuminate\Support\Str;

class CreateFaq extends CreateContentRecord
{
    protected static string $resource = FaqResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['collection'] = 'faq';
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
