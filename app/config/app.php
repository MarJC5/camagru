<?php 

namespace Camagru\config;

use Camagru\helpers\Env;

return [
    'site_url' => Env::get('APP_URL', 'https://localhost'),
    'name' => Env::get('APP_NAME', 'Camagru'),
    'timezone' => Env::get('APP_TIMEZONE', 'UTC'),
    'locale' => Env::get('APP_LOCALE', 'fr'),
    'fallback_locale' => Env::get('APP_FALLBACK_LOCALE', 'en'),
    'media' => [
        'size' => 3 * 1024 * 1024, // 3MB
        'allowed' => [
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ],
        'path' => 'storage/uploads/medias/',
    ],
];