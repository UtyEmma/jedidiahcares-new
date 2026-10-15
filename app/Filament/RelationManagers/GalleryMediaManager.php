<?php

namespace App\Filament\RelationManagers;

use App\Support\CmsForms;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Saade\Fluppy\Forms\Components\SpatieMediaLibraryFluppyField;

class GalleryMediaManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $title = 'Gallery media';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Media title')->required(),
            Textarea::make('custom_properties.caption')->label('Caption')->required()->maxLength(1000),
            Textarea::make('custom_properties.alt')->label('Image alternative text')->required(fn ($record) => str_starts_with($record->mime_type, 'image/'))->maxLength(500),
            TextInput::make('custom_properties.credit')->label('Credit')->maxLength(255),
            Toggle::make('custom_properties.visible')->label('Show on website')->default(false),
            Toggle::make('custom_properties.consentApproved')->label('Publication consent confirmed')->default(false),
            CmsForms::image('posterUpload', 'poster')->label('Video poster image')->visible(fn ($record) => str_starts_with($record->mime_type, 'video/')),
            SpatieMediaLibraryFluppyField::make('captionsUpload')->label('Video captions')->collection('captions')->disk('cms')->visibility('private')->acceptedFileTypes(['text/vtt', 'text/plain', '.vtt'])->maxSize(1024)->helperText('Upload a WebVTT (.vtt) file, up to 1 MB.')->visible(fn ($record) => str_starts_with($record->mime_type, 'video/')),
            Textarea::make('custom_properties.transcript')->label('Video transcript')->rows(6)->visible(fn ($record) => str_starts_with($record->mime_type, 'video/')),
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table->modifyQueryUsing(fn ($query) => $query->where('collection_name', 'gallery'))->columns([
            TextColumn::make('name')->label('Media')->searchable(),
            TextColumn::make('mime_type')->label('Type'),
            TextColumn::make('custom_properties.caption')->label('Caption')->wrap()->limit(80),
            TextColumn::make('custom_properties.visible')->label('Visible')->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
            TextColumn::make('custom_properties.consentApproved')->label('Consent')->formatStateUsing(fn ($state) => $state ? 'Confirmed' : 'Pending'),
        ])->defaultSort('order_column')->reorderable('order_column')->recordActions([
            EditAction::make()->label('Edit details'), DeleteAction::make(),
        ]);
    }
}
