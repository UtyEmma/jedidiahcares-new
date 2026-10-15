<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Staff Accounts';

    protected static ?string $pluralModelLabel = 'Staff Accounts';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    protected static string|\BackedEnum|null $navigationIcon = 'ph-users';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('password')->password()->minLength(12)->maxLength(255)->required(fn ($operation) => $operation === 'create')->dehydrated(fn ($state) => filled($state))->helperText('At least 12 characters. Leave blank when editing to keep the current password.'),
            Select::make('role')->options(['admin' => 'Administrator', 'editor' => 'Editor'])->default('editor')->required(),
            Toggle::make('is_active')->label('Account active')->default(true),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(), TextColumn::make('email')->searchable(),
            TextColumn::make('role')->badge(), TextColumn::make('is_active')->label('Active')->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
        ])->recordActions([EditAction::make()->before(function (User $record, array $data, EditAction $action): void {
            if ($record->role === 'admin' && $record->is_active && (($data['role'] ?? 'admin') !== 'admin' || ! ($data['is_active'] ?? true)) && ! User::where('role', 'admin')->where('is_active', true)->whereKeyNot($record->id)->exists()) {
                Notification::make()->title('Keep an active administrator')->body('Create or activate another administrator before changing this account.')->danger()->send();
                $action->halt();
            }
        })->mutateRecordDataUsing(function ($data) {
            $data['password'] = '';

            return $data;
        }), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageStaff::route('/')];
    }
}

class ManageStaff extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
