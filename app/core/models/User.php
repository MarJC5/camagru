<?php

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\core\models\Media;
use Camagru\core\models\Comment;
use Camagru\core\models\Like;
use Camagru\core\models\Post;
use Camagru\helpers\Logger;
use Camagru\mail\Mail;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\middlewares\Validation;
use Camagru\helpers\CSRF;

/**
 * Class User
 * Model representing a user in the application.
 */
class User extends AModel
{
    protected $table = 'users';
    protected $fillable = ['username', 'first_name', 'last_name', 'email', 'password', 'media_id', 'role', 'validated', 'token', 'notification'];
    protected $hidden = [];

    const ROLES = ['admin', 'user'];

    /**
     * User constructor.
     *
     * @param int|null $id The ID of the user to load.
     */
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    /**
     * Get the username of the user.
     *
     * @return string
     */
    public function username()
    {
        return $this->data->username ?? '';
    }

    /**
     * Get the first name of the user.
     *
     * @return string
     */
    public function first_name()
    {
        return $this->data->first_name ?? '';
    }

    /**
     * Get the last name of the user.
     *
     * @return string
     */
    public function last_name()
    {
        return $this->data->last_name ?? '';
    }

    /**
     * Get the email of the user.
     *
     * @return string
     */
    public function email()
    {
        return $this->data->email ?? '';
    }

    /**
     * Get the hashed password of the user.
     *
     * @return string
     */
    public function password()
    {
        return $this->data->password ?? '';
    }

    /**
     * Get the avatar media associated with the user.
     *
     * @return Media
     */
    public function avatar()
    {
        return new Media($this->data->media_id) ?? null;
    }

    /**
     * Get the role of the user.
     *
     * @return string
     */
    public function role()
    {
        return $this->data->role ?? 'user';
    }

    /**
     * Get the token associated with the user.
     *
     * @return string
     */
    public function token()
    {
        return $this->data->token ?? '';
    }

    /**
     * Get the likes made by the user.
     *
     * @return array
     */
    public function likes()
    {
        return Like::where('user_id', $this->id());
    }

    /**
     * Get the comments made by the user.
     *
     * @return array
     */
    public function comments()
    {
        return Comment::where('user_id', $this->id());
    }

    /**
     * Get the posts created by the user.
     *
     * @return array
     */
    public function posts()
    {
        return Post::where('user_id', $this->id());
    }

    /**
     * Check if the user is validated.
     *
     * @return bool
     */
    public function is_validated()
    {
        return $this->data->validated ?? false;
    }

    /**
     * Check if the user has admin role.
     *
     * @return bool
     */
    public function is_admin()
    {
        return $this->role() === 'admin';
    }

    /**
     * Check if the user has user role.
     *
     * @return bool
     */
    public function is_user()
    {
        return $this->role() === 'user';
    }

    /**
     * Check if the user has notifications enabled.
     *
     * @return bool
     */
    public function is_notification_enabled()
    {
        return $this->data->notification ?? false;
    }

    /**
     * Convert the user to a JSON-serializable array.
     *
     * @return array
     */
    public function toJSON()
    {
        if (!$this->id()) {
            return [];
        }
        
        return [
            'id' => $this->id(),
            'username' => $this->username(),
        ];
    }

    /**
     * Validate the user account.
     *
     * @param string $token
     */
    public function validate($token)
    {
        if ($this->is_validated()) {
            Session::set('success', 'Your account is already validated.');
            Router::redirect('profile');
            return;
        }

        if (!password_verify($token, $this->token())) {
            Session::set('error', 'Invalid token.');
            return;
        }

            // New token for security
            $plainToken = CSRF::generate();
            $hashedToken = password_hash($plainToken, PASSWORD_DEFAULT);

        if ($this->update(['token' => $hashedToken, 'validated' => 1])) {
            Session::set('success', 'Your account has been validated, you can now login.');
            Router::redirect('login');
        } else {
            Session::set('error', 'An error occurred while validating your account.');
            Router::redirect('home');
        }
    }

    /**
     * Resend the email validation link.
     *
     * @return bool
     */
    public function resend_email_validation()
    {
        if ($this->is_validated()) {
            Session::set('success', 'Your account is already validated.');
            Router::redirect('profile');
            return; // Ensuring redirection by stopping further execution.
        }

        // Generate a new plain token and store its hash in the database
        $plainToken = CSRF::generate(); // Generate a new token
        $hashedToken = password_hash($plainToken, PASSWORD_DEFAULT);
        $this->update(['token' => $hashedToken]); // Update the stored hash token in the database

        // Send email with the plain token
        return Mail::send(
            $this->email(),
            'Email validation',
            'email-validation',
            [
                'activation_link' => BASE_URL . Router::to('validate_email') . '?token=' . $plainToken . '&id=' . $this->id(),
            ]
        );
    }


    /**
     * Send a password reset request email.
     *
     * @param string $token
     * @return bool
     */
    public function reset_password_request($token)
    {
        // Send email
        return Mail::send(
            $this->email(),
            'Password reset',
            'reset-password',
            [
                'user_name' => $this->username(),
                'reset_link' => BASE_URL . Router::to('reset_password') . '?token=' . $token . '&id=' . $this->id(),
                'notification_body' => 'You have 10 minutes to reset your password. If you did not request a password reset, please ignore this email.'
            ]
        );
    }

    /**
     * Update the user's password.
     *
     * @param string $password
     */
    public function new_password($password, $showError = true)
    {
        $validation = new Validation();
        $rules = $this->validation();

        // Only validate the password, so unset the other fields
        foreach ($rules as $key => $value) {
            if ($key !== 'password') {
                unset($rules[$key]);
            }
        }

        $validation->validate(['password' => $password], $rules);

        if ($validation->fails()) {
            $errors = $validation->getErrors();
            Session::set('error', $errors);
            Router::redirect('reset_password');
            return false;
        }

        $result = $this->update(['password' => password_hash($password, PASSWORD_DEFAULT)]);

        if ($result) {
            if ($showError) {
                Session::set('success', 'Password updated successfully.');
                Router::redirect('login');
            }
            return true;
        } else {
            if ($showError) {
                Session::set('error', 'An error occurred while updating your password.');
                Router::redirect('reset_password');
            }
            return false;
        }
    }

    /**
     * Get the validation rules for the user.
     *
     * @return array
     */
    public function validation()
    {
        return [
            'username' => 'optional|min:3|max:20|alpha_num|unique:users,username,' . $this->id(),
            'email' => 'optional|email|unique:users,email,' . $this->id(),
            'password' => 'optional|min:6',
        ];
    }

    /**
     * Get the validation rules for registering a user.
     *
     * @return array
     */
    public function registerValidation()
    {
        return [
            'username' => 'required|min:3|max:20|alpha_num|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];
    }
}
