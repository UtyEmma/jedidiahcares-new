<?php

namespace App\Filament\Resources\Gallery;

use App\Filament\Resources\ContentResource;

class GalleryResource extends ContentResource
{
    public const COLLECTION = 'galleries';

    protected static ?string $slug = 'galleries';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGallery::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
