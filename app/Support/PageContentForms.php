<?php

namespace App\Support;

use App\Models\SeoSetting;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Image;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Str;

class PageContentForms
{
    public static function sections(SeoSetting $record): array
    {
        $sections = [];
        foreach (app(PageContentSchema::class)->fields($record->path) as $path => $field) {
            $name = 'content.'.$path;
            $input = match (true) {
                $field['type'] === 'date' => DatePicker::make($name)->native(false),
                (bool) $field['long'] => Textarea::make($name)->rows(4),
                default => TextInput::make($name),
            };
            $input->label($field['label'])->required($field['required'])
                ->rules(fn () => $field['type'] === 'date' ? ['present', 'date'] : app(PageContentSchema::class)->rules($field))
                ->dehydrateStateUsing(fn ($state) => $field['type'] === 'date' && filled($state) ? Carbon::parse($state)->toDateString() : ($state ?? ''))->columnSpanFull();
            if ($input instanceof TextInput || $input instanceof Textarea) {
                $input->maxLength($field['max']);
            }
            if (in_array($field['type'], ['link', 'external-url'], true)) {
                $input->helperText($field['type'] === 'link' ? 'Use an internal path beginning with / or a complete HTTPS URL.' : 'Optional HTTPS address for the external enquiry form.');
            }
            $sections[$field['group']][] = $input;
        }

        return array_map(fn ($name, $fields) => Section::make($name)
            ->description('Edit the content in this existing section. Its layout and position are fixed.')
            ->schema($fields)->collapsible()->collapsed($name !== 'Hero' && $name !== 'Page introduction'), array_keys($sections), array_values($sections));
    }

    public static function images(SeoSetting $record): array
    {
        $schema = app(PageContentSchema::class);
        $sources = $schema->sources();

        return array_map(function ($slot, $definition) use ($sources, $record): Section {
            $source = isset($definition['photo']) ? 'media' : strtok($definition['target'], '.');
            $inherited = $record->path === 'global' ? $sources[$source] : app(PageContent::class)->apply($source, $sources[$source], 'global');
            $fallback = isset($definition['photo']) ? $inherited[$definition['photo']]['src'] : data_get([$source => $inherited], $definition['target']);
            if (str_starts_with($fallback, '/')) {
                $fallback = asset(ltrim($fallback, '/'));
            }
            $uploadField = 'pageImage_'.$slot;

            return Section::make($definition['group'].' image')
                ->description('The existing image frame, proportions and styling are retained. Removing your upload restores the inherited or bundled image. Edit alternative text in the matching content section.')
                ->schema([
                    Image::make($fallback, 'Current inherited image')->imageHeight('120px')
                        ->hidden(fn (Get $get): bool => filled($get($uploadField))),
                    CmsForms::image($uploadField, 'page-'.$slot)->label(Str::headline($slot).' image')->live(),
                ]);
        }, array_keys($schema->imageSlots($record->path)), array_values($schema->imageSlots($record->path)));
    }
}
