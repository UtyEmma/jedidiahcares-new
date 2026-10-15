<?php

namespace App\Filament\Resources\Seo;

use App\Models\SeoSetting;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoResource extends Resource
{
    protected static ?string $model = SeoSetting::class;

    protected static ?string $slug = 'seo';

    protected static ?string $navigationLabel = 'SEO';

    protected static ?string $pluralModelLabel = 'SEO settings';

    protected static ?int $navigationSort = 2;

    protected static string|\UnitEnum|null $navigationGroup = 'Website Settings';

    protected static string|\BackedEnum|null $navigationIcon = 'ph-chart-bar';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(fn (?SeoSetting $record) => [
            Section::make($record?->path === 'global' ? 'Site-wide search appearance' : 'Page search appearance')
                ->description('These values change search and social previews. Website headings, copy and layouts stay unchanged.')
                ->schema([
                    TextInput::make('title')->label($record?->path === 'global' ? 'Title suffix' : 'SEO title')->maxLength(255)
                        ->helperText($record?->path === 'global' ? 'Defaults to the organisation’s name.' : 'The site title suffix is appended automatically. Leave blank to use the page title.'),
                    Textarea::make('description')->label('Meta description')->rows(3)->maxLength(500)->helperText('Leave blank to use the existing description.'),
                    TextInput::make('social_image')->label('Social preview image')->maxLength(2048)->helperText('An HTTPS image URL or a local path such as /images/social-preview.png.'),
                    ...($record?->path === 'global' ? [
                        TextInput::make('site_url')->label('Canonical website URL')->url()->maxLength(2048),
                        Toggle::make('indexing_enabled')->label('Enable search engine indexing')->afterStateHydrated(function ($component, $state): void {
                            if ($state === null) {
                                $component->state(true);
                            }
                        })->helperText('When disabled, the public website stays noindex and the sitemap remains empty.'),
                    ] : [
                        TextInput::make('canonical_url')->label('Canonical URL override')->url()->maxLength(2048)->helperText('Optional. Defaults to the current website URL and page path.'),
                        Toggle::make('indexable')->label('Allow this page to be indexed')->helperText('Site-wide indexing must also be enabled.'),
                    ]),
                ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('path')->label('Page')->formatStateUsing(fn ($state) => SeoSetting::pageLabels()[$state] ?? $state)->description(fn ($record) => $record->path === 'global' ? 'Defaults across the website' : $record->path)->searchable(),
            TextColumn::make('title')->label('SEO title')->placeholder('Original page title')->wrap(),
            TextColumn::make('editor.name')->label('Last edited by')->placeholder('Initial setup'),
            TextColumn::make('updated_at')->label('Updated')->since(),
        ])->recordActions([EditAction::make()])->defaultSort('path');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListSeo::route('/'), 'edit' => Pages\EditSeo::route('/{record}/edit')];
    }
}
