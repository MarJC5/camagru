<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\core\models\Media;

class Page extends AModel
{
    protected $table = 'pages';

    protected $fillable = ['title', 'content', 'slug', 'media_id'];

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

    public function media()
    {
        return new Media($this->data->media_id);
    }

    public function validation()
    {
        return [
            'title' => 'required|min:3|max:255',
            'content' => 'required',
            'slug' => 'required|min:3|max:255|alpha_dash|unique:pages',
            'media_id' => 'required|exists:medias,id'
        ];
    }
}