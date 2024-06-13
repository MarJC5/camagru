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
        $all = array_map(function($post) {
            return Post::where('id', $post['id'])->first();
        }, $all);
        foreach ($all as $post) {
            $this->createComments($post->id());
            $this->createLikes($post->id());
        }
    }

    private function createComments($post_id)
    {
        $comments = [];

        for ($i = 0; $i < 10; $i++) {
            $comments[] = [
                'post_id' => $post_id,
                'user_id' => $i + 1,
                'comment' => 'This is a comment ' . $i,
            ];
        }

        foreach ($comments as $comment) {
            $this->db->insertIfNotExists('comments', $comment, 'user_id');
        }
    }

    private function createLikes($post_id)
    {
        $likes = [];

        for ($i = 0; $i < 10; $i++) {
            $likes[] = [
                'post_id' => $post_id,
                'user_id' => $i + 1,
            ];
        }

        foreach ($likes as $like) {
            $this->db->insertIfNotExists('likes', $like, 'user_id');
        }
    }
}