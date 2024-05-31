<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;

class Page extends AModel
{
    protected $table = 'pages';

    protected $fillable = ['title', 'content', 'slug'];

    public function __construct(?int $id = null)
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

    public function validation()
    {
        return [
            'title' => 'required|min:3|max:255',
            'content' => 'required',
            'slug' => 'required|min:3|max:255|alpha_dash|unique:pages',
        ];
    }
}