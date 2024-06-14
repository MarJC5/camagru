<?php

namespace Camagru\helpers;

/**
 * Class Sanitize
 * Helper class for sanitizing strings and arrays.
 */
class Sanitize
{
    /**
     * Escape a string for safe output.
     *
     * @param string $string The string to escape.
     * @return string The escaped string.
     */
    public static function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Escape all strings in an array for safe output.
     *
     * @param array $array The array of strings to escape.
     * @return array The array with escaped strings.
     */
    public static function escapeArray($array)
    {
        return array_map(function($value) {
            return self::escape($value);
        }, $array);
    }
}
