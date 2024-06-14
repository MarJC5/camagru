<?php

namespace Camagru\core\middlewares;

use Camagru\helpers\Logger;
use Camagru\routes\Router;
use Camagru\helpers\Session;
use Camagru\core\database\Runner;

class Auth
{
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

        $rolesArray = explode('|', $roles);

        // Check for 'authentified' that the user is logged in
        if (in_array('authentified', $rolesArray) && $user->id()) {
            return true;
        }

        // Check if the user has the required role
        if (in_array($user->role(), $rolesArray)) {
            return true;
        }

        // Check if the user is accessing their own resource
        if (in_array('self', $rolesArray) && isset($params['id']) && $params['id'] == $user->id()) {
            return true;
        }

        // Redirect to login page if not authenticated
        Session::set('error', 'You do not have permission to access this page');
        Router::redirect('error', ['code' => 403]);
        exit;
    }
}
