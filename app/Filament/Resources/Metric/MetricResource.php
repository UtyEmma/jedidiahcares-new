<?php

namespace App\Filament\Resources\Metric;

use App\Filament\Resources\ContentResource;

class MetricResource extends ContentResource
{
    public const COLLECTION = 'metrics';

    protected static ?string $slug = 'metrics';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMetric::route('/'),
            'create' => Pages\CreateMetric::route('/create'),
            'edit' => Pages\EditMetric::route('/{record}/edit'),
        ];
    }
}
