<?php 

namespace Camagru\core\models;

use Camagru\core\models\AModel;
use Camagru\core\models\User;
use Camagru\mail\Mail;
use Camagru\routes\Router;
use function Camagru\getElapsedTime;

/**
 * Class Comment
 * Model representing a comment in the application.
 */
class Comment extends AModel
{
    protected $table = 'comments';

    protected $fillable = ['comment', 'user_id', 'post_id'];

    /**
     * Comment constructor.
     *
     * @param int|null $id The ID of the comment to load.
     */
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    /**
     * Get the comment text.
     *
     * @return string
     */
    public function comment()
    {
        return $this->data->comment;
    }

    /**
     * Get the ID of the user who made the comment.
     *
     * @return int
     */
    public function user()
    {
        return $this->data->user_id;
    }

    /**
     * Get the ID of the post the comment belongs to.
     *
     * @return int
     */
    public function post()
    {
        return $this->data->post_id;
    }

    /**
     * Send notification email to the post owner.
     */
    public function sendNotificationEmail($post_id)
    {
        $post = Post::where('id', $post_id)->first();
        $user = User::where('id', $post->user())->first();

        if (!$user) {
            return false;
        }

        if ($user->is_notification_enabled()) {
            $status = Mail::send(
                $user->email(), 
                'New Comment',
                'notification',
                [
                    'user_name' => $user->username(),
                    'notification_body' => 'A new comment has been added to your post.',
                    'site_url' => BASE_URL . Router::to('post', ['id' => $post_id]),
                ]
            );
    
            return $status;
        }
    }


    /**
     * Get the validation rules for the comment.
     *
     * @return array
     */
    public function validation()
    {
        return [
            'comment' => 'required|max:255',
            'user_id' => 'required|exists:users',
            'post_id' => 'required|exists:posts,id',
        ];
    }

    /**
     * Convert the comment to a JSON-serializable format.
     *
     * @return array
     */
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
