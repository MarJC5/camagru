<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;

class Comment extends AModel
{
    protected $table = 'comments';

    protected $fillable = ['content', 'user_id', 'post_id'];

    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }
}