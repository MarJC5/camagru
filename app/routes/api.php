<?php 

namespace Camagru\routes;

use Camagru\core\controllers\PostController;

class Api {
    public static function routes() {
        return array_merge(
            self::posts(),
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
                'action' => [PostController::class, 'json']
            ],
        ];
    }
}