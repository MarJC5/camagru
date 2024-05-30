<?php 

namespace Camagru\models;

use Camagru\models\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = ['content', 'user_id', 'post_id'];

    public function __construct()
    {
        parent::__construct();
    }
}