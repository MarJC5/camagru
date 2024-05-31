<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;

class Like extends AModel
{
    protected $table = 'likes';

    protected $fillable = ['user_id', 'post_id'];

    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }
}