<?php

namespace Camagru\helpers;

/**
 * Class Env
 * Helper class for loading and accessing environment variables from a file.
 */
class Env
{
    private static $variables = [];

    /**
     * Load environment variables from a file.
     *
     * @param string $filePath The path to the environment file.
     * @throws \Exception If the file does not exist.
     */
    public static function load($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Env file does not exist: {$filePath}");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Skip comments
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            self::$variables[$name] = $value;
        }
    }

    /**
     * Get an environment variable.
     *
     * @param string $key The key of the environment variable.
     * @param mixed $default The default value if the key does not exist.
     * @return mixed The value of the environment variable, or the default value.
     */
    public static function get($key, $default = null)
    {
        return array_key_exists($key, self::$variables) ? self::$variables[$key] : $default;
    }
}
