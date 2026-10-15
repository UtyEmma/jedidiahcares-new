<?php

namespace App\Filament\Resources\Partner;

use App\Filament\Resources\ContentResource;

class PartnerResource extends ContentResource
{
    public const COLLECTION = 'partners';

    protected static ?string $slug = 'partners';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartner::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}
