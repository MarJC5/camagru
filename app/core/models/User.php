<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;

class User extends AModel
{
    protected $table = 'users';

    protected $fillable = ['username', 'email', 'password'];
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

    public function validation()
    {
        return [
            'username' => 'required|min:3|max:20|alpha_num|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];
    }
}