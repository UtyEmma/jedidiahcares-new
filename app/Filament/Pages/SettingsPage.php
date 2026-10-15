<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Support\WebsiteSettings;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

abstract class SettingsPage extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Website Settings';

    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccessCms() && auth()->user()?->role === 'admin';
    }

    protected function settingsRecord(): SiteSetting
    {
        Gate::authorize('viewAny', SiteSetting::class);
        $record = app(WebsiteSettings::class)->forEditing();
        Gate::authorize('update', $record);

        return $record;
    }

    public function mount(): void
    {
        $record = $this->settingsRecord();
        $this->form->fill([
            'values' => array_replace(WebsiteSettings::defaults(), $record->values),
            'scripts_enabled' => $record->scripts_enabled,
            'header_scripts' => $record->header_scripts, 'footer_scripts' => $record->footer_scripts,
            'robots_txt' => $record->robots_txt ?: WebsiteSettings::defaultRobots(),
        ]);
    }

    public function save(): void
    {
        $record = $this->settingsRecord();
        try {
            DB::transaction(function () use ($record): void {
                $record->update($this->form->getState());
            });
        } catch (ValidationException $exception) {
            $errors = [];
            foreach ($exception->errors() as $key => $messages) {
                $errors['data.'.$key] = $messages;
            }
            throw ValidationException::withMessages($errors);
        }
        Notification::make()->title('Settings saved')->body('Changes are available on the public website.')->success()->send();
    }
}
