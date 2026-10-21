<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** An allowlist of content slots in the existing templates, never a page builder. */
class PageContentSchema
{
    private ?array $sources = null;

    private array $fieldDefinitions = [];

    public function sources(): array
    {
        return $this->sources ??= [
            'pages' => CmsSchema::seed('pages'), 'site' => CmsSchema::seed('site'),
            'interface' => CmsSchema::seed('interface'), 'media' => CmsSchema::seed('media'),
            'policies' => CmsSchema::seed('policies'), 'sections' => CmsSchema::seed('page-sections'),
        ];
    }

    public static function requestPath(Request $request): string
    {
        if ($key = $request->route('pageKey')) {
            return config("website.pages.{$key}.uri");
        }
        if ($slug = $request->route('policySlug')) {
            return '/'.$slug;
        }
        if ($request->route('projectSlug')) {
            return config('website.pages.donations.uri');
        }

        return $request->getPathInfo();
    }

    public function pageKey(string $path): ?string
    {
        foreach (config('website.pages') as $key => $page) {
            if ($page['uri'] === $path) {
                return $key;
            }
        }

        return null;
    }

    /** Section names and order are authored here and cannot be changed in the CMS. */
    public function groups(string $path): array
    {
        $cta = ['site.home.finalTitle', 'site.home.finalText', 'sections.shared.ctaEyebrow', 'sections.shared.ctaButton', 'sections.shared.ctaHref'];
        $founder = array_map(fn ($key) => 'site.founderNote.'.$key, ['label', 'headline', 'text', 'name', 'role', 'portraitAlt', 'motifLines']);
        $founder[] = 'sections.shared.founderButton';
        $partner = ['site.partnerShowcase'];
        if ($path === 'global') {
            return [
                'Shared call to action' => $cta,
                'Shared founder’s note' => $founder,
                'Shared partner invitations' => $partner,
                'Shared involvement cards' => ['site.involvement'],
                'Footer and top bar' => ['site.footer', 'site.topBar'],
                'Enquiry and FAQ introductions' => ['interface.actions.emailTitle', 'interface.actions.emailText', 'interface.faq', 'site.contact.socialHeading', 'site.contact.socialText', 'site.contact.unavailable'],
                'Collection empty states' => ['site.empty'],
            ];
        }

        $key = $this->pageKey($path);
        $groups = match ($key) {
            'home' => [
                'Hero' => [...array_map(fn ($key) => 'site.home.'.$key, ['eyebrow', 'heroLines', 'heroText', 'heroAside', 'artCaption']), 'interface.home.scroll'],
                'Values strip' => ['site.home.values'],
                'Who we are' => ['site.home.introEyebrow', 'site.home.introHeadline', 'site.home.introText', 'interface.home.faithLine', 'interface.home.careLine'],
                'Founder’s note' => $founder,
                'Programmes introduction' => ['site.home.programmeEyebrow', 'site.home.programmeHeadline', 'site.home.programmeText', 'interface.home.programmeNoteEyebrow', 'interface.home.programmeNoteTitle', 'interface.home.programmeNoteText'],
                'Education feature' => ['site.home.featureEyebrow', 'site.home.featureTitle', 'site.home.featureText'],
                'Accountability' => ['site.home.evidenceEyebrow', 'site.home.evidenceTitle', 'site.home.evidenceText', 'interface.home.evidenceNoteTitle', 'interface.home.evidenceNoteText'],
                'Get involved' => ['site.home.involvementEyebrow', 'site.home.involvementTitle', 'site.home.involvementText', 'site.involvement'],
                'Partner invitations' => $partner,
            ],
            'about' => [
                'Hero' => ['pages.about.headline', 'pages.about.fields.Founding Narrative'],
                'Founder’s note' => $founder,
                'Who we serve' => ['interface.about.introTitle', 'pages.about.fields.Who We Serve', 'site.home.introText'],
                'Mission and vision' => ['interface.about.missionTitle', 'site.about.mission', 'site.about.vision', 'site.about.visionStatus'],
                'Our values' => ['interface.about.valuesTitle', 'interface.about.valuesText', 'site.about.values'],
                'Our approach' => ['interface.about.approachTitle', 'pages.about.fields.Our Approach', 'site.about.approach'],
            ],
            'programmes' => [
                'Hero' => ['pages.programmes.headline', 'pages.programmes.fields.Intro'],
                'Hosting invitation' => ['interface.programme.hostTitle', 'pages.programmes.fields.Closing Invitation'],
            ],
            'projects' => ['Hero' => ['pages.projects.headline', 'pages.projects.fields.Intro'], 'Empty state' => ['site.empty.projects']],
            'stories' => ['Hero' => ['pages.stories.headline', 'pages.stories.fields.Introduction'], 'Empty state' => ['site.empty.stories']],
            'events' => ['Hero' => ['pages.events.headline', 'pages.events.fields.Intro'], 'Empty state' => ['site.empty.events']],
            'reports' => [
                'Hero' => ['pages.impact.headline', 'pages.impact.fields.Introduction'],
                'Accountability' => ['interface.impact.evidenceTitle', 'pages.impact.fields.Reflection'],
                'Progress and reporting' => ['interface.impact.progressTitle', 'interface.impact.progressText', 'interface.impact.reportsTitle', 'interface.impact.projectsTitle'],
                'Empty state' => ['site.empty.reports'],
            ],
            'get-involved' => [
                'Hero' => ['pages.get-involved.headline', 'pages.get-involved.fields.Introduction'],
                'Ways to get involved' => ['site.involvement'],
                'Hosting a programme' => ['interface.getInvolved.hostTitle', 'pages.programmes.fields.Closing Invitation'],
            ],
            'donations' => [
                'Hero' => ['pages.donations.headline', 'interface.support.intro'],
                'Purpose and causes' => ['interface.support.purposeTitle', 'site.home.introText', 'interface.support.causes', 'interface.support.allocationNote'],
                'Giving information' => ['interface.actions.givingTitle', 'interface.actions.handoff', 'site.giving.unavailable'],
                'Other ways to help' => ['interface.support.otherTitle', 'interface.support.otherText'],
            ],
            'volunteers' => [
                'Hero' => ['pages.volunteers.headline', 'pages.volunteers.fields.Intro'],
                'Volunteer roles' => ['interface.volunteer.rolesTitle', 'pages.volunteers.fields.What Volunteering Involves', 'site.volunteering.roles'],
                'Your next step' => ['interface.volunteer.nextTitle', 'site.volunteering.steps', 'interface.volunteer.safeguarding'],
                'Enquiry' => ['interface.volunteer.externalTitle', 'interface.volunteer.externalText', 'site.volunteering.externalInterestFormUrl', 'interface.actions.emailTitle', 'interface.actions.emailText'],
            ],
            'partner' => [
                'Hero' => ['pages.partner.headline', 'pages.partner.fields.Intro'],
                'Partnership possibilities' => ['interface.partner.introTitle', 'pages.partner.fields.Partnership Possibilities', 'site.partnership.options'],
                'How we collaborate' => ['interface.partner.approachTitle', 'pages.partner.fields.How We Collaborate', 'site.partnership.steps'],
                'Enquiry' => ['interface.partner.externalTitle', 'interface.partner.externalText', 'site.partnership.externalEnquiryFormUrl', 'interface.actions.emailTitle', 'interface.actions.emailText'],
                'Partner invitations' => $partner,
            ],
            'team' => ['Hero' => ['pages.team.headline', 'pages.team.fields.Intro'], 'Empty state' => ['site.empty.team']],
            'gallery' => ['Hero' => ['pages.gallery.headline', 'pages.gallery.fields.Intro'], 'Empty state' => ['site.empty.gallery']],
            'contact' => [
                'Hero' => ['pages.contact.headline', 'pages.contact.fields.Intro'],
                'Contact introduction' => ['interface.contact', 'site.contact.unavailable'],
                'Social messages' => ['site.contact.socialHeading', 'site.contact.socialText'],
                'Enquiry form' => ['interface.actions.emailTitle', 'interface.actions.emailText'],
            ],
            default => [],
        };
        if ($key) {
            $groups['Section labels and buttons'] = ['sections.'.$key];
            if (! in_array($key, ['contact', 'donations', 'programmes'], true)) {
                $groups['Closing invitation'] = $cta;
            }
            if (in_array($key, ['programmes', 'volunteers', 'donations'], true)) {
                $groups['FAQ introduction'] = ['interface.faq'];
            }
        }
        foreach ($this->imageSlots($path) as $slot) {
            $groups[$slot['group']][] = $slot['alt'];
        }
        foreach ($this->sources()['policies'] as $index => $policy) {
            if ($path === '/'.$policy['slug']) {
                $groups['Page introduction'] = ["policies.{$index}.title", "policies.{$index}.summary", "policies.{$index}.updatedAt"];
                foreach ($policy['sections'] as $section => $values) {
                    $groups[$values['heading']] = ["policies.{$index}.sections.{$section}"];
                }
                $groups['Contact invitation'] = ["policies.{$index}.contact"];
            }
        }

        return $groups;
    }

