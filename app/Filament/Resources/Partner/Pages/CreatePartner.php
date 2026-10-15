<?php

namespace App\Filament\Resources\Partner\Pages;

use App\Filament\Resources\Pages\CreateContentRecord;
use App\Filament\Resources\Partner\PartnerResource;
use Illuminate\Support\Str;

class CreatePartner extends CreateContentRecord
{
    protected static string $resource = PartnerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['collection'] = 'partners';
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
