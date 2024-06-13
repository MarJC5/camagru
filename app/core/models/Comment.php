<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\core\models\User;
use function Camagru\getElapsedTime;

class Comment extends AModel
{
    protected $table = 'comments';

    protected $fillable = ['comment', 'user_id', 'post_id'];

    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    public function comment()
    {
        return $this->data->comment;
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
            'comment' => 'required|max:255',
            'user_id' => 'required|exists:users',
            'post_id' => 'required|exists:posts,id',
        ];
    }

    public function toJSON()
    {
        return [
            'id' => $this->id(),
            'post_id' => $this->post(),
            'comment' => $this->comment(),
            'user' => User::where('id', $this->user())->first()->toJSON(),
            'created_at' => getElapsedTime($this->created_at()),
        ];
    }
}