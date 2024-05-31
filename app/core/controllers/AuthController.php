<?php

namespace Camagru\core\controllers;

use Camagru\core\models\User;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use function Camagru\loadView;

class AuthController {
    public static function login() {
        $_GET['title'] = 'Login';

        echo loadView('auth/login.php');
    }

    public static function register() {
        $_GET['title'] = 'Register';

        echo loadView('auth/register.php');
    }

    public static function logout() {
        Session::destroy();
        Router::redirect('/');
    }
}