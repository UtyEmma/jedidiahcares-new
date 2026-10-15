<?php

return [
    'pages' => [
        'home' => [
            'uri' => '/', 'component' => 'home/HomePage',
            'title' => 'Building the future, one person at a time',
            'description' => ['site', 'seo.description'],
        ],
        'about' => [
            'uri' => '/about', 'component' => 'about/AboutPage', 'title' => 'About us',
            'description' => ['pages', 'about.fields.Founding Narrative'],
        ],
        'programmes' => [
            'uri' => '/programmes', 'component' => 'programmes/ProgrammesPage', 'title' => 'Our programmes',
            'description' => ['pages', 'programmes.fields.Intro'],
        ],
        'projects' => [
            'uri' => '/projects', 'component' => 'projects/ProjectsPage', 'title' => 'Projects & campaigns',
            'description' => ['pages', 'projects.fields.Intro'],
        ],
        'reports' => [
            'uri' => '/reports', 'component' => 'reports/ReportsPage', 'title' => 'Our Reports',
            'description' => ['pages', 'impact.fields.Introduction'],
        ],
        'stories' => [
            'uri' => '/stories', 'component' => 'stories/StoriesPage', 'title' => 'Stories & updates',
            'description' => ['pages', 'stories.fields.Introduction'],
        ],
        'events' => [
            'uri' => '/events', 'component' => 'events/EventsPage', 'title' => 'Events & activities',
            'description' => ['pages', 'events.fields.Intro'],
        ],
        'get-involved' => [
            'uri' => '/get-involved', 'component' => 'get-involved/GetInvolvedPage', 'title' => 'Get involved',
            'description' => ['pages', 'get-involved.fields.Introduction'],
        ],
        'donations' => [
            'uri' => '/donations', 'component' => 'donations/DonationsPage', 'title' => 'Support our work',
            'description' => ['interface', 'support.intro'],
        ],
        'volunteers' => [
            'uri' => '/volunteers', 'component' => 'volunteers/VolunteersPage', 'title' => 'Volunteer with us',
            'description' => ['pages', 'volunteers.fields.Intro'],
        ],
        'partner' => [
            'uri' => '/partner', 'component' => 'partners/PartnersPage', 'title' => 'Partner with us',
            'description' => ['pages', 'partner.fields.Intro'],
        ],
        'team' => [
            'uri' => '/team', 'component' => 'team/TeamPage', 'title' => 'Our people',
            'description' => ['pages', 'team.fields.Intro'],
        ],
        'gallery' => [
            'uri' => '/gallery', 'component' => 'gallery/GalleryPage', 'title' => 'Photo gallery',
            'description' => ['pages', 'gallery.fields.Intro'],
        ],
        'contact' => [
            'uri' => '/contact', 'component' => 'contact/ContactPage', 'title' => 'Contact',
            'description' => ['pages', 'contact.fields.Intro'],
        ],
    ],
    'details' => [
        'programmes' => 'programme-detail/ProgrammeDetailPage',
        'projects' => 'project-detail/ProjectDetailPage',
        'stories' => 'story-detail/StoryDetailPage',
        'events' => 'event-detail/EventDetailPage',
        'people' => 'team-member/TeamMemberPage',
    ],
    'detail_paths' => ['people' => 'team'],
    'policies' => [
        'privacy' => ['title' => 'Privacy policy', 'component' => 'privacy/PrivacyPage'],
        'safeguarding' => ['title' => 'Safeguarding', 'component' => 'safeguarding/SafeguardingPage'],
        'donation-terms' => ['title' => 'Terms of use & giving', 'component' => 'donation-terms/DonationTermsPage'],
        'accessibility' => ['title' => 'Accessibility statement', 'component' => 'accessibility/AccessibilityPage'],
    ],
];
