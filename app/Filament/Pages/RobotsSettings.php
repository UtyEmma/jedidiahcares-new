<?php

namespace App\Filament\Pages;

use App\Support\WebsiteSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RobotsSettings extends SettingsPage
{
    protected static ?string $title = 'Robots.txt';

    protected static ?string $navigationLabel = 'Robots.txt';

    protected static string|\BackedEnum|null $navigationIcon = 'ph-file-text';

    protected static ?int $navigationSort = 4;

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->model($this->settingsRecord())->components([
            Section::make('Crawler instructions')->description('The default allows public pages and media to be crawled, excludes admin and upload utilities, and advertises the sitemap. Search indexing is enabled by default; the site-wide SEO indexing control takes priority if deliberately disabled.')->schema([
                Textarea::make('robots_txt')->label('robots.txt content')->required()->rows(18)->maxLength(100000)->extraInputAttributes(['spellcheck' => 'false', 'class' => 'font-mono'])->helperText('Use {site_url} for the canonical website address from SEO settings. Changes appear immediately at /robots.txt.'),
            ]),
        ])->columns(1);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')->label('View robots.txt')->url(url('/robots.txt'))->openUrlInNewTab(),
            Action::make('restoreDefaults')->label('Restore defaults')->action(function (): void {
                $this->settingsRecord();
                $this->data['robots_txt'] = WebsiteSettings::defaultRobots();
            }),
        ];
    }
}