    public function imageSlots(string $path): array
    {
        $founder = ['group' => 'Founder’s note', 'target' => 'site.founderNote.portraitPath', 'alt' => 'site.founderNote.portraitAlt'];
        $site = $this->sources()['site'];

        return match ($this->pageKey($path) ?? $path) {
            'global' => ['founder' => [...$founder, 'group' => 'Shared founder’s note']],
            'home' => [
                'hero' => ['group' => 'Hero', 'photo' => $site['home']['heroPhotoId'], 'alt' => 'media.'.$site['home']['heroPhotoId'].'.alt'],
                'feature' => ['group' => 'Education feature', 'photo' => $site['home']['featurePhotoId'], 'alt' => 'media.'.$site['home']['featurePhotoId'].'.alt'],
                'founder' => $founder,
            ],
            'about' => [
                'hero' => ['group' => 'Hero', 'photo' => 'community', 'alt' => 'media.community.alt'],
                'approach' => ['group' => 'Our approach', 'photo' => 'care', 'alt' => 'media.care.alt'],
                'founder' => $founder,
            ],
            'volunteers' => ['hero' => ['group' => 'Hero', 'photo' => 'care', 'alt' => 'media.care.alt']],
            default => [],
        };
    }

    public function fields(string $path): array
    {
        if (isset($this->fieldDefinitions[$path])) {
            return $this->fieldDefinitions[$path];
        }
        $fields = [];
        foreach ($this->groups($path) as $group => $prefixes) {
            foreach ($prefixes as $prefix) {
                $value = data_get($this->sources(), $prefix);
                $leaves = is_array($value) ? Arr::dot([$prefix => $value]) : [$prefix => $value];
                foreach ($leaves as $key => $default) {
                    // Never expose identifiers, media source paths, structural flags or decorative choices.
                    if (! is_string($default) || in_array(Str::afterLast($key, '.'), ['icon', 'illustration', 'source', 'id', 'slug'], true)) {
                        continue;
                    }
                    $type = str_ends_with($key, 'Url') ? 'external-url' : (preg_match('/(href|url)$/i', $key) ? 'link' : (str_ends_with($key, '.updatedAt') ? 'date' : 'text'));
                    $long = strlen($default) > 120 || preg_match('/(text|introduction|summary|narrative|mission|vision|paragraphs)/i', $key);
                    $label = implode(' · ', array_map(fn ($part) => ctype_digit((string) $part) ? 'Item '.((int) $part + 1) : Str::headline($part), array_slice(explode('.', $key), 2)));
                    $required = (bool) preg_match('/(\.alt|\.portraitAlt|\.headline|\.heroLines\.\d+|\.title|\.heading)$/', $key);
                    $fields[$key] = compact('group', 'default', 'type', 'long', 'label', 'required') + ['max' => $type === 'text' ? ($long ? 15000 : 500) : 2048];
                }
            }
        }

        return $this->fieldDefinitions[$path] = $fields;
    }

    public function validLink(mixed $value, bool $externalOnly = false): bool
    {
        return is_string($value) && ! preg_match('/[\x00-\x20\\\\]/', $value)
            && (($externalOnly === false && str_starts_with($value, '/') && ! str_starts_with($value, '//'))
                || (filter_var($value, FILTER_VALIDATE_URL) && parse_url($value, PHP_URL_SCHEME) === 'https'));
    }

    public function rules(array $field): array
    {
        $rules = [$field['required'] ? 'required' : 'present', 'string', 'max:'.$field['max']];
        if ($field['type'] === 'date') {
            $rules[] = 'date_format:Y-m-d';
        }
        if (in_array($field['type'], ['link', 'external-url'], true)) {
            $rules[] = function ($attribute, $value, $fail) use ($field): void {
                if ($value !== '' && ! $this->validLink($value, $field['type'] === 'external-url')) {
                    $fail('Use an HTTPS URL'.($field['type'] === 'link' ? ' or an internal path beginning with /.' : '.'));
                }
            };
        }

        return $rules;
    }

    public function validate(string $path, array $content): array
    {
        $fields = $this->fields($path);
        $errors = [];
        foreach (Arr::dot($content) as $key => $value) {
            if (! isset($fields[$key])) {
                $errors['content.'.$key] = 'This content or design setting is not editable on this page.';

                continue;
            }
            $validator = Validator::make(['value' => $value], ['value' => $this->rules($fields[$key])]);
            if ($validator->fails()) {
                $errors['content.'.$key] = $validator->errors()->first();
            }
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return $content;
    }
}
