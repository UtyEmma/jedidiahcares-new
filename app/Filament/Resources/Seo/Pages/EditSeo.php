<?php

namespace App\Filament\Resources\Seo\Pages;

use App\Filament\Resources\Pages\EditContentRecord;
use App\Filament\Resources\Seo\SeoResource;
use App\Support\PageContent;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class EditSeo extends EditContentRecord
{
    protected static string $resource = SeoResource::class;

    protected ?string $subheading = 'Edit existing section content and images. Layout, styling, section order and decorative elements are fixed. Saving updates the live website.';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('restoreContent')->label('Use default content')->icon('ph-file-text')
                ->requiresConfirmation()->modalHeading('Restore inherited text and links?')
                ->modalDescription('This fills the content fields with current PHP and shared defaults. Images and SEO values remain as they are. Review the fields and save to apply the change.')
                ->action(function (): void {
                    $this->authorizeAccess();
                    $this->data['content'] = app(PageContent::class)->editingValues($this->getRecord(), includeRecord: false);
                    Notification::make()->title('Default content loaded')->body('Review the content and save changes to apply it.')->success()->send();
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['content'] = app(PageContent::class)->editingValues($this->getRecord());

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['content'] = app(PageContent::class)->overrides($this->getRecord(), $data['content'] ?? []);

        return $data;
    }
}
