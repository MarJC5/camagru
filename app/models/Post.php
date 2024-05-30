<?php 

namespace Camagru\models;

use Camagru\models\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = ['user_id', 'image', 'caption'];

    public function __construct(int $id = null)
    {
        parent::__construct($id);
    }
}