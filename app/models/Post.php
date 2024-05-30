<?php 

namespace Camagru\models;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = ['user_id', 'image', 'caption'];

    public function __construct()
    {
        parent::__construct();
    }
}