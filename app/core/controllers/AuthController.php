<?php

namespace Camagru\core\controllers;

use Camagru\core\models\User;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\middlewares\Validation;
use Camagru\helpers\CSRF;
use Camagru\helpers\Slugify;
use function Camagru\loadView;

/**
 * Class AuthController
 * Handles authentication-related actions such as login, register, and logout.
 */
class AuthController
{
    /**
     * Display the login page.
     */
    public static function login()
    {
        if (Session::get('user')) {
            Router::redirect('profile');
        }

        $_GET['title'] = 'Login';

        echo loadView('auth/login.php', [
            'form' => loadView('auth/forms/login-form.php')
        ]);
    }

    /**
     * Display the registration page.
     */
    public static function register()
    {
        if (Session::get('user')) {
            Router::redirect('profile');
        }

        $_GET['title'] = 'Register';

        echo loadView('auth/register.php', [
            'form' => loadView('auth/forms/register-form.php')
        ]);
    }

    /**
     * Handle user logout and redirect to home page.
     */
    public static function logout()
    {
        if (!Session::get('user')) {
            Router::redirect('home');
        }
        
        Session::clear();
        Session::set('success', 'You have been logged out');
        Router::redirect('home');
    }

    /**
     * Handle user login.
     */
    public static function connect()
    {
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_connect_user'], 'csrf_connect_user')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

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

    /**
     * Handle user registration.
     */
    public static function create()
    {
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_create_user'], 'csrf_create_user')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

        $validation = new Validation();
        $user = new User();
        
        $data = [
            'username' => Slugify::format(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS)),
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT), // Hashing the password
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)
        ];

        $rules = $user->registerValidation();
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

            $user->update([
                'token' => password_hash(CSRF::generate(), PASSWORD_DEFAULT)
            ]);

            $user->resend_email_validation();
            
            Session::set('success', 'Account created successfully. Please login.');
            Router::redirect('login');
        }
    }
}
