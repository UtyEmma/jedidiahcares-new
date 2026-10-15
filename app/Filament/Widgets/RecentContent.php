<?php

namespace App\Filament\Widgets;

use App\Models\ContentEntry;
use App\Support\CmsSchema;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class RecentContent extends Widget
{
    protected string $view = 'filament.widgets.recent-content';

    protected int|string|array $columnSpan = 'full';

    public function entries()
    {
        return ContentEntry::whereIn('collection', array_keys(CmsSchema::COLLECTIONS))->with('editor')->latest('updated_at')->take(8)->get();
    }

    public function resources(): array
    {
        $resources = [];
        foreach (Filament::getResources() as $resource) {
            if (defined($resource.'::COLLECTION')) {
                $resources[$resource::COLLECTION] = $resource;
            }
        }

        return $resources;
    }
}
