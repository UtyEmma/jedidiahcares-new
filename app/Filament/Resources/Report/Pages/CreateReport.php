<?php

namespace App\Filament\Resources\Report\Pages;

use App\Filament\Resources\Pages\CreateContentRecord;
use App\Filament\Resources\Report\ReportResource;
use Illuminate\Support\Str;

class CreateReport extends CreateContentRecord
{
    protected static string $resource = ReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['collection'] = 'reports';
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
