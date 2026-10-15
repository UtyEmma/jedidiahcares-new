<?php

namespace App\Filament\Resources\Programme;

use App\Filament\Resources\ContentResource;

class ProgrammeResource extends ContentResource
{
    public const COLLECTION = 'programmes';

    protected static ?string $slug = 'programmes';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgramme::route('/'),
            'create' => Pages\CreateProgramme::route('/create'),
            'edit' => Pages\EditProgramme::route('/{record}/edit'),
        ];
    }
}
