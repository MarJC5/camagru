<?php 

namespace Camagru\config;

use Camagru\helpers\Env;

return [
    'env' => Env::get('APP_ENV', 'production'),
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
    'collage' => [
        'size' => 3 * 1024 * 1024, // 3MB
        'allowed' => [
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ],
        'path' => 'storage/collages/',
    ],
    'mail' => [
        'host' => Env::get('MAIL_HOST', 'mailpit'),
        'port' => Env::get('MAIL_PORT', 1025),
        'username' => Env::get('MAIL_USERNAME', 'camagru'),
        'password' => Env::get('MAIL_PASSWORD', 'camagru'),
        'encryption' => Env::get('MAIL_ENCRYPTION', 'tls'),
        'from' => [
            'address' => Env::get('MAIL_FROM_ADDRESS', 'hello@camagru.local'),
            'name' => Env::get('MAIL_FROM_NAME', 'Camagru'),
        ],
    ]
];