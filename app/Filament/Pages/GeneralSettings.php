<?php

namespace App\Filament\Pages;

use App\Support\CmsForms;
use App\Support\WebsiteSettings;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class GeneralSettings extends SettingsPage
{
    protected static ?string $title = 'General & Brand';

    protected static ?string $navigationLabel = 'General & Brand';

    protected static string|\BackedEnum|null $navigationIcon = 'ph-heart';

    protected static ?int $navigationSort = 1;

    protected ?string $subheading = 'Manage the identity and contact details used across the website. Edit section content under Pages; page layouts stay in code.';

    public function form(Schema $schema): Schema
    {
        return $schema->statePath('data')->model($this->settingsRecord())->components([
            Tabs::make('Site settings')->tabs([
                Tab::make('Identity & brand')->schema([
                    Section::make('Site identity')->schema([
                        TextInput::make('values.site_title')->label('Site title / organisation name')->required()->maxLength(255)->columnSpanFull(),
                        TextInput::make('values.short_name')->label('Short name')->required()->maxLength(100)->helperText('Used in compact search titles and the admin header.'),
                        TextInput::make('values.tagline')->label('Tagline')->maxLength(300),
                        Textarea::make('values.site_description')->label('Site description')->required()->rows(3)->maxLength(500)->helperText('The default search description. Page-specific SEO descriptions take priority.')->columnSpanFull(),
                        ColorPicker::make('values.primary_color')->label('Primary colour')->required()->regex('/^#[0-9a-fA-F]{6}$/')->helperText('Use a dark colour for readable text and buttons.'),
                        ColorPicker::make('values.accent_color')->label('Accent colour')->required()->regex('/^#[0-9a-fA-F]{6}$/')->helperText('Use a light colour that contrasts with the primary colour.'),
                    ])->columns(2),
                    Section::make('Brand images')->description('Upload JPEG, PNG or WebP. Images are optimized automatically. Removing an upload restores the bundled default; an empty footer logo uses the main logo.')->schema([
                        CmsForms::image('logoUpload', 'logo')->label('Main logo'),
                        CmsForms::image('footerLogoUpload', 'footer-logo')->label('Footer logo'),
                        CmsForms::image('faviconUpload', 'favicon')->label('Favicon')->helperText('A square image works best. The supplied icon.webp is used by default.'),
                        CmsForms::image('socialImageUpload', 'social-image')->label('Default social preview image'),
                    ]),
                ]),
                Tab::make('Contact')->schema([
                    TextInput::make('values.contact_email')->label('Public email address')->email()->maxLength(254),
                    TextInput::make('values.contact_phone')->label('Telephone')->tel()->maxLength(50)->regex('/^[+0-9() .-]+$/'),
                    Textarea::make('values.contact_address')->label('Office address')->rows(4)->maxLength(1000)->helperText('Blank contact fields are hidden from the website.'),
                ]),
                Tab::make('Social media')->schema(array_map(
                    fn ($platform, $label) => TextInput::make('values.social_'.$platform)->label($label)->url()->rules(['url:https'])->maxLength(2048)->placeholder('https://...')->helperText('Leave blank to hide this profile.'),
                    array_keys(WebsiteSettings::SOCIAL_PLATFORMS), array_values(WebsiteSettings::SOCIAL_PLATFORMS),
                )),
            ])->columnSpanFull(),
        ]);
    }
}
