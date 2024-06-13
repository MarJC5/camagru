<?php

namespace Camagru\core\database\seeders;

use Camagru\core\database\seeders\ASeeders;
use Camagru\core\models\Media;
use Camagru\helpers\CSRF;

class PostSeeders extends ASeeders
{
    protected $table = 'posts';
    protected $userIds;

    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        $this->createUsers();
        $this->createPostsCommentsLikes();
    }

    private function createUsers()
    {
        // Fetch users from JSONPlaceholder
        $usersData = json_decode(file_get_contents('https://jsonplaceholder.typicode.com/users'), true);
        $userIds = [];

        // Insert users into the database
        foreach ($usersData as $userData) {
            $user = [
                'username' => $userData['username'],
                'email' => $userData['email'],
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'user',
                'token' => password_hash(CSRF::generate(), PASSWORD_DEFAULT),
            ];
            $this->db->insertIfNotExists('users', $user, 'email');
            $userIds[$userData['id']] = $this->db->getLastInsertId();
        }

        $this->userIds = $userIds;
    }

    private function createPostsCommentsLikes()
    {
        // Fetch posts and comments from JSONPlaceholder
        $postsData = json_decode(file_get_contents('https://jsonplaceholder.typicode.com/posts'), true);
        $commentsData = json_decode(file_get_contents('https://jsonplaceholder.typicode.com/comments'), true);

        // Shuffle postsData to randomize post creation
        shuffle($postsData);

        foreach ($postsData as $postData) {
            // Randomly assign a user to the post
            $userId = $this->userIds[array_rand($this->userIds)];

            // Create media for the post
            $photoData = [
                'title' => $postData['title'],
                'url' => 'https://picsum.photos/600/600',
            ];
            $media = new Media();
            $mediaPath = $media->uploadFromURL($photoData['url']);
            $mediaData = [
                'media_path' => $mediaPath,
                'user_id' => $userId,
                'title' => $photoData['title'],
                'alt' => $photoData['title'],
                'legende' => $photoData['title'],
            ];
            $this->db->insertIfNotExists('medias', $mediaData, 'media_path');
            $mediaId = $this->db->getLastInsertId();

            // Create post
            $post = [
                'media_id' => $mediaId,
                'user_id' => $userId,
                'caption' => $postData['title'],
            ];
            $this->db->insertIfNotExists('posts', $post, 'media_id');
            $postId = $this->db->getLastInsertId();

            // Create a random number of comments for each post
            $numComments = rand(1, 20);
            for ($j = 0; $j < $numComments; $j++) {
                $commentKey = array_rand($commentsData);
                $commentData = $commentsData[$commentKey];
                $comment = [
                    'user_id' => $this->userIds[array_rand($this->userIds)],
                    'post_id' => $postId,
                    'comment' => $commentData['body'],
                ];
                $this->db->insertIfNotExists('comments', $comment, 'comment');
            }

            // Create a random number of likes for each post
            $numLikes = rand(1, 20);
            for ($k = 0; $k < $numLikes; $k++) {
                $likeUserId = $this->userIds[array_rand($this->userIds)]; // Ensure random user ID exists
                $like = [
                    'user_id' => $likeUserId,
                    'post_id' => $postId,
                ];
                $this->db->insertIfNotExists('likes', $like, 'post_id');
            }
        }
    }
}
