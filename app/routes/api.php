<?php 

namespace Camagru\routes;

use Camagru\controllers\PostController;

class Api {
    public static function routes() {
        return array_merge(
            self::posts(),
        );
    }

    /**
     * Page routes
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