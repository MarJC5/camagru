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
}