<?php 

namespace Camagru\config;
use Camagru\helpers\Env;

return [
    'name' => Env::get('APP_NAME', 'Camagru'),
    'timezone' => Env::get('APP_TIMEZONE', 'UTC'),
    'locale' => Env::get('APP_LOCALE', 'fr'),
    'fallback_locale' => Env::get('APP_FALLBACK_LOCALE', 'en'),
];