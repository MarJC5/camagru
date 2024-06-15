<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\helpers\Logger;

/**
 * Class Like
 * Model representing a like in the application.
 */
class Like extends AModel
{
    protected $table = 'likes';

    protected $fillable = ['user_id', 'post_id'];

    /**
     * Like constructor.
     *
     * @param int|null $id The ID of the like to load.
     */
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    /**
     * Get the ID of the user who liked the post.
     *
     * @return int
     */
    public function user()
    {
        return $this->data->user_id;
    }

    /**
     * Get the ID of the post that was liked.
     *
     * @return int
     */
    public function post()
    {
        return $this->data->post_id;
    }

    /**
     * Check if a user has liked a specific post.
     *
     * @param int $user_id The ID of the user.
     * @param int $post_id The ID of the post.
     * @return bool|int Returns the like ID if the user has liked the post, false otherwise.
     */
    public static function hasLiked($user_id, $post_id)
    {
        $like = self::where('user_id', $user_id)->andWhere('post_id', $post_id)->first();
        return $like ? $like->id() : 0;
    }

    /**
     * Get the validation rules for the like.
     *
     * @return array
     */
    public function validation()
    {
        return [
            'user_id' => 'required|exists:users',
            'post_id' => 'required|exists:posts,id',
        ];
    }

    /**
     * Convert the like to a JSON-serializable format.
     *
     * @return array
     */
    public function toJSON()
    {
        return [
            'id' => $this->id(),
            'user_id' => $this->user(),
            'post_id' => $this->post(),
        ];
    }
}
