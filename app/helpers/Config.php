<?php

namespace Camagru\helpers;

class Config
{
    protected static $config = [];

    public static function load($file)
    {
        self::$config = require $file;
    }

    public static function get($key)
    {
        return self::$config[$key] ?? null;
    }
}
