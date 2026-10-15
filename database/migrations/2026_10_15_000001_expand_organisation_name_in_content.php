<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const FULL_NAME = 'Jedidiah Cares Global Outreach for Women, Youth and Children';

    public function up(): void
    {
        // Correct authored text without changing addresses, relationships, consent or audit fields.
        DB::table('content_entries')->orderBy('id')->chunkById(100, function ($entries): void {
            foreach ($entries as $entry) {
                $updates = [];
                foreach (['title' => 255, 'summary' => 3000] as $field => $limit) {
                    $value = $this->expand($entry->{$field});
                    if ($value !== $entry->{$field} && mb_strlen($value) <= $limit) {
                        $updates[$field] = $value;
                    }
                }
                $data = json_decode($entry->data ?: '[]', true, 512, JSON_THROW_ON_ERROR);
                $updated = $data;
                foreach (['role', 'biography', 'shortTitle', 'heroTitle', 'introduction', 'originalThematicName', 'activities', 'intendedActivities', 'sections', 'verifiedOutcomes', 'location', 'audience', 'author', 'period', 'label', 'unit', 'source', 'topic', 'question', 'answer', 'coverAlt'] as $field) {
                    if (array_key_exists($field, $data)) {
                        $updated[$field] = $this->expand($data[$field]);
                    }
                }
                foreach (['seoTitle' => 150, 'seoDescription' => 500] as $field => $limit) {
                    if (isset($data[$field])) {
                        $value = $this->expand($data[$field]);
                        // Retain compact metadata when expansion would exceed its field limit.
                        if (mb_strlen($value) <= $limit) {
                            $updated[$field] = $value;
                        }
                    }
                }
                if ($updated !== $data) {
                    $updates['data'] = json_encode($updated, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
                }
                if ($updates) {
                    DB::table('content_entries')->where('id', $entry->id)->update($updates);
                }
            }
        });

        DB::table('media')->orderBy('id')->chunkById(100, function ($items): void {
            foreach ($items as $item) {
                $properties = json_decode($item->custom_properties ?: '{}', true, 512, JSON_THROW_ON_ERROR);
                $updated = $properties;
                foreach (['caption', 'credit', 'alt', 'transcript'] as $field) {
                    if (array_key_exists($field, $properties)) {
                        $updated[$field] = $this->expand($properties[$field]);
                    }
                }
                if ($updated !== $properties) {
                    DB::table('media')->where('id', $item->id)->update(['custom_properties' => json_encode($updated, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)]);
                }
            }
        });

        DB::table('seo_settings')->orderBy('id')->chunkById(100, function ($settings): void {
            foreach ($settings as $setting) {
                $description = $this->expand($setting->description);
                if ($description !== $setting->description && mb_strlen($description) <= 500) {
                    DB::table('seo_settings')->where('id', $setting->id)->update(['description' => $description]);
                }
            }
        });
    }

    private function expand(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => $this->expand($item), $value);
        }
        if (! is_string($value)) {
            return $value;
        }
        $expanded = preg_replace('/\bJedidiah Cares(?: Global Outreach)?(?: for Women, Youth and Children)?\b/iu', self::FULL_NAME, $value) ?? $value;

        return str_replace([self::FULL_NAME.'’ ', self::FULL_NAME."' "], [self::FULL_NAME.'’s ', self::FULL_NAME."'s "], $expanded);
    }

    public function down(): void
    {
        // This name correction remains in place on rollback to preserve subsequent editorial work.
    }
};
