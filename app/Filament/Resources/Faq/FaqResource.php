<?php

namespace App\Filament\Resources\Faq;

use App\Filament\Resources\ContentResource;

class FaqResource extends ContentResource
{
    public const COLLECTION = 'faq';

    protected static ?string $slug = 'faq';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaq::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
