<?php 

namespace Camagru\routes;

use Camagru\core\controllers\PostController;
use Camagru\core\controllers\PageController;

/**
 * Class Api
 * Defines the API routes for the application.
 */
class Api {
    
    /**
     * Get all API routes.
     *
     * @return array
     */
    public static function routes() {
        return array_merge(
            self::posts(),
            self::pages()
        );
    }

    /**
     * Get the routes for posts.
     *
     * @return array
     */
    private static function posts() {
        return [
            [
                'method' => 'GET',
                'path' => '/api/posts',
                'query' => ['page', 'limit', 'user_id'],
                'name' => 'posts_json',
                'action' => [PostController::class, 'json']
            ],
            [
                'method' => 'GET',
                'path' => '/api/post/{id}',
                'name' => 'post_show_json',
                'action' => [PostController::class, 'show_json']
            ],
        ];
    }

    /**
     * Get the routes for pages.
     *
     * @return array
     */
    private static function pages() {
        return [
            [
                'method' => 'GET',
                'path' => '/api/pages',
                'name' => 'pages_json',
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
