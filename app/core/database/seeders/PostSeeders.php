<?php

namespace Camagru\core\database\seeders;

use Camagru\core\database\seeders\ASeeders;
use Camagru\core\models\Media;
use Camagru\helpers\CSRF;
use Camagru\helpers\Slugify;

class PostSeeders extends ASeeders
{
    protected $table = 'posts';

    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        $this->createPosts();
    }

    private function createPosts()
    {
        // Create 10 users
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $users[] = [
                'username' => Slugify::format('User ' . $i), // Format username (User 1 => user-1)
                'email' => 'user' . $i . '@camagru.com',
                'password' => password_hash('password', PASSWORD_DEFAULT),
                'role' => 'user',
                'token' => password_hash(CSRF::generate(), PASSWORD_DEFAULT),
            ];
        }

        // Create 10 medias from URL (skip user 1 because it's the admin)
        $medias = [];
        for ($i = 0; $i < 10; $i++) {
            $media = new Media();
            $mediaPath = $media->uploadFromURL('https://picsum.photos/600/600');

            $medias[] = [
                'media_path' => $mediaPath,
                'user_id' => $i + 2,
                'title' => 'Title ' . $i,
                'alt' => 'Alt ' . $i,
                'legende' => 'Legende ' . $i,
            ];
        }

        // Create 10 posts
        $posts = [];
        for ($i = 0; $i < 10; $i++) {
            $posts[] = [
                'media_id' => $i + 1,
                'user_id' => $i + 2,
                'caption' => 'Caption ' . $i,
            ];
        }

        // Insert data
        foreach ($users as $user) {
            $this->db->insertIfNotExists('users', $user, 'email');
        }

        foreach ($medias as $media) {
            $this->db->insertIfNotExists('medias', $media, 'media_path');
        }

        foreach ($posts as $post) {
            $this->db->insertIfNotExists('posts', $post, 'media_id');
        }
    }
}
