<?php

namespace App\Filament\Resources\Partner\Pages;

use App\Filament\Resources\Pages\EditContentRecord;
use App\Filament\Resources\Partner\PartnerResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

class EditPartner extends EditContentRecord
{
    protected static string $resource = PartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make(), RestoreAction::make(), ForceDeleteAction::make()];
    }
}
