<?php

namespace App\Support;

use App\Models\ContentEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

/** Shared collection definitions keep admin validation and public serialization aligned. */
class CmsSchema
{
    public const PROJECT_STAGES = ['planned' => 'Upcoming', 'active' => 'Ongoing', 'completed' => 'Completed'];

    public const FUNDING_CURRENCIES = ['NGN' => 'NGN — Nigerian naira', 'USD' => 'USD — US dollar', 'GBP' => 'GBP — British pound', 'EUR' => 'EUR — Euro'];

    public const PEOPLE_SOCIAL_PLATFORMS = ['linkedin' => 'LinkedIn', 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'x' => 'X', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'website' => 'Personal website'];

    public const COLLECTIONS = [
        'people' => 'Our People', 'programmes' => 'Programmes', 'projects' => 'Projects',
        'reports' => 'Reports', 'stories' => 'Stories & Updates', 'events' => 'Events',
        'galleries' => 'Galleries', 'partners' => 'Partners',
        'faq' => 'FAQs', 'metrics' => 'Impact Metrics',
    ];

    public const FIELDS = [
        'people' => ['role', 'group', 'biography', 'portraitConsent', 'email', 'socialLinks', 'coverAlt'],
        'programmes' => ['originalThematicName', 'shortTitle', 'heroTitle', 'introduction', 'photoId', 'sections', 'activities', 'relatedProjectIds', 'coverAlt'],
        'projects' => ['programmeIds', 'stage', 'location', 'startDate', 'intendedActivities', 'sections', 'verifiedOutcomes', 'reportIds', 'approvedDonationUrl', 'fundingTarget', 'fundsRaised', 'fundingCurrency', 'coverAlt'],
        'reports' => ['period', 'publishedAt', 'projectId', 'coverAlt'],
        'stories' => ['category', 'publishedAt', 'programmeId', 'projectId', 'author', 'blocks', 'coverAlt'],
        'events' => ['startDate', 'endDate', 'timezone', 'location', 'audience', 'registrationUrl', 'sections', 'programmeIds', 'coverAlt'],
        'galleries' => ['projectId', 'eventId'],
        'partners' => ['url', 'mediaRightsApproved', 'coverAlt'],
        'faq' => ['topic', 'question', 'answer'],
        'metrics' => ['label', 'value', 'unit', 'period', 'source', 'verified'],
    ];

    public static function seed(string $file): array
    {
        if (! preg_match('/^[a-z][a-z-]*$/D', $file) || ! is_file($path = resource_path("data/{$file}.php"))) {
            throw new InvalidArgumentException('Unknown website data file.');
        }

        $data = require $path;
        if (! is_array($data)) {
            throw new InvalidArgumentException('Website data files must return an array.');
        }

        return $data;
    }

