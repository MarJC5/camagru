<?php

namespace Camagru\core\middlewares;

use Camagru\helpers\Logger;
use Camagru\routes\Router;
use Camagru\helpers\Session;

/**
 * Class Auth
 * Middleware to handle authentication and authorization checks.
 */
class Auth
{
    /**
     * Handle the authentication and authorization checks.
     *
     * @param string $roles The roles or conditions required for access (e.g., 'admin', 'self', 'authentified').
     * @param array $params Additional parameters such as 'id' for checking ownership.
     * @return bool True if the user is authorized, false otherwise.
     */
    public static function handle($roles, $params = [])
    {
        // Check if the user is logged in
        $user = Session::currentUser();
        if (!$user) {
            // Redirect to login page if not authenticated
            Session::set('error', 'You must be logged in to access this page');
            Router::redirect('error', ['code' => 401]);
            exit;
        }

        // Split the roles into an array
        $rolesArray = explode('|', $roles);

        // Check for 'authentified' role which only requires the user to be logged in
        if (in_array('authentified', $rolesArray) && $user->id()) {
            return true;
        }

        // Check if the user has one of the required roles
        if (in_array($user->role(), $rolesArray)) {
            return true;
        }

        // Check if the user is accessing their own resource
        if (in_array('self', $rolesArray) && isset($params['id']) && $params['id'] == $user->id()) {
            return true;
        }

        // Redirect to error page if the user does not have the required permissions
        Session::set('error', 'You do not have permission to access this page');
        Router::redirect('error', ['code' => 403]);
        exit;
    }
}
