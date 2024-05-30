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
                'name' => 'users',
                'action' => [UserController::class, 'index']
            ],
            [
                'method' => 'GET',
                'path' => '/user/{id}',
                'name' => 'user',
                'action' => [UserController::class, 'show']
            ],
            [
                'method' => 'GET',
                'path' => '/user/{id}/edit',
                'name' => 'edit_user',
                'action' => [UserController::class, 'edit']
            ],
            [
                'method' => 'GET',
                'path' => '/user/create',
                'name' => 'create_user',
                'action' => [UserController::class, 'create']
            ],
            [
                'method' => 'POST',
                'path' => '/user',
                'name' => 'store_user',
                'action' => [UserController::class, 'store']
            ],
            [
                'method' => 'PUT',
                'path' => '/user/{id}',
                'name' => 'update_user',
                'action' => [UserController::class, 'update']
            ],
            [
                'method' => 'DELETE',
                'path' => '/user/{id}',
                'name' => 'delete_user',
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
                'name' => 'posts',
                'action' => [PostController::class, 'index']
            ],
            [
                'method' => 'GET',
                'path' => '/post/{id}',
                'name' => 'post',
                'action' => [PostController::class, 'show']
            ],
            [
                'method' => 'GET',
                'path' => '/post/{id}/edit',
                'name' => 'edit_post',
                'action' => [PostController::class, 'edit']
            ],
            [
                'method' => 'GET',
                'path' => '/post/create',
                'name' => 'create_post',
                'action' => [PostController::class, 'create']
            ],
            [
                'method' => 'POST',
                'path' => '/post',
                'name' => 'store_post',
                'action' => [PostController::class, 'store']
            ],
            [
                'method' => 'PUT',
                'path' => '/post/{id}',
                'name' => 'update_post',
                'action' => [PostController::class, 'update']
            ],
            [
                'method' => 'DELETE',
                'path' => '/post/{id}',
                'name' => 'delete_post',
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
                'name' => 'home',
                'action' => [PageController::class, 'index']
            ],
            [
                'method' => 'GET',
                'path' => '/{slug}',
                'name' => 'page',
                'action' => [PageController::class, 'show']
            ],
            [
                'method' => 'GET',
                'path' => '/error/{code}',
                'name' => 'error',
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
                'name' => 'error',
                'action' => [PageController::class, 'error']
            ]
        ];
    }
}