<?php

return [

    'avatar' => [

        'disk' => env('IMAGE_USER_DISK', 'avatars'),
        'dir' => env('IMAGE_USER_DIR', '/images/avatars'),

    ],

    'image_no_available_url' => env('NO_IMAGE_AVAILABLE_URL','https://placehold.it/128x128'),

];
