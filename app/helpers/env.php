<?php

namespace Camagru\helpers;

class Env {
    private static $variables = [];

    public static function load($filePath) {
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

    public static function get($key, $default = null) {
        return array_key_exists($key, self::$variables) ? self::$variables[$key] : $default;
    }
}