<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\helpers\Logger;

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

    public static function hasLiked($user_id, $post_id)
    {
        // If the user has liked the post, return true else return false
        $likes = self::where('user_id', $user_id)->andWhere('post_id', $post_id)->first();
        return $likes ? $likes->id() : false;
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