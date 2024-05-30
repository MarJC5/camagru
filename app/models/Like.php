<?php 

namespace Camagru\models;

class Like extends Model
{
    protected $table = 'likes';

    protected $fillable = ['user_id', 'post_id'];

    public function __construct()
    {
        parent::__construct();
    }
}