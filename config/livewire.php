<?php

return [
    'temporary_file_upload' => [
        'disk' => 'local',
        'rules' => ['required', 'file', 'max:102400'],
        'directory' => 'livewire-tmp',
        'middleware' => 'throttle:60,1',
        'max_upload_time' => 30,
        'cleanup' => true,
    ],
];
