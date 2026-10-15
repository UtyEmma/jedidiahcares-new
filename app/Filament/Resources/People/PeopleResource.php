<?php

namespace App\Filament\Resources\People;

use App\Filament\Resources\ContentResource;

class PeopleResource extends ContentResource
{
    public const COLLECTION = 'people';

    protected static ?string $slug = 'people';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePeople::route('/create'),
            'edit' => Pages\EditPeople::route('/{record}/edit'),
        ];
    }
}
