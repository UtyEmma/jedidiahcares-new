<?php

namespace App\Filament\Resources\Event;

use App\Filament\Resources\ContentResource;

class EventResource extends ContentResource
{
    public const COLLECTION = 'events';

    protected static ?string $slug = 'events';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvent::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
