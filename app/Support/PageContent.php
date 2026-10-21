<?php

namespace App\Support;

use App\Models\SeoSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class PageContent
{
    private array $records = [];

    private array $loadedPaths = [];

    public function __construct(private PageContentSchema $schema) {}

    private function records(?string $path = null): array
    {
        $missing = array_diff(array_unique(['global', $path ?? 'global']), $this->loadedPaths);
        if ($missing) {
            $this->records += SeoSetting::with('media')->whereIn('path', $missing)->get()->keyBy('path')->all();
            $this->loadedPaths = [...$this->loadedPaths, ...$missing];
        }

        return $this->records;
    }

    /** Hydrate existing content rather than asking an editor to recreate the page. */
    public function editingValues(SeoSetting $record, bool $includeRecord = true): array
    {
        $values = $this->schema->sources();
        if ($record->path !== 'global' && ($global = $this->records()['global'] ?? null)) {
            $values = $this->merge($values, $global);
        }
        if ($includeRecord) {
            $values = $this->merge($values, $record);
        }
        $result = [];
        foreach ($this->schema->fields($record->path) as $key => $field) {
            Arr::set($result, $key, data_get($values, $key, $field['default']));
        }

        return $result;
    }

    /** Store only changes so runtime PHP defaults and shared content can keep evolving. */
    public function overrides(SeoSetting $record, array $values): array
    {
        $values = array_map(fn ($value) => $value === null ? '' : $value, Arr::dot($values));
        $content = [];
        foreach ($values as $key => $value) {
            Arr::set($content, $key, $value);
        }
        $this->schema->validate($record->path, $content);
        $defaults = Arr::dot($this->editingValues($record, includeRecord: false));
        $overrides = [];
        foreach ($values as $key => $value) {
            if ($value !== ($defaults[$key] ?? null)) {
                Arr::set($overrides, $key, $value);
            }
        }

        return $overrides;
    }

    public function apply(string $source, array $values, ?string $path): array
    {
        $records = $this->records($path);
        foreach (array_unique(['global', $path ?? 'global']) as $key) {
            if ($record = $records[$key] ?? null) {
                $values = $this->merge([$source => $values], $record)[$source];
            }
        }

        foreach (array_unique(['global', $path ?? 'global']) as $key) {
            if (! ($record = $records[$key] ?? null)) {
                continue;
            }
            foreach ($this->schema->imageSlots($key) as $slot => $definition) {
                $image = $record->getFirstMedia('page-'.$slot);
                if (! $image || ! $image->isPublic()) {
                    continue;
                }
                if ($source === 'media' && isset($definition['photo'])) {
                    $photo = $definition['photo'];
                    $values[$photo]['src'] = $image->getUrl();
                    $values[$photo]['smallSrc'] = $image->hasGeneratedConversion('thumb') ? $image->getUrl('thumb') : null;
                    $values[$photo]['width'] = $image->getCustomProperty('optimization.width', $values[$photo]['width']);
                    $values[$photo]['height'] = $image->getCustomProperty('optimization.height', $values[$photo]['height']);
                    $values[$photo]['credit'] = $values[$photo]['source'] = '';
                    $values[$photo]['generated'] = $values[$photo]['representative'] = false;
                }
                if (isset($definition['target']) && str_starts_with($definition['target'], $source.'.')) {
                    Arr::set($values, substr($definition['target'], strlen($source) + 1), $image->getUrl());
                }
            }
        }

        return $values;
    }

    private function merge(array $values, SeoSetting $record): array
    {
        $fields = $this->schema->fields($record->path);
        foreach (Arr::dot($record->content ?? []) as $key => $value) {
            // Ignore unknown/invalid legacy database values rather than sending design keys publicly.
            if (isset($fields[$key]) && array_key_exists(strtok($key, '.'), $values)
                && Validator::make(['value' => $value], ['value' => $this->schema->rules($fields[$key])])->passes()) {
                Arr::set($values, $key, $value);
            }
        }

        return $values;
    }
}
