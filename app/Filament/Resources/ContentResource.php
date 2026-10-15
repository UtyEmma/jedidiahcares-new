<?php

namespace App\Filament\Resources;

use App\Filament\RelationManagers\GalleryMediaManager;
use App\Models\ContentEntry;
use App\Support\CmsForms;
use App\Support\CmsSchema;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\ValidationException;

abstract class ContentResource extends Resource
{
    protected static ?string $model = ContentEntry::class;

    public const COLLECTION = '';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return CmsSchema::COLLECTIONS[static::COLLECTION];
    }

    public static function getPluralModelLabel(): string
    {
        return static::getNavigationLabel();
    }

    public static function getModelLabel(): string
    {
        return ['people' => 'person', 'programmes' => 'programme', 'projects' => 'project', 'reports' => 'report', 'stories' => 'story', 'events' => 'event', 'galleries' => 'gallery', 'partners' => 'partner', 'faq' => 'FAQ', 'metrics' => 'impact metric'][static::COLLECTION];
    }

    public static function getNavigationSort(): ?int
    {
        return (int) array_search(static::COLLECTION, array_keys(CmsSchema::COLLECTIONS), true);
    }

    public static function getNavigationGroup(): ?string
    {
        return static::COLLECTION === 'faq' ? 'Our Work' : 'Our Work';
    }

    public static function getNavigationIcon(): ?string
    {
        return 'ph-'.(match (static::COLLECTION) {
            'people' => 'users', 'programmes' => 'heart', 'projects' => 'hand-heart', 'reports' => 'file-text', 'stories' => 'newspaper', 'events' => 'calendar', 'galleries' => 'images', 'partners' => 'handshake', 'faq' => 'question', 'metrics' => 'chart-bar', default => 'note-pencil'
        });
    }

    public static function canCreate(): bool
    {
        return parent::canCreate();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components(fn (?ContentEntry $record) => CmsForms::fields(static::COLLECTION, $record))->columns(1);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('collection', static::COLLECTION)->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->searchable()->sortable()->wrap(),
            TextColumn::make('status')->badge()->color(fn ($state) => match ($state) {
                'published' => 'success', 'archived' => 'gray', default => 'warning'
            }),
            ...(static::COLLECTION === 'galleries' ? [TextColumn::make('is_active')->label('Tab')->formatStateUsing(fn ($state) => $state ? 'Active' : 'Hidden')] : []),
            TextColumn::make('editor.name')->label('Last edited by')->placeholder('Content import')->toggleable(),
            TextColumn::make('updated_at')->label('Updated')->since()->sortable(),
            TextColumn::make('publisher.name')->label('Published by')->placeholder('Content import')->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']), TrashedFilter::make(),
        ])->recordActions([
            EditAction::make(),
            Action::make('publish')->label('Publish')->icon('ph-check')->visible(fn ($record) => ! $record->trashed() && $record->status !== 'published')->requiresConfirmation()->action(function ($record): void {
                try {
                    if ($record->collection === 'reports' && ! $record->hasMedia('document')) {
                        throw ValidationException::withMessages(['documentUpload' => 'Upload a report PDF before publishing.']);
                    }
                    $record->update(['status' => 'published']);
                } catch (ValidationException $exception) {
                    Notification::make()->title('Complete the content before publishing')->body(implode(' ', $exception->validator->errors()->all()))->danger()->persistent()->send();
                }
            }),
            Action::make('archive')->label('Archive')->icon('ph-archive')->visible(fn ($record) => ! $record->trashed() && $record->status === 'published')->requiresConfirmation()->action(fn ($record) => $record->update(['status' => 'archived'])),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make(), RestoreBulkAction::make(), ForceDeleteBulkAction::make()])])->defaultSort('sort_order')->reorderable('sort_order')->paginated([15, 30, 60]);
    }

    public static function getRelations(): array
    {
        return static::COLLECTION === 'galleries' ? [GalleryMediaManager::class] : [];
    }
}
