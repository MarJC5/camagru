<?php 

namespace Camagru\routes;

use Camagru\core\controllers\PostController;
use Camagru\core\controllers\PageController;

class Api {
    public static function routes() {
        return array_merge(
            self::posts(),
            self::pages()
        );
    }

    /**
     * Posts routes
     */
    private static function posts() {
        return [
            [
                'method' => 'GET',
                'path' => '/api/posts',
                'query' => ['page', 'limit', 'sort', 'order', 'user_id'],
                'name' => 'posts_json',
                'action' => [PostController::class, 'json']
            ],
            [
                'method' => 'GET',
                'path' => '/api/post/{id}',
                'name' => 'show_json',
                'action' => [PostController::class, 'show_json']
            ],
        ];
    }

    /**
     * Pages routes
     */
    private static function pages() {
        return [
            [
                'method' => 'GET',
                'path' => '/api/pages',
                'action' => [PageController::class, 'json']
            ],
            [
                'method' => 'GET',
                'path' => '/api/page/{slug}',
                'name' => 'page',
                'action' => [PageController::class, 'show_json']
            ],
        ];
    }
}