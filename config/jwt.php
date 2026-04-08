<?php

return [
    'key' => env('JWT_KEY'),
    'alg' => env('JWT_ALG', 'HS256'),
    'cookie' => [
        'name' => env('JWT_REFRESH_COOKIE_NAME', 'refresh_token'),
        'domain' => env('JWT_REFRESH_COOKIE_DOMAIN'),
        'secure' => env('JWT_REFRESH_COOKIE_SECURE', true),
        'same_site' => env('JWT_REFRESH_SAME_SITE', 'none'),
    ],
];
