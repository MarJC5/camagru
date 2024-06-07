<?php

namespace Camagru\helpers;

class Sanitize {
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public static function escapeArray($array) {
        return array_map(function($value) {
            return self::escape($value);
        }, $array);
    }
}