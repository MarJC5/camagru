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

    public static function show($data) {
        $user = new User($data['id']);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $_GET['title'] = '@' . $user->username();

        echo loadView('user/show.php', [
            'user' => $user,
        ]);
    }

    public static function edit($data) {
        $user = new User($data['id']);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $_GET['title'] = '@' . $user->username() . ' - Edit';

        echo loadView('user/edit.php', [
            'user' => $user,
            'form' => loadView('user/forms/update-form.php', [
                'user_id' => $user->id(),
                'old_username' => $user->username(),
                'old_email' => $user->email(),
                'notification' => $user->is_notification_enabled() ? 'Disable' : 'Enable',
            ]),
        ]);
    }

    public static function update() {
        // TODO : Check csrf_token validity

        $id = $_POST['id'];
        $user = new User($id);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        // remove if empty
        $data = array_filter($_POST);

        // remove id
        unset($data['id']);
        unset($data['csrf_token']);

        $validation = new Validation();
        $rules = $user->validation();
        $validation->validate($data, $rules);

        if ($validation->fails()) {
            $errors = $validation->getErrors();

            Session::set('error', $errors);
            Router::redirect('edit_user', ['id' => $id]);
        } else {
            // Hash password
            if (isset($data['password'])) {

                // Validate password
                if ($data['password'] !== $data['password_confirmation']) {
                    Session::set('error', 'Passwords do not match');
                    Router::redirect('edit_user', ['id' => $id]);
                }

                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $status = $user->update($data);

            if ($status) {
                Session::set('success', 'User updated successfully');
                if ($id === Session::currentUser()->id()) {
                    Router::redirect('profile');
                } else {
                    Router::redirect('user', ['id' => $id]);
                }
            } else {
                Session::set('error', 'An error occurred while updating the user');
                Router::redirect('edit_user', ['id' => $id]);
            }
        }
    }

    public static function delete() {
        // TODO : Check csrf_token validity
        $id = $_POST['id'];
        $user = new User($id);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $status = $user->delete();

        if ($status) {
            Session::set('success', 'User deleted successfully');
            // Redirect to home if the user is deleting his own account
            if ($id === Session::get('user')) {
                Router::redirect('logout');
            } else {
                Router::redirect('home');
            }
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

    public static function toggle_notification()
    {
        // TODO : Check csrf_token validity

        $user_id = $_POST['id'];
        $user = User::where('id', $user_id)->first();

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $notification = 0;
        if (!$user->is_notification_enabled()) {
            $notification = 1;
        } else {
            $notification = 0;
        }
        $status = $user->update(['notification' => $notification]);

        if ($status) {
            Session::set('success', 'Notification settings updated successfully');
        } else {
            Session::set('error', 'An error occurred while updating the notification settings');
        }

        Router::redirect('profile');
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
        // TODO : Check csrf_token validity

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
        // TODO : Check csrf_token validity
        
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

        // Avoid reuse reset link
        Session::removeTempResetToken();

        Session::set('success', 'Password updated successfully');
        Router::redirect('login');
    }
}