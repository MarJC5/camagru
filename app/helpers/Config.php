<?php

namespace Camagru\helpers;

/**
 * Class Config
 * Helper class to manage configuration settings.
 */
class Config
{
    protected static $config = [];

    /**
     * Load the configuration file.
     *
     * @param string $file The path to the configuration file.
     * @return void
     */
    public static function load($file)
    {
        self::$config = require $file;
    }

    /**
     * Get a configuration value by key.
     *
     * @param string $key The key of the configuration value.
     * @return mixed|null The configuration value, or null if the key does not exist.
     */
    public static function get($key)
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return null;
            }
        }

        return $value;
    }
}
