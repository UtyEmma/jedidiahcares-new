<?php

namespace App\Filament\Resources\Report;

use App\Filament\Resources\ContentResource;

class ReportResource extends ContentResource
{
    public const COLLECTION = 'reports';

    protected static ?string $slug = 'reports';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReport::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
