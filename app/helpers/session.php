<?php

namespace Camagru\helpers;

use Camagru\core\models\User;

/**
 * Class Session
 * Helper class for managing session data and user authentication.
 */
class Session
{
    /**
     * Start the session if it hasn't been started already.
     */
    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a session variable.
     *
     * @param string $key The key for the session variable.
     * @param mixed $value The value to set.
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session variable.
     *
     * @param string $key The key for the session variable.
     * @return mixed|null The session variable value, or null if not set.
     */
    public static function get($key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Destroy the session.
     */
    public static function destroy()
    {
        // Start the session if it hasn't been started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Clear all session data
        $_SESSION = array();
        
        // If a session cookie is used, destroy it
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
    }

    /**
     * Get and delete a session variable.
     *
     * @param string $key The key for the session variable.
     * @return mixed|null The session variable value, or null if not set.
     */
    public static function flash($key)
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }

    /**
     * Check if a session variable is set.
     *
     * @param string $key The key for the session variable.
     * @return bool True if the session variable is set, false otherwise.
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Get all session variables.
     *
     * @return array All session variables.
     */
    public static function all()
    {
        return $_SESSION;
    }

    /**
     * Clear all session variables.
     */
    public static function clear()
    {
        $_SESSION = [];
    }

    /**
     * Check if a user is logged in.
     *
     * @return bool True if a user is logged in, false otherwise.
     */
    public static function isLogged()
    {
        if (self::has('user')) {
            $user = User::find(self::get('user'));
            return $user ? true : false;
        }
        return false;
    }

    /**
     * Get the currently logged-in user.
     *
     * @return User|null The current user, or null if not logged in.
     */
    public static function currentUser()
    {
        if (self::has('user')) {
            return new User(self::get('user'));
        }
        return null;
    }

    /**
     * Get the CSRF token from the session.
     *
     * @return string|null The CSRF token, or null if not set.
     */
    public static function getCSRFToken()
    {
        return $_SESSION['csrf_token'] ?? null;
    }

    /**
     * Set a temporary reset token in the session.
     *
     * @param string $token The reset token.
     */
    public static function setTempResetToken($token)
    {
        // Keep the token for 10 minutes
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_token_expires'] = time() + 600;
    }

    /**
     * Check if a temporary reset token is valid.
     *
     * @param string $token The reset token to check.
     * @return bool True if the reset token is valid, false otherwise.
     */
    public static function checkTempResetToken($token)
    {
        $reset_token = $_SESSION['reset_token'] ?? null;
        $reset_token_expires = $_SESSION['reset_token_expires'] ?? null;

        if (empty($reset_token) || empty($reset_token_expires) || $reset_token !== $token || time() > $reset_token_expires) {
            return false;
        }

        return true;
    }

    /**
     * Remove the temporary reset token from the session.
     */
    public static function removeTempResetToken()
    {
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_token_expires']);
    }
}
