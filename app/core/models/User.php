<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\helpers\Logger;
use Camagru\mail\Mail;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\middlewares\Validation;

class User extends AModel
{
    protected $table = 'users';

    protected $fillable = ['username', 'email', 'password', 'avatar'];
    protected $hidden = [];

    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    public function username()
    {
        return $this->data->username;
    }

    public function email()
    {
        return $this->data->email;
    }

    public function password()
    {
        return $this->data->password;
    }

    public function avatar()
    {
        return $this->data->avatar;
    }

    public function role()
    {
        return $this->data->role;
    }

    public function token()
    {
        return $this->data->token;
    }

    public function is_validated()
    {
        return $this->data->validated;
    }

    public function is_admin()
    {
        return $this->role() === 'admin';
    }

    public function is_user()
    {
        return $this->role() === 'user';
    }

    public function is_notification_enabled()
    {
        return $this->data->notification;
    }

    public function validate($token)
    {
        if ($this->is_validated()) {
            Session::set('success', 'Your account is already validated.');
            Router::redirect('profile');
        }

        if (password_verify($token, $this->token())) {
            Session::set('error', 'Invalid token.');
            return;
        }

        if ($this->update(['validated' => 1]))
        {
            Session::set('success', 'Your account has been validated.');
            Router::redirect('profile');
        } else {
            Session::set('error', 'An error occurred while validating your account.');
            Router::redirect('home');
        }
    }

    public function resend_email_validation()
    {
        if ($this->is_validated()) {
            Session::set('success', 'Your account is already validated.');
            Router::redirect('profile');
        }

        // Send email
        return Mail::send(
            $this->email(), 
            'Email validation',
            'email-validation',
            [
                'activation_link' => BASE_URL . Router::to('validate_email') . '?token=' . $this->token(),
            ]
        );
    }

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

    public function new_password($password)
    {
        $validation = new Validation();
        $rules = $this->validation();

        // only validate the password, so unset the other fields
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
        }

        if ($this->update(['password' => password_hash($password, PASSWORD_DEFAULT)]))
        {
            Session::set('success', 'Password updated successfully.');
            Router::redirect('login');
        } else {
            Session::set('error', 'An error occurred while updating your password.');
            Router::redirect('reset_password');
        }
    }

    public function validation()
    {
        return [
            'username' => 'required|min:3|max:20|alpha_num',
            'email' => 'required|email',
            'password' => 'optinal|min:6',
        ];
    }

    public function registerValidation()
    {
        return [
            'username' => 'required|min:3|max:20|alpha_num|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];
    }
}