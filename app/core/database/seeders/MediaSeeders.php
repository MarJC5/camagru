<?php 

namespace Camagru\core\database\seeders;

use Camagru\core\database\seeders\ASeeders;
use Camagru\core\models\Post;
use Camagru\helpers\Logger;

class MediaSeeders extends ASeeders
{
    protected $table = 'medias';
    
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        $this->createInteractions();
    }

    public function createInteractions()
    {
        $all = Post::all();
        // Create Fake comments & likes for each post
        foreach ($all as $post) {
            $this->createComments($post['id']);
            $this->createLikes($post['id']);
        }
    }

    private function createComments($post_id)
    {
        $comments = [
            [
                'post_id' => $post_id,
                'user_id' => 1,
                'comment' => 'This is a great photo!',
            ],
            [
                'post_id' => $post_id,
                'user_id' => 2,
                'comment' => 'I love this photo!',
            ],
            [
                'post_id' => $post_id,
                'user_id' => 3,
                'comment' => 'This photo is amazing!',
            ],
        ];

        foreach ($comments as $comment) {
            $this->db->insertIfNotExists('comments', $comment, 'user_id');
        }
    }

    private function createLikes($post_id)
    {
        $likes = [
            [
                'post_id' => $post_id,
                'user_id' => 1,
            ],
            [
                'post_id' => $post_id,
                'user_id' => 2,
            ],
            [
                'post_id' => $post_id,
                'user_id' => 3,
            ],
        ];

        foreach ($likes as $like) {
            $this->db->insertIfNotExists('likes', $like, 'user_id');
        }
    }
}