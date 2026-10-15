<?php

namespace App\Filament\Resources\Metric\Pages;

use App\Filament\Resources\Metric\MetricResource;
use App\Filament\Resources\Pages\CreateContentRecord;
use Illuminate\Support\Str;

class CreateMetric extends CreateContentRecord
{
    protected static string $resource = MetricResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['collection'] = 'metrics';
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
