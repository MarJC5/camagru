<?php 

namespace Camagru\models;

use Camagru\models\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['username', 'email', 'password'];
    protected $hidden = ['password'];

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

    public function validation()
    {
        return [
            'username' => 'required|min:3|max:20|alpha_num|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];
    }
}