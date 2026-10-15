<?php

namespace App\Filament\Resources\Story;

use App\Filament\Resources\ContentResource;

class StoryResource extends ContentResource
{
    public const COLLECTION = 'stories';

    protected static ?string $slug = 'stories';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStory::route('/'),
            'create' => Pages\CreateStory::route('/create'),
            'edit' => Pages\EditStory::route('/{record}/edit'),
        ];
    }
}
