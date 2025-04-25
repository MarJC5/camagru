<?php

namespace Camagru\core\controllers;

use Camagru\helpers\Session;
use Camagru\helpers\CSRF;
use Camagru\routes\Router;
use Camagru\core\models\User;
use Camagru\core\middlewares\Validation;
use Camagru\helpers\Logger;
use Camagru\helpers\Slugify;
use Camagru\core\middlewares\Auth;
use Camagru\helpers\Sanitize;

use function Camagru\loadView;

/**
 * Class UserController
 * Handles actions related to users, such as displaying, creating, editing, and deleting users.
 */
class UserController {

    /**
     * Display all users.
     *
     * @return void
     */
    public static function index() {
        $users = User::all();

        $_GET['title'] = 'Users';

        // Convert to array
        $users = array_map(function($user) {
            return User::where('id', $user['id'])->first();
        }, $users);

        echo loadView('user/index.php', [
            'users' => $users,
        ]);
    }

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function profile() {
        if (!Session::isLogged()) {
            Router::redirect('login');
        }

        $user = new User($_SESSION['user']);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        // Check if user is allowed to access this page
        if (!Auth::handle('self', ['id' => $user->id()])) {
            return;
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

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function show($data) {
        $user = new User($data['id']);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        if (Session::currentUser() && ($user->id() === Session::currentUser()->id())) {
            return self::profile();
        }

        $_GET['title'] = '@' . $user->username();

        echo loadView('user/show.php', [
            'user' => $user,
        ]);
    }

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function edit($data) {
        $user = new User($data['id']);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        // Check if user is allowed to access this page
        if (!Auth::handle('admin|self', ['id' => $user->id()])) {
            return;
        }

        $_GET['title'] = '@' . $user->username() . ' - Edit';

        echo loadView('user/edit.php', [
            'user' => $user,
            'form' => loadView('user/form/_form.php', [
                'user_id' => $user->id(),
                'old' => [
                    'username' => $user->username(),
                    'email' => $user->email(),
                    'role' => $user->role(),
                ],
                'notification' => $user->is_notification_enabled() ? 'Disable' : 'Enable',
            ]),
        ]);
    }
    
    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function update() {
        // Sanitize the data
        $_POST = Sanitize::escapeArray($_POST);

        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_update_user'], 'csrf_update_user')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

        $id = $_POST['id'];
        $user = new User($id);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        // Check if user is allowed to access this page
        if (!Auth::handle('admin|self', ['id' => $user->id()])) {
            return;
        }

        $data = array_filter($_POST);  
        unset($data['id'], $data['csrf_update_user']);

        // Remove unchanged fields first
        $currentData = $user->toArray(); 
        $data = array_filter($data, function($value, $key) use ($currentData) {
            return !isset($currentData[$key]) || $currentData[$key] !== $value;
        }, ARRAY_FILTER_USE_BOTH);

        $passwordUpdated = false;

        // Handle password after filtering
        if (isset($data['password'])) {
            if ($data['password'] !== $data['password_confirmation']) {
                Session::set('error', 'Passwords do not match');
                Router::redirect('edit_user', ['id' => $id]);
            }

            if (!password_verify($data['old_password'], $user->password())) {
                Session::set('error', 'Invalid old password');
                Router::redirect('edit_user', ['id' => $id]);
            }

            $passwordUpdateResult = $user->new_password($data['password'], false);
            $passwordUpdated = $passwordUpdateResult;

            unset($data['old_password'], $data['password_confirmation'], $data['password']);
        }

        // If there's still data to update, validate and update it
        if (!empty($data)) {
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
        } else if ($passwordUpdated) {
            // If only password was updated and it was successful
            Session::set('success', 'Password updated successfully');
            if ($id === Session::currentUser()->id()) {
                Router::redirect('profile');
            } else {
                Router::redirect('user', ['id' => $id]);
            }
        } else {
            Session::set('error', 'No data to update');
            Router::redirect('edit_user', ['id' => $id]);
        }
    }

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function delete() {
        // Sanitize the data
        $_POST = Sanitize::escapeArray($_POST);

        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_delete_user'], 'csrf_delete_user')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

        $id = $_POST['id'];
        $user = new User($id);

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        // Check if user is allowed to access this page
        if (!Auth::handle('admin|self', ['id' => $user->id()])) {
            return;
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

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function validate($params) {

        if (!isset($params['token'])) {
            Session::set('error', 'Invalid token');
            Router::redirect('home');
        }

        $token = $params['token'];

        if (empty($token)) {
            Router::redirect('error', ['code' => 404]);
        }

        $user = User::where('id', $params['id'])->first();

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $user->validate($token);
    }

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function toggle_notification()
    {
        // Sanitize the data
        $_POST = Sanitize::escapeArray($_POST);

        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_toggle_notification'], 'csrf_toggle_notification')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

        $user_id = $_POST['id'];
        $user = User::where('id', $user_id)->first();

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        // Check if user is allowed to access this page
        if (!Auth::handle('admin|self', ['id' => $user->id()])) {
            return;
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
            if ($user->id() === Session::currentUser()->id()) {
                Router::redirect('profile');
            } else {
                Router::redirect('user', ['id' => $user->id()]);
            }
        } else {
            Session::set('error', 'An error occurred while updating the notification settings');
            Router::redirect('edit_user', ['id' => $user->id()]);
        }

        Router::redirect('profile');
    }

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function edit_role()
    {
        // Sanitize the data
        $_POST = Sanitize::escapeArray($_POST);

        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_edit_role'], 'csrf_edit_role')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

        $user_id = $_POST['id'];
        $user = User::where('id', $user_id)->first();

        if (empty($user)) {
            Session::set('error', 'Invalid user');
            Router::redirect('error', ['code' => 404]);
        }

        $role = Sanitize::escape($_POST['role']);
        $status = $user->update(['role' => $role]);

        if ($status) {
            Session::set('success', 'Role updated successfully');
            if ($user->id() === Session::currentUser()->id()) {
                Router::redirect('profile');
            } else {
                Router::redirect('user', ['id' => $user->id()]);
            }
        } else {
            Session::set('error', 'An error occurred while updating the role');
            Router::redirect('edit_user', ['id' => $user->id()]);
        }

        Router::redirect('profile');
    }

    /**
     * Display the user creation form.
     *
     * @return void
     */
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
                'form' => loadView('user/form/_new-password.php', [
                    'user_id' => $user_id,
                ]),
            ]);

        } else {
            echo loadView('user/reset-password.php', [
                'form' => loadView('user/form/_reset-password.php'),
            ]);
        }
    }

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function validation_needed() {
        $_GET['title'] = 'Validation needed';

        echo loadView('user/validate.php', [
            'form' => loadView('user/form/_resend-email-validation.php', [
                'token' => Session::currentUser()->token(),
            ]),
        ]);
    }

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function resend_email_validation() {
        // Sanitize the data
        $_POST = Sanitize::escapeArray($_POST);

        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_resend_email_validation'], 'csrf_resend_email_validation')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

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

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function reset_password_request() {
        // Sanitize the data
        $_POST = Sanitize::escapeArray($_POST);

        Logger::log(print_r($_POST, true));

        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_reset_password_request'], 'csrf_reset_password_request')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }
        
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

        $token = CSRF::generate();

        if (!$user->reset_password_request($token))
        {
            Session::set('error', 'An error occurred while sending the reset password email');
            Router::redirect('reset_password');
        }

        Session::setTempResetToken($token);
        Session::set('success', 'Reset password email sent successfully, you have 10 minutes to reset your password');
        Router::redirect('home');
    }

    /**
     * Display the user creation form.
     *
     * @return void
     */
    public static function new_password()
    {
        // Sanitize the data
        $_POST = Sanitize::escapeArray($_POST);
        
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_new_password'], 'csrf_new_password')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

        if (!isset($_POST['password']) || !isset($_POST['password_confirmation'])) {
            Session::set('error', 'No password provided');
            Router::redirect('login');
        }

        $password = $_POST['password'];
        $password_confirmation = $_POST['password_confirmation'];

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