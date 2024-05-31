<?php

namespace Camagru\helpers;

class CSRF {
    public static function token() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    public static function field() {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function verify($token) {
        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $token) {
            // Log this attempt or handle it accordingly
            error_log('CSRF token verification failed.');
            return false;
        }
    
        unset($_SESSION['csrf_token']);
        return true;
    }
}