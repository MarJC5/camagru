<?php 

namespace Camagru\routes;

use Camagru\controllers\PageController;
use Camagru\controllers\PostController;
use Camagru\controllers\UserController;

class Web {
    public static function routes() {
        return array_merge(
            self::user(),
            self::post(),
            self::page()
        );
    }

    /**
     * User routes
     */
    private static function user() {
        return [
            [
                'method' => 'GET',
                'path' => '/users',
                'action' => [UserController::class, 'index']
            ],
            [
                'method' => 'GET',
                'path' => '/user/{id}',
                'action' => [UserController::class, 'show']
            ],
            [
                'method' => 'GET',
                'path' => '/user/{id}/edit',
                'action' => [UserController::class, 'edit']
            ],
            [
                'method' => 'GET',
                'path' => '/user/create',
                'action' => [UserController::class, 'create']
            ],
            [
                'method' => 'POST',
                'path' => '/user',
                'action' => [UserController::class, 'store']
            ],
            [
                'method' => 'PUT',
                'path' => '/user/{id}',
                'action' => [UserController::class, 'update']
            ],
            [
                'method' => 'DELETE',
                'path' => '/user/{id}',
                'action' => [UserController::class, 'delete']
            ]
        ];
    }

    /**
     * Post routes
     */
    private static function post() {
        return [
            [
                'method' => 'GET',
                'path' => '/posts',
                'action' => [PostController::class, 'index']
            ],
            [
                'method' => 'GET',
                'path' => '/post/{id}',
                'action' => [PostController::class, 'show']
            ],
            [
                'method' => 'GET',
                'path' => '/post/{id}/edit',
                'action' => [PostController::class, 'edit']
            ],
            [
                'method' => 'GET',
                'path' => '/post/create',
                'action' => [PostController::class, 'create']
            ],
            [
                'method' => 'POST',
                'path' => '/post',
                'action' => [PostController::class, 'store']
            ],
            [
                'method' => 'PUT',
                'path' => '/post/{id}',
                'action' => [PostController::class, 'update']
            ],
            [
                'method' => 'DELETE',
                'path' => '/post/{id}',
                'action' => [PostController::class, 'delete']
            ]
        ];
    }

    /**
     * Page routes
     */
    private static function page() {
        return [
            [
                'method' => 'GET',
                'path' => '/',
                'action' => [PageController::class, 'index']
            ],
            [
                'method' => 'GET',
                'path' => '/{slug}',
                'action' => [PageController::class, 'show']
            ],
            [
                'method' => 'GET',
                'path' => '/error/{code}',
                'action' => [PageController::class, 'error']
            ]
        ];
    }

    /**
     * Handle errors
     */
    public static function error() {
        return [
            [
                'method' => 'GET',
                'path' => '/error/{code}',
                'action' => [PageController::class, 'error']
            ]
        ];
    }
}