<?php

namespace App\Support;

use App\Models\ContentEntry;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;
use Saade\Fluppy\Forms\Components\SpatieMediaLibraryFluppyField;

class CmsForms
{
    public static function options(string $collection): array
    {
        return ContentEntry::where('collection', $collection)->orderBy('title')->pluck('title', 'id')->all();
    }

    private static function text(string $name, string $label, bool $long = false): TextInput|Textarea
    {
        return ($long ? Textarea::make('data.' . $name)->rows(5) : TextInput::make('data.' . $name))->label($label);
    }

    private static function relation(string $name, string $label, string $collection, bool $multiple = false): Select
    {
        return Select::make('data.' . $name)->label($label)->options(fn() => self::options($collection))->searchable()->multiple($multiple)->default($multiple ? [] : null);
    }

    private static function funding(): Section
    {
        return Section::make('Project funding')
            ->description('Optional funding progress shown on project cards and the project page. Update the amount raised manually using confirmed totals.')
            ->schema([
                TextInput::make('data.fundingTarget')->label('Funding target')->numeric()->minValue(0.01)->maxValue(999999999999.99)->step(0.01)->rules(['decimal:0,2'])->helperText('Leave blank to hide funding progress for this project.'),
                TextInput::make('data.fundsRaised')->label('Amount raised')->numeric()->minValue(0)->maxValue(999999999999.99)->step(0.01)->rules(['decimal:0,2'])->default(0)->helperText('Use the total raised in the selected currency. Amounts above the target are supported.'),
                Select::make('data.fundingCurrency')->label('Funding currency')->options(CmsSchema::FUNDING_CURRENCIES)->default('NGN')->required()->helperText('Applies to both the target and amount raised.'),
            ])->columns(2)->columnSpanFull();
    }

    private static function socialProfiles(): Section
    {
        return Section::make('Social profiles')
            ->description('Optional public accounts and website links for this person. Use complete HTTPS URLs that the member has agreed to publish.')
            ->schema(array_map(
                fn($platform, $label) => TextInput::make('data.socialLinks.' . $platform)->label($label)->url()->rules(['url:https'])->maxLength(2048)->placeholder('https://...'),
                array_keys(CmsSchema::PEOPLE_SOCIAL_PLATFORMS),
                array_values(CmsSchema::PEOPLE_SOCIAL_PLATFORMS),
            ))->columns(2)->columnSpanFull();
    }

    private static function sections(string $name = 'sections'): Repeater
    {
        return Repeater::make('data.' . $name)->label('Content sections')->schema([
            TextInput::make('heading')->required(),
            Textarea::make('text')->label('Content')->rows(5)->required(),
        ])->default([])->collapsible()->itemLabel(fn(array $state): ?string => $state['heading'] ?? null)->columnSpanFull();
    }

    private static function list(string $name, string $label): Repeater
    {
        return Repeater::make('data.' . $name)->label($label)->simple(Textarea::make('value')->required())->default([])->columnSpanFull();
    }

    public static function image(string $name = 'coverUpload', string $collection = 'cover'): SpatieMediaLibraryFluppyField
    {
        return SpatieMediaLibraryFluppyField::make($name)->label('Image')->collection($collection)->disk('cms')->visibility('private')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(10240)->image()->imageEditor()
            ->openable()
            ->camera()
            ->importFromUrl()
            ->helperText('JPEG, PNG or WebP, up to 10 MB. Images are automatically resized and saved as optimized WebP.')->columnSpanFull();
    }

    public static function fields(string $collection, ?ContentEntry $record): array
    {
        $base = [
            Section::make('Publication')->schema([
                TextInput::make('title')->label($collection === 'people' ? 'Name' : 'Title')->required()->maxLength(255)->live(onBlur: true)->afterStateUpdated(function ($state, $set, $get, $operation): void {
                    if ($operation === 'create' && blank($get('slug'))) {
                        $set('slug', Str::slug($state ?? ''));
                    }
                })->columnSpanFull(),
                TextInput::make('slug')->label('Address')->required()->maxLength(200)->helperText('Use lowercase words separated by hyphens.'),
                Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->default('draft')->required(),
                TextInput::make('sort_order')->label('Display order')->numeric()->default(0)->minValue(0)->required(),
                ...($collection === 'galleries' ? [Toggle::make('is_active')->label('Show gallery tab')->default(true)->helperText('Published galleries with this enabled appear as tabs.')] : []),
                Textarea::make('summary')->label($collection === 'galleries' ? 'Description' : 'Summary')->maxLength(3000)->rows(3)->columnSpanFull(),
            ])->columns(2),
        ];
        $fields = match ($collection) {
            'people' => [self::text('role', 'Role'), Select::make('data.group')->label('Group')->options(['leadership' => 'Leadership', 'team' => 'Team', 'volunteers' => 'Volunteers'])->default('team')->required(), self::text('email', 'Public email address')->email()->maxLength(254)->helperText('Optional. Only enter an address the member has agreed to publish. Leave blank to hide the email link.'), self::text('biography', 'Biography', true), self::socialProfiles(), Toggle::make('data.portraitConsent')->label('Portrait publication consent confirmed')->default(false)],
            'programmes' => [self::text('shortTitle', 'Short title'), self::text('heroTitle', 'Page heading'), self::text('introduction', 'Introduction', true), self::text('originalThematicName', 'Thematic name'), Select::make('data.photoId')->label('Context photograph')->options(array_map(fn($p) => $p['caption'], CmsSchema::seed('media'))), self::sections(), self::list('activities', 'Activities'), self::relation('relatedProjectIds', 'Related projects', 'projects', true)],
            'projects' => [self::relation('programmeIds', 'Programmes', 'programmes', true), Select::make('data.stage')->label('Project status')->options(CmsSchema::PROJECT_STAGES)->default('planned')->required(), self::text('location', 'Location'), DatePicker::make('data.startDate')->label('Start date'), self::funding(), self::sections(), self::list('intendedActivities', 'Intended activities'), Repeater::make('data.verifiedOutcomes')->label('Outcomes')->schema([Textarea::make('description')->required(), TextInput::make('source')->required(), Toggle::make('verified')->label('Evidence verified')->default(false)])->default([])->columnSpanFull(), self::relation('reportIds', 'Related reports', 'reports', true), self::text('approvedDonationUrl', 'Approved donation link')->url()],
            'reports' => [self::text('period', 'Reporting period'), DatePicker::make('data.publishedAt')->label('Report date'), self::relation('projectId', 'Project', 'projects'), SpatieMediaLibraryFluppyField::make('documentUpload')->label('Report PDF')->collection('document')->required(fn($get) => $get('status') === 'published')->disk('cms')->visibility('private')->acceptedFileTypes(['application/pdf'])->maxSize(20480)->openable()->helperText('PDF, up to 20 MB.')],
            'stories' => [self::text('category', 'Category'), DatePicker::make('data.publishedAt')->label('Publication date'), self::text('author', 'Author'), self::relation('programmeId', 'Programme', 'programmes'), self::relation('projectId', 'Project', 'projects'), Builder::make('data.blocks')->label('Story content')->blocks([
                Block::make('paragraph')->schema([Textarea::make('text')->rows(6)->required()]),
                Block::make('heading')->schema([TextInput::make('text')->required()]),
                Block::make('quote')->schema([Textarea::make('text')->required(), TextInput::make('attribution')]),
                Block::make('bullets')->schema([Repeater::make('items')->simple(TextInput::make('value')->required())]),
            ])->default([])->columnSpanFull()],
            'events' => [DateTimePicker::make('data.startDate')->label('Starts')->timezone(fn($get) => $get('data.timezone') ?: 'Africa/Lagos'), DateTimePicker::make('data.endDate')->label('Ends')->timezone(fn($get) => $get('data.timezone') ?: 'Africa/Lagos')->helperText('If left blank, the event is treated as ongoing until the end of its start day in the selected time zone.'), Select::make('data.timezone')->label('Time zone')->options(array_combine(timezone_identifiers_list(), timezone_identifiers_list()))->searchable()->default('Africa/Lagos')->required(), self::relation('programmeIds', 'Programmes', 'programmes', true), self::text('location', 'Location'), self::text('audience', 'Who can attend'), self::text('registrationUrl', 'External registration link')->url(), self::sections()],
            'galleries' => [self::relation('projectId', 'Project', 'projects'), self::relation('eventId', 'Event', 'events'), SpatieMediaLibraryFluppyField::make('photoUpload')->label('Upload photos')->collection('gallery')->filterMediaUsing(fn($media) => $media->filter(fn($m) => str_starts_with($m->mime_type, 'image/')))
            ->googleDrive()
            ->googlePhotos()
            ->camera()
            ->importFromUrl()->multiple()->disk('cms')->visibility('private')->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(10240)->customProperties(['visible' => false, 'consentApproved' => false])->helperText('Select multiple photos. Images are automatically resized and saved as optimized WebP. Add captions, alternative text and consent in Gallery media after saving.')->columnSpanFull(), SpatieMediaLibraryFluppyField::make('videoUpload')->label('Upload videos')->collection('gallery')->filterMediaUsing(fn($media) => $media->filter(fn($m) => str_starts_with($m->mime_type, 'video/')))->multiple()->googleDrive()
            ->googlePhotos()
            ->camera()
            ->importFromUrl()->disk('cms')->visibility('private')->acceptedFileTypes(['video/mp4', 'video/webm'])->maxSize(102400)->customProperties(['visible' => false, 'consentApproved' => false])->helperText('MP4 or WebM, up to 100 MB each. Use browser-compatible encoding; videos are not transcoded.')->columnSpanFull()],
            'partners' => [self::text('url', 'Website')->url(), Toggle::make('data.mediaRightsApproved')->label('Logo publication rights confirmed')->default(false)],
            'faq' => [self::text('topic', 'Topic'), self::text('question', 'Question', true), self::text('answer', 'Answer', true)],
            'metrics' => [self::text('label', 'Label'), TextInput::make('data.value')->label('Value')->numeric()->minValue(0), self::text('unit', 'Unit'), self::text('period', 'Period'), self::text('source', 'Evidence source'), Toggle::make('data.verified')->label('Value and evidence verified')->default(false)],
            default => [],
        };
        if (! in_array($collection, ['galleries', 'faq', 'metrics'])) {
            $fields[] = self::image();
            $fields[] = self::text('coverAlt', $collection === 'partners' ? 'Logo alternative text' : 'Image alternative text')->required(fn($get) => $get('status') === 'published' && filled($get('coverUpload')));
        }
        $base[] = Section::make('Details')->schema($fields)->columns(2);
        if (array_key_exists($collection, config('website.details'))) {
            $base[] = Section::make('Search appearance')->schema([self::text('seoTitle', 'Search title')->maxLength(150), self::text('seoDescription', 'Search description', true)->maxLength(500)])->collapsed()->description('Optional overrides for the page’s search title and description.');
        }

        return $base;
    }
}
