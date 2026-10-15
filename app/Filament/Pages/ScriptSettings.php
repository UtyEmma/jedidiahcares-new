<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScriptSettings extends SettingsPage
{
    protected static ?string $title = 'Header & Footer Scripts';

    protected static ?string $navigationLabel = 'Scripts';

    protected static string|\BackedEnum|null $navigationIcon = 'ph-note-pencil';

    protected static ?int $navigationSort = 3;

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->model($this->settingsRecord())->components([
            Section::make('Website integrations')->description('For trusted analytics, tag managers and other site integrations. Saved HTML and JavaScript run on the public website only; administrator access is required.')->schema([
                Toggle::make('scripts_enabled')->label('Enable custom scripts')->helperText('Disable to pause both locations without deleting your code.'),
                Textarea::make('header_scripts')->label('Header scripts')->rows(14)->maxLength(100000)->extraInputAttributes(['spellcheck' => 'false', 'class' => 'font-mono'])->helperText('Include complete <script> or <meta> tags. Inserted immediately before </head>.'),
                Textarea::make('footer_scripts')->label('Footer scripts')->rows(14)->maxLength(100000)->extraInputAttributes(['spellcheck' => 'false', 'class' => 'font-mono'])->helperText('Include complete tags. Inserted immediately before </body>. Scripts run once per full page load; use Inertia navigation events for page-view tracking.'),
            ]),
        ])->columns(1);
    }
}
