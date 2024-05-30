<?php 

namespace Camagru\models;

use Camagru\models\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['username', 'email', 'password'];

    public function __construct(int $id = null)
    {
        parent::__construct($id);
    }
}