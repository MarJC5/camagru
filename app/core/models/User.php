<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\helpers\Logger;
use Camagru\mail\Mail;
use Camagru\helpers\Session;
use Camagru\routes\Router;

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
                'activation_link' => BASE_URL . Router::to('validate_email', ['token' => $this->token()])
            ]
        );
    }

    public function validation()
    {
        return [
            'username' => 'required|min:3|max:20|alpha_num|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];
    }
}