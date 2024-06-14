<?php

namespace Camagru\core\middlewares;

use Camagru\routes\Router;
use Camagru\core\database\Runner;
use Camagru\helpers\Session;

/**
 * Class Migration
 * Middleware to handle migration checks and ensure the application is ready.
 */
class Migration
{
    /**
     * Check if the application is migrated and handle redirection if not.
     *
     * @param string $requestUri The current request URI.
     * @param string $requestMethod The current request method.
     * @return bool True if the application is migrated and ready, false otherwise.
     */
    public static function ready($requestUri, $requestMethod)
    {
        // Check if the application has been migrated
        if (!Runner::isMigrated()) {
            // If the application is not migrated and the request is not for the setup page, redirect to the setup page
            if ($requestUri !== '/setup') {
                Session::set('error', 'The application has not been migrated yet');
                Router::redirect('setup');
                return false;
            }

            // If the request is for the setup page and the method is GET, allow the request to proceed
            if ($requestUri === '/setup' && $requestMethod === 'GET') {
                Session::set('error', 'The application has not been migrated yet');
                return true;
            }
        }

        return true;
    }
}
