<?php

namespace Camagru\core\controllers;

use Camagru\core\models\User;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\middlewares\Validation;
use function Camagru\loadView;

class AuthController
{
    public static function login()
    {
        $_GET['title'] = 'Login';

        echo loadView('auth/login.php', [
            'form' => loadView('auth/forms/login-form.php')
        ]);
    }

    public static function register()
    {
        $_GET['title'] = 'Register';

        echo loadView('auth/register.php', [
            'form' => loadView('auth/forms/register-form.php')
        ]);
    }

    public static function logout()
    {
        Session::destroy();
        Router::redirect('home');
    }

    public static function connect()
    {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = $_POST['password'];

        $user = User::where('username', $username)->first();

        if ($user && password_verify($password, $user->password())) {
            Session::set('user', $user->id());
            Session::set('username', $username);
            Router::redirect('profile');
        } else {
            Session::flash('error', 'Invalid username or password');
            Router::redirect('login');
        }
    }

    public static function create()
    {
        $validation = new Validation();
        $user = new User();
        $data = [
            'username' => filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT), // Hashing the password
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)
        ];

        $rules = $user->validation();
        $validation->validate($data, $rules);

        if ($validation->fails()) {
            Session::flash('errors', $validation->getErrors());
            Router::redirect('register_user');
        } else {
            $status = $user->insert($data);

            if (!$status) {
                Session::flash('error', 'An error occurred while creating your account. Please try again.');
                Router::redirect('register_user');
            }
            
            Session::flash('success', 'Account created successfully. Please login.');
            Router::redirect('login');
        }
    }
}
