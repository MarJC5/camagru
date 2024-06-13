<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;

class Like extends AModel
{
    protected $table = 'likes';

    protected $fillable = ['user_id', 'post_id'];

    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    public function user()
    {
        return $this->data->user_id;
    }

    public function post()
    {
        return $this->data->post_id;
    }

    public function validation()
    {
        return [
            'user_id' => 'required|exists:users',
            'post_id' => 'required|exists:posts,id',
        ];
    }

    public function toJSON()
    {
        return [
            'id' => $this->id(),
            'user_id' => $this->user(),
            'post_id' => $this->post(),
        ];
    }
}