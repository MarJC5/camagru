<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\core\models\Media;
use Camagru\core\models\Comment;
use Camagru\core\models\Like;
use Camagru\core\models\User;
use Camagru\helpers\Session;
use Camagru\helpers\CSRF;

/**
 * Class Post
 * Model representing a post in the application.
 */
class Post extends AModel
{
    protected $table = 'posts';
    protected $fillable = ['user_id', 'media_id', 'caption'];

    const PAGINATE = 6;

    /**
     * Post constructor.
     *
     * @param int|null $id The ID of the post to load.
     */
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    /**
     * Get the user ID associated with the post.
     *
     * @return int
     */
    public function user()
    {
        return $this->data->user_id ?? null;
    }

    /**
     * Get the caption of the post.
     *
     * @return string
     */
    public function caption()
    {
        return $this->data->caption ?? null;
    }

    /**
     * Get the media associated with the post.
     *
     * @return Media
     */
    public function media()
    {
        return new Media($this->data->media_id) ?? null;
    }

    /**
     * Get the comments associated with the post.
     *
     * @return array
     */
    public function comments()
    {
        return Comment::where('post_id', $this->id());
    }

    /**
     * Get the likes associated with the post.
     *
     * @return array
     */
    public function likes()
    {
        return Like::where('post_id', $this->id());
    }

    /**
     * Convert the post to a JSON-serializable array.
     *
     * @return array
     */
    public function toJSON()
    {
        if (!$this->id()) {
            return [];
        }

        $current_user = Session::currentUser() ? Session::currentUser()->id() : 0;

        return [
            'id' => $this->id(),
            'caption' => $this->caption(),
            'count_comments' => $this->comments()->count('post_id', $this->id()),
            'count_likes' => $this->likes()->count('post_id', $this->id()),
            'created_at' => $this->created_at(),
            'updated_at' => $this->updated_at(),
            'user' => User::where('id', $this->user())->first()->toJSON(),
            'media' => $this->media()->toJSON(),
            'comments' => array_reverse($this->comments()->map(function ($comment) {
                return $comment->toJSON();
            })),
            'likes' => $this->likes()->map(function ($like) {
                return $like->toJSON();
            }),
            'current_user' => [
                'id' => $current_user,
                'has_liked' => $current_user ? Like::hasLiked(Session::currentUser()->id(), $this->id()) : 0,
                'like' => [
                    'csrf'=> $current_user ? CSRF::token('csrf_like_' . $this->id() . '_' . Session::currentUser()->id()) : null,
                    'csrf_name' => $current_user ? 'csrf_like_' . $this->id() . '_' . Session::currentUser()->id() : null,
                    'path' => $current_user ? 'like' : 'login'
                ],
                'unlike' => [
                    'csrf'=> $current_user ? CSRF::token('csrf_unlike_' . $this->id() . '_' . Session::currentUser()->id()) : null,
                    'csrf_name' => $current_user ? 'csrf_unlike_' . $this->id() . '_' . Session::currentUser()->id() : null,
                    'path' => $current_user ? 'unlike' : 'login',
                ]
            ],
        ];
    }

    /**
     * Get the validation rules for the post.
     *
     * @return array
     */
    public function validation()
    {
        return [
            'user_id' => 'required|exists:users',
            'media_id' => 'required|exists:medias,id',
            'caption' => 'required|max:255'
        ];
    }
}
