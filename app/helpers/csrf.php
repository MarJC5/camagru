<?php

namespace Camagru\helpers;

/**
 * Class CSRF
 * Helper class for generating and verifying CSRF tokens.
 */
class CSRF
{
    /**
     * Generate a CSRF token and store it in the session.
     *
     * @param string $name The session key to store the token.
     * @return string The generated CSRF token.
     */
    public static function token($name = 'csrf_token')
    {
        $_SESSION[$name] = bin2hex(random_bytes(32));
        return $_SESSION[$name];
    }

    /**
     * Generate an HTML hidden input field with the CSRF token.
     *
     * @param string $name The session key to store the token.
     * @return string The HTML hidden input field.
     */
    public static function field($name = 'csrf_token')
    {
        return '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars(self::token($name), ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Verify a CSRF token against the stored session token.
     *
     * @param string $token The CSRF token to verify.
     * @param string $name The session key to store the token.
     * @return bool True if the token is valid, false otherwise.
     */
    public static function verify($token, $name = 'csrf_token')
    {
        if (!isset($_SESSION[$name]) || $_SESSION[$name] !== $token) {
            // Log this attempt or handle it accordingly
            error_log('CSRF token verification failed.');
            return false;
        }

        unset($_SESSION[$name]);
        return true;
    }

    /**
     * Check if a CSRF token is valid.
     *
     * @param string $token The CSRF token to check.
     * @param string $name The session key to store the token.
     * @return bool True if the token is valid, false otherwise.
     */
    public static function check($token, $name = 'csrf_token')
    {
        return isset($_SESSION[$name]) && $_SESSION[$name] === $token;
    }

    /**
     * Destroy the CSRF token in the session.
     *
     * @param string $name The session key to store the token.
     * @return void
     */
    public static function destroy($name = 'csrf_token')
    {
        unset($_SESSION[$name]);
    }

    /**
     * Regenerate a CSRF token.
     *
     * @return string The new CSRF token.
     */
    public static function regenerate()
    {
        self::destroy();
        return self::token();
    }

    /**
     * Alias for the check method.
     *
     * @param string $token The CSRF token to check.
     * @param string $name The session key to store the token.
     * @return bool True if the token is valid, false otherwise.
     */
    public static function is_valid($token, $name = 'csrf_token')
    {
        return self::check($token, $name);
    }

    /**
     * Generate a new CSRF token without storing it in the session.
     *
     * @return string The generated CSRF token.
     */
    public static function generate()
    {
        return bin2hex(random_bytes(32));
    }
}
