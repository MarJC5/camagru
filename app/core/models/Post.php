<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\core\models\Media;

class Post extends AModel
{
    protected $table = 'posts';

    protected $fillable = ['user_id', 'media_id', 'caption'];

    const PAGINATE = 6;

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

    public function comments()
    {
        return Comment::where('post_id', $this->id());
    }

    public function likes()
    {
        return Like::where('post_id', $this->id());
    }

    public function toJSON()
    {
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
        ];
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