<?php

return [
    'primary' => [
        [
            'label' => 'About Us',
            'href' => route('about', absolute: false),
            'children' => [
                [
                    'label' => 'Our story',
                    'href' => route('about', absolute: false),
                ],
                [
                    'label' => 'Mission & vision',
                    'href' => route('about', absolute: false).'#mission',
                ],
                [
                    'label' => 'Our people',
                    'href' => route('team', absolute: false),
                ],
            ],
        ],
        [
            'label' => 'Our Programmes',
            'href' => route('programmes', absolute: false),
            'children' => [
                [
                    'label' => 'Explore our programmes',
                    'href' => route('programmes', absolute: false),
                ],
                [
                    'label' => 'Education & Scholarships',
                    'href' => route('programmes.show', ['slug' => 'education'], absolute: false),
                ],
                [
                    'label' => 'Youth Development & Drug Abuse Prevention',
                    'href' => route('programmes.show', ['slug' => 'youth-drug-prevention'], absolute: false),
                ],
                [
                    'label' => 'Girls’ Health, Safety & Empowerment',
                    'href' => route('programmes.show', ['slug' => 'girls-health'], absolute: false),
                ],
                [
                    'label' => 'Women & Community Empowerment',
                    'href' => route('programmes.show', ['slug' => 'community-empowerment'], absolute: false),
                ],
            ],
        ],
        [
            'label' => 'Our Impact',
            'href' => route('reports', absolute: false),
            'children' => [
                [
                    'label' => 'Our Reports',
                    'href' => route('reports', absolute: false),
                ],
                [
                    'label' => 'Projects',
                    'href' => route('projects', absolute: false),
                ],
                [
                    'label' => 'Stories & Updates',
                    'href' => route('stories', absolute: false),
                ],
                [
                    'label' => 'Events & Activities',
                    'href' => route('events', absolute: false),
                ],
                [
                    'label' => 'Photo Gallery',
                    'href' => route('gallery', absolute: false),
                ],
            ],
        ],
        [
            'label' => 'Get Involved',
            'href' => route('get-involved', absolute: false),
            'children' => [
                [
                    'label' => 'Ways to get involved',
                    'href' => route('get-involved', absolute: false),
                ],
                [
                    'label' => 'Support our work',
                    'href' => route('donations', absolute: false),
                ],
                [
                    'label' => 'Volunteer',
                    'href' => route('volunteers', absolute: false),
                ],
                [
                    'label' => 'Partner with us',
                    'href' => route('partner', absolute: false),
                ],
                [
                    'label' => 'Host a programme',
                    'href' => route('contact', ['topic' => 'Host a programme'], absolute: false),
                ],
            ],
        ],
        [
            'label' => 'Contact',
            'href' => route('contact', absolute: false),
        ],
    ],
    'footer' => [
        [
            'title' => 'Explore',
            'links' => [
                [
                    'label' => 'About us',
                    'href' => route('about', absolute: false),
                ],
                [
                    'label' => 'Our programmes',
                    'href' => route('programmes', absolute: false),
                ],
                [
                    'label' => 'Projects & campaigns',
                    'href' => route('projects', absolute: false),
                ],
                [
                    'label' => 'Our Reports',
                    'href' => route('reports', absolute: false),
                ],
                [
                    'label' => 'Stories & updates',
                    'href' => route('stories', absolute: false),
                ],
            ],
        ],
        [
            'title' => 'Make a difference',
            'links' => [
                [
                    'label' => 'Support our work',
                    'href' => route('donations', absolute: false),
                ],
                [
                    'label' => 'Volunteer with us',
                    'href' => route('volunteers', absolute: false),
                ],
                [
                    'label' => 'Partner with us',
                    'href' => route('partner', absolute: false),
                ],
                [
                    'label' => 'Contact',
                    'href' => route('contact', absolute: false),
                ],
            ],
        ],
    ],
    'policies' => [
        [
            'label' => 'Privacy',
            'href' => route('privacy', absolute: false),
        ],
        [
            'label' => 'Safeguarding',
            'href' => route('safeguarding', absolute: false),
        ],
        [
            'label' => 'Terms & giving',
            'href' => route('donation-terms', absolute: false),
        ],
        [
            'label' => 'Accessibility',
            'href' => route('accessibility', absolute: false),
        ],
    ],
];
