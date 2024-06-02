<?php

namespace Camagru\helpers;

use Camagru\core\models\User;

class Session {
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }

    public static function destroy() {
        session_destroy();
    }

    public static function flash($key) {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }

    public static function has($key) {
        return isset($_SESSION[$key]);
    }

    public static function all() {
        return $_SESSION;
    }

    public static function clear() {
        $_SESSION = [];
    }

    public static function isLogged() {
        if (self::has('user')) {
            $user = User::find(self::get('user'));
            return $user ? true : false;
        }
    }

    public static function currentUser() {
        if (self::has('user')) {
            return new User(self::get('user'));
        }
    }

    public static function getCSRFToken() {
        return $_SESSION['csrf_token'] ?? null;
    }

    public static function setTempResetToken($token) {
        // keep the token for 10 minutes
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_token_expires'] = time() + 600;
    }

    public static function checkTempResetToken($token) {
        $reset_token = $_SESSION['reset_token'] ?? null;
        $reset_token_expires = $_SESSION['reset_token_expires'] ?? null;

        if (empty($reset_token) || empty($reset_token_expires) || $reset_token !== $token || time() > $reset_token_expires) {
            return false;
        }

        return true;
    }

    public static function removeTempResetToken() {
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_token_expires']);
    }
}