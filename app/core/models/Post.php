<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\core\models\Media;

class Post extends AModel
{
    protected $table = 'posts';

    protected $fillable = ['user_id', 'media_id', 'caption'];

    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    public function user()
    {
        return $this->data->user_id;
    }

    public function image()
    {
        return $this->data->image;
    }

    public function caption()
    {
        return $this->data->caption;
    }

    public function media()
    {
        return new Media($this->data->media_id);
    }

    public function validation()
    {
        return [
            'user_id' => 'required|exists:users',
            'media_id' => 'required|exists:medias,id',
            'caption' => 'required|max:255'
        ];
    }
}