<?php

namespace Camagru\helpers;

class CSRF {
    public static function token($name = 'csrf_token') {
        $_SESSION[$name] = bin2hex(random_bytes(32));
        return $_SESSION[$name];
    }

    public static function field($name = 'csrf_token') {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token($name), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function verify($token, $name = 'csrf_token') {
        if (!isset($_SESSION[$name]) || $_SESSION[$name] !== $token) {
            // Log this attempt or handle it accordingly
            error_log('CSRF token verification failed.');
            return false;
        }
    
        unset($_SESSION[$name]);
        return true;
    }

    public static function check($token, $name = 'csrf_token') {
        return isset($_SESSION[$name]) && $_SESSION[$name] === $token;
    }

    public static function destroy($name = 'csrf_token') {
        unset($_SESSION[$name]);
    }

    public static function regenerate() {
        self::destroy();
        return self::token();
    }

    public static function is_valid($token, $name = 'csrf_token') {
        return self::check($token, $name);
    }

    public static function generate() {
        return bin2hex(random_bytes(32));
    }
}