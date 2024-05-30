<?php 

namespace Camagru\models;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = ['content', 'user_id', 'post_id'];

    public function __construct()
    {
        parent::__construct();
    }
}