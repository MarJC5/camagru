<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;

class Post extends AModel
{
    protected $table = 'posts';

    protected $fillable = ['user_id', 'image', 'caption'];

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

    public function validation()
    {
        return [
            'user_id' => 'required|exists:users',
            'image' => 'required|image',
            'caption' => 'required|max:255',
        ];
    }
}