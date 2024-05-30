<?php 

namespace Camagru\models;

use Camagru\models\Model;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = ['title', 'content', 'slug'];

    public function __construct()
    {
        parent::__construct();
    }
}