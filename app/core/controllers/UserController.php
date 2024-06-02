<?php

namespace Camagru\core\controllers;

use Camagru\helpers\Session;
use Camagru\helpers\CSRF;
use Camagru\routes\Router;
use Camagru\core\models\User;
use Camagru\core\middlewares\Validation;
use Camagru\helpers\Logger;

use function Camagru\loadView;

class UserController {
    public static function index() {
        $users = User::all();

        $_GET['title'] = 'Users';

        echo loadView('user/index.php', [
            'users' => $users,
        ]);
    }

    public static function profile() {
        if (!Session::isLogged()) {
            Router::redirect('login');
        }

        $user = new User($_SESSION['user']);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        // Is user validated?
        if (!$user->is_validated()) {
            return self::validation_needed();
        }

        $_GET['title'] = '@' . $user->username();

        echo loadView('user/profile.php', [
            'user' => $user,
        ]);
    }

    public static function show($id) {
        $user = new User($id);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $_GET['title'] = '@' . $user->username();

        echo loadView('user/show.php', [
            'user' => $user,
        ]);
    }

    public static function edit($id) {
        $user = new User($id);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $_GET['title'] = '@' . $user->username() . ' - Edit';

        echo loadView('user/edit.php', [
            'user' => $user,
        ]);
    }

    public static function update($id, $data) {
        $user = new User($id);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $validation = new Validation();
        $rules = $user->validation();
        $validation->validate($data, $rules);

        if ($validation->fails()) {
            $errors = $validation->getErrors();

            Session::set('error', $errors);
            Router::redirect('edit_user', ['id' => $id]);
        } else {
            $status = $user->update($data);

            if ($status) {
                Session::set('success', 'User updated successfully');
                Router::redirect('user', ['id' => $id]);
            } else {
                Session::set('error', 'An error occurred while updating the user');
                Router::redirect('edit_user', ['id' => $id]);
            }
        }
    }

    public static function delete($id) {
        $user = new User($id);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $status = $user->delete();

        if ($status) {
            Session::set('success', 'User deleted successfully');
        } else {
            Session::set('error', 'An error occurred while deleting the user');
        }
    }

    public static function validate($params) {

        if (!isset($params['token'])) {
            Session::set('error', 'Invalid token');
            Router::redirect('home');
        }

        $token = $params['token'];

        if (empty($token)) {
            Router::redirect('error', ['code' => 404]);
        }

        $user = User::where('token', $token)->first();

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $user->validate($token);
    }

    public static function reset_password($params) {
        $_GET['title'] = 'Reset password';

        if (isset($params['token']) && isset($params['id'])) {
            $token = $params['token'];

            if (empty($token)) {
                Router::redirect('error', ['code' => 404]);
            }

            if (!Session::checkTempResetToken($token)) {
                Session::set('error', 'Token expired');
                Router::redirect('reset_password');
            }

            $user_id = $params['id'];
            $user = User::where('id', $user_id)->first();

            if (empty($user)) {
                Session::set('error', 'Invalid user');
                Router::redirect('error', ['code' => 404]);
            }

            // Avoid reuse reset link
            Session::removeTempResetToken();

            echo loadView('user/reset-password.php', [
                'form' => loadView('user/forms/new-password.php', [
                    'user_id' => $user_id,
                ]),
            ]);

        } else {
            echo loadView('user/reset-password.php', [
                'form' => loadView('user/forms/reset-password.php'),
            ]);
        }
    }

    public static function validation_needed() {
        $_GET['title'] = 'Validation needed';

        echo loadView('user/validate.php', [
            'form' => loadView('user/forms/resend-email-validation.php', [
                'token' => Session::currentUser()->token(),
            ]),
        ]);
    }

    public static function resend_email_validation() {
        $user = Session::currentUser();

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        if (!$user->resend_email_validation())
        {
            Session::set('error', 'An error occurred while sending the validation email');
            Router::redirect('profile');
        }
        Session::set('success', 'Validation email sent successfully');
        Router::redirect('profile');
    }

    public static function reset_password_request() {

        if (!isset($_POST['email'])) {
            Session::set('error', 'No email provided');
            Router::redirect('reset_password');
        }

        $email = $_POST['email'];

        $user = User::where('email', $email)->first();

        if (empty($user)) {
            Session::set('error', 'An error occurred while sending the reset password email');
            Router::redirect('reset_password');
        }

        $token = $_POST['csrf_token'];

        if (!CSRF::verify($token)) {
            Session::set('error', 'Invalid token');
            Router::redirect('reset_password');
        }

        if (!$user->reset_password_request($token))
        {
            Session::set('error', 'An error occurred while sending the reset password email');
            Router::redirect('reset_password');
        }

        Session::setTempResetToken($token);
        Session::set('success', 'Reset password email sent successfully, you have 10 minutes to reset your password');
        Router::redirect('home');
    }

    public static function new_password()
    {
        if (!isset($_POST['password']) || !isset($_POST['password_confirmation'])) {
            Session::set('error', 'No password provided');
            Router::redirect('login');
        }

        $password = $_POST['password'];
        $password_confirmation = $_POST['password_confirmation'];
        $token = $_POST['csrf_token'];

        if (!CSRF::verify($token)) {
            Session::set('error', 'Invalid token');
            Router::redirect('reset_password');
        }

        if ($password !== $password_confirmation) {
            Session::set('error', 'Passwords do not match');
            Router::redirect('reset_password');
        }

        if (!isset($_POST['user_id']))
        {
            Session::set('error', 'User id not provided');
            Router::redirect('reset_password');
        }

        $user_id = $_POST['user_id'];
        $user = User::where('id', $user_id)->first();

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('reset_password');
        }

        if (!$user->new_password($password)) {
            Session::set('error', 'An error occurred while updating the password');
            Router::redirect('reset_password');
        }

        Session::set('success', 'Password updated successfully');
        Router::redirect('login');
    }
}