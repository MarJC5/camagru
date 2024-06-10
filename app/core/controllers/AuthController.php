<?php

namespace Camagru\core\controllers;

use Camagru\core\models\User;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\middlewares\Validation;
use Camagru\helpers\CSRF;
use Camagru\helpers\Logger;

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
        Session::clear();
        Session::set('success', 'You have been logged out');
        Router::redirect('home');
    }

    public static function connect()
    {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = $_POST['password'];

        $user = User::where('username', $username)->first();

        if ($user && password_verify($password, $user->password())) {
            Session::set('user', $user->id());
            Session::set('success', 'Welcome back, ' . $username);
            Router::redirect('profile');
        } else {
            Session::set('error', 'Invalid username or password');
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
            Session::set('errors', $validation->getErrors());
            Router::redirect('register_user');
        } else {
            $status = $user->insert($data);

            if (!$status) {
                Session::set('error', 'An error occurred while creating your account. Please try again.');
                Router::redirect('register_user');
            }

            $user = User::where('username', $data['username'])->first();

            if (!$user) {
                Session::set('error', 'An error occurred while creating your account. Please try again.');
                Router::redirect('register_user');
            }

            // Send email validation link
            $user->update([
                'token' => password_hash(CSRF::generate(), PASSWORD_DEFAULT)
            ]);

            $user->resend_email_validation();
            
            Session::set('success', 'Account created successfully. Please login.');
            Router::redirect('login');
        }
    }
}
