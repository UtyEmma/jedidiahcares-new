<?php

return [
    [
        'from' => '/blog',
        'route' => 'stories',
    ],
    [
        'from' => '/blog/:slug',
        'route' => 'stories.show',
    ],
    [
        'from' => '/impact',
        'route' => 'reports',
    ],
];
