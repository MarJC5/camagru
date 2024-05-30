<?php 

namespace Camagru\models;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['username', 'email', 'password'];

    public function __construct()
    {
        parent::__construct();
    }
}