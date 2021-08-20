<?php

return [

    'namespace' => env('MEDIA_MANAGER_NAMESPACE', 'media-manager'),

    'thumb_size' => [
        'width' => 368,
        'height' => 232,
    ],
    'auth_middleware' => [
        'admin'    => [
            'middleware' => '',
            'except'     => [],
        ]
    ]
];
