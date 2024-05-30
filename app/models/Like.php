<?php 

namespace Camagru\models;

use Camagru\models\Model;

class Like extends Model
{
    protected $table = 'likes';

    protected $fillable = ['user_id', 'post_id'];

    public function __construct(int $id = null)
    {
        parent::__construct($id);
    }
}