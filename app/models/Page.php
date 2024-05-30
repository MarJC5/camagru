<?php 

namespace Camagru\models;

use Camagru\models\Model;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = ['title', 'content', 'slug'];

    public function __construct(int $id = null)
    {
        parent::__construct($id);
    }

    public function title()
    {
        return $this->data->title;
    }

    public function content()
    {
        return $this->data->content;
    }

    public function slug()
    {
        return $this->data->slug;
    }
}