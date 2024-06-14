<?php

namespace Camagru\core\middlewares;

use Camagru\routes\Router;
use Camagru\core\database\Runner;
use Camagru\helpers\Logger;
use Camagru\helpers\Session;

class Migration
{
    public static function ready($requestUri, $requestMethod)
    {
        // Check if the application has been migrated and redirect to the install page if not
        if (!Runner::isMigrated() && $requestUri !== '/setup') {
            // Redirect to the setup page
            Session::set('error', 'The application has not been migrated yet');
            return false;
        } else if (!Runner::isMigrated() && $requestUri === '/setup' && $requestMethod === 'GET') {
            // Show the installation page
            Session::set('error', 'The application has not been migrated yet');
            return false;
        }

        return true;
    }
}
