<?php

namespace App\Filament\Resources\Project;

use App\Filament\Resources\ContentResource;

class ProjectResource extends ContentResource
{
    public const COLLECTION = 'projects';

    protected static ?string $slug = 'projects';

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProject::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
