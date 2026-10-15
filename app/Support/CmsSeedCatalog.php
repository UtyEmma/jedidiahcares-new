<?php

namespace App\Support;

use Illuminate\Support\Str;
use InvalidArgumentException;

class CmsSeedCatalog
{
    public const RELATIONSHIPS = [
        'programmeId' => 'programmes', 'programmeIds' => 'programmes',
        'projectId' => 'projects', 'relatedProjectIds' => 'projects',
        'eventId' => 'events', 'reportIds' => 'reports',
    ];

    public function records(): array
    {
        $snapshot = json_decode(file_get_contents(database_path('seeders/data/website-content.json')), true, 512, JSON_THROW_ON_ERROR);
        $impact = CmsSchema::seed('impact');
        $result = [];
        $aliases = [];
        foreach (array_keys(CmsSchema::COLLECTIONS) as $collection) {
            $bundled = match ($collection) {
                'reports', 'metrics' => $impact[$collection] ?? [],
                'galleries' => CmsSchema::seed('gallery'),
                default => CmsSchema::seed($collection),
            };
            // Local PHP data wins when a record exists in both sources.
            foreach ([...($snapshot['collections'][$collection] ?? []), ...$bundled] as $record) {
                $record['slug'] ??= Str::slug($record['id']);
                $key = $record['id'];
                foreach ($result[$collection] ?? [] as $id => $existing) {
                    if ($existing['slug'] === $record['slug']) {
                        $key = $id;
                        break;
                    }
                }
                $merged = array_replace($result[$collection][$key] ?? [], $record);
                if ($key !== $record['id']) {
                    $aliases[$collection][$key] = $record['id'];
                    unset($result[$collection][$key]);
                }
                $result[$collection][$record['id']] = $merged;
            }
            $result[$collection] ??= [];
        }
        foreach ($result as $collection => $records) {
            foreach ($records as $id => $record) {
                foreach (self::RELATIONSHIPS as $field => $target) {
                    if (! isset($record[$field])) {
                        continue;
                    }
                    $resolve = function (string $relatedId) use ($aliases, $target): string {
                        while (isset($aliases[$target][$relatedId])) {
                            $relatedId = $aliases[$target][$relatedId];
                        }

                        return $relatedId;
                    };
                    $result[$collection][$id][$field] = is_array($record[$field]) ? array_map($resolve, $record[$field]) : $resolve($record[$field]);
                }
            }
        }

        return $result;
    }

    /** Include referenced source records, including circular programme/project links. */
    public function select(array $collections): array
    {
        $catalog = $this->records();
        $selected = [];
        $visit = function (string $collection, string $id) use (&$visit, &$selected, $catalog): void {
            if (isset($selected[$collection][$id]) || ! isset($catalog[$collection][$id])) {
                return;
            }
            $record = $catalog[$collection][$id];
            $selected[$collection][$id] = $record;
            foreach (self::RELATIONSHIPS as $field => $target) {
                foreach ((array) ($record[$field] ?? []) as $relatedId) {
                    $visit($target, $relatedId);
                }
            }
        };
        foreach ($collections as $collection) {
            if (! array_key_exists($collection, CmsSchema::COLLECTIONS)) {
                throw new InvalidArgumentException('Unknown seed collection: '.$collection);
            }
            foreach (array_keys($catalog[$collection]) as $id) {
                $visit($collection, $id);
            }
            $selected[$collection] ??= [];
        }

        return $selected;
    }
}