    public static function validate(ContentEntry $entry): void
    {
        $rules = [
            'collection' => ['required', Rule::in(array_keys(self::COLLECTIONS))],
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:200', 'regex:/^[a-z0-9]+(?:[-:][a-z0-9]+)*$/', Rule::unique('content_entries')->where('collection', $entry->collection)->ignore($entry->id)],
            'summary' => 'nullable|string|max:3000',
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'sort_order' => 'required|integer|min:0',
            'data' => 'array',
            'data.coverAlt' => 'nullable|string|max:500',
            'data.seoTitle' => 'nullable|string|max:150',
            'data.seoDescription' => 'nullable|string|max:500',
        ];
        $required = $entry->status === 'published' ? 'required' : 'nullable';
        if ($entry->collection === 'galleries') {
            $rules['summary'] = "$required|string|max:3000";
        }
        $fields = match ($entry->collection) {
            'people' => ['role' => "$required|string|max:255", 'biography' => "$required|string", 'group' => ['required', Rule::in(['leadership', 'team', 'volunteers'])], 'portraitConsent' => 'boolean'],
            'programmes' => ['shortTitle' => "$required|string", 'heroTitle' => "$required|string", 'introduction' => "$required|string", 'activities' => 'array', 'sections' => 'array', 'relatedProjectIds' => 'array'],
            'projects' => [
                'programmeIds' => 'array', 'stage' => ['required', Rule::in(array_keys(self::PROJECT_STAGES))], 'startDate' => 'nullable|date',
                'intendedActivities' => 'array', 'sections' => 'array', 'verifiedOutcomes' => 'array', 'reportIds' => 'array', 'approvedDonationUrl' => 'nullable|url:https',
                'fundingTarget' => 'nullable|numeric|gt:0|max:999999999999.99|decimal:0,2',
                'fundsRaised' => 'nullable|numeric|min:0|max:999999999999.99|decimal:0,2',
                'fundingCurrency' => ['nullable', Rule::in(array_keys(self::FUNDING_CURRENCIES))],
            ],
            'reports' => ['period' => "$required|string", 'publishedAt' => "$required|date"],
            'stories' => ['category' => "$required|string", 'publishedAt' => "$required|date", 'blocks' => 'array'],
            'events' => ['startDate' => "$required|date", 'endDate' => 'nullable|date|after_or_equal:data.startDate', 'timezone' => 'required|timezone', 'location' => "$required|string", 'audience' => "$required|string", 'sections' => 'array', 'programmeIds' => 'sometimes|array', 'registrationUrl' => 'nullable|url:https'],
            'partners' => ['url' => 'nullable|url:https', 'mediaRightsApproved' => 'boolean'],
            'faq' => ['topic' => "$required|string", 'question' => "$required|string", 'answer' => "$required|string"],
            'metrics' => ['label' => "$required|string", 'value' => "$required|numeric|min:0", 'period' => "$required|string", 'source' => "$required|string", 'verified' => $entry->status === 'published' ? 'accepted' : 'boolean'],
            default => [],
        };
        foreach ($fields as $key => $rule) {
            $rules['data.'.$key] = $rule;
        }
        if ($entry->collection === 'people') {
            $rules['data.email'] = 'nullable|email:rfc|max:254';
            $rules['data.socialLinks'] = 'sometimes|nullable|array:'.implode(',', array_keys(self::PEOPLE_SOCIAL_PLATFORMS));
            foreach (array_keys(self::PEOPLE_SOCIAL_PLATFORMS) as $platform) {
                $rules['data.socialLinks.'.$platform] = 'nullable|url:https|max:2048';
            }
        }
        foreach (['projectId' => 'projects', 'eventId' => 'events', 'programmeId' => 'programmes'] as $key => $collection) {
            $rules['data.'.$key] = ['nullable', Rule::exists('content_entries', 'id')->where('collection', $collection)->whereNull('deleted_at')];
        }
        foreach (['programmeIds' => 'programmes', 'relatedProjectIds' => 'projects', 'reportIds' => 'reports'] as $key => $collection) {
            $rules['data.'.$key.'.*'] = ['string', Rule::exists('content_entries', 'id')->where('collection', $collection)->whereNull('deleted_at')];
        }
        foreach (['sections', 'verifiedOutcomes', 'blocks'] as $key) {
            $rules['data.'.$key] = 'sometimes|array';
        }
        $rules += [
            'data.sections.*.heading' => 'required|string|max:255',
            'data.sections.*.text' => 'nullable|string',
            'data.sections.*.paragraphs' => 'sometimes|array',
            'data.sections.*.paragraphs.*' => 'string',
            'data.sections.*.items' => 'sometimes|array',
            'data.sections.*.items.*' => 'string',
            'data.sections.*.links.*.label' => 'required|string',
            'data.sections.*.links.*.url' => 'required|url:https',
            'data.activities.*' => 'string', 'data.intendedActivities.*' => 'string',
            'data.verifiedOutcomes.*.description' => 'required|string',
            'data.verifiedOutcomes.*.source' => 'required|string',
            'data.verifiedOutcomes.*.verified' => 'boolean',
            'data.blocks.*.type' => ['required', Rule::in(['paragraph', 'heading', 'quote', 'bullets'])],
            'data.blocks.*.data.text' => 'sometimes|string',
            'data.blocks.*.data.items.*' => 'string',
        ];
        if ($entry->collection === 'stories' && $entry->status === 'published') {
            $rules['data.blocks'] = 'required|array|min:1';
        }
        $values = $entry->attributesToArray();
        $validator = Validator::make($values, $rules);
        $validator->after(function ($validator) use ($entry): void {
            if (DB::table('content_redirects')->where('collection', $entry->collection)->where('slug', $entry->slug)->where('entry_id', '!=', $entry->id)->exists()) {
                $validator->errors()->add('slug', 'This address is reserved by another record.');
            }
        });
        $validator->validate();
    }
}
