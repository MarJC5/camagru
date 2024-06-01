<?php 

namespace Camagru\routes;

use Camagru\core\controllers\PageController;
use Camagru\core\controllers\PostController;
use Camagru\core\controllers\UserController;
use Camagru\core\controllers\AuthController;

class Web {
    public static function routes() {
        return array_merge(
            self::setup(),
            self::users(),
            self::posts(),
            self::pages(),
            self::auth(),
        );
    }

    /**
     * Auth routes
     */
    private static function auth() {
        return [
            [
                'method' => 'GET',
                'path' => '/auth/login',
                'name' => 'login',
                'action' => [AuthController::class, 'login']
            ],
            [
                'method' => 'GET',
                'path' => '/auth/register',
                'name' => 'register_user',
                'action' => [AuthController::class, 'register']
            ],
            [
                'method' => 'POST',
                'path' => '/auth/login',
                'name' => 'connect_user',
                'action' => [AuthController::class, 'connect']
            ],
            [
                'method' => 'POST',
                'path' => '/auth/register',
                'name' => 'create_user',
                'action' => [AuthController::class, 'create']
            ],
            
            [
                'method' => 'GET',
                'path' => '/auth/logout',
                'name' => 'logout',
                'action' => [AuthController::class, 'logout']
            ]
        ];
    }

    /**
     * User routes
     */
    private static function users() {
        return [
            [
                'method' => 'POST',
                'path' => '/profile/resend-email-validation',
                'name' => 'resend_email_validation',
                'action' => [UserController::class, 'resend_email_validation']
            ],
            [
                'method' => 'GET',
                'path' => '/profile/validation-needed',
                'name' => 'validation_needed',
                'action' => [UserController::class, 'validation_needed']
            ],
            [
                'method' => 'GET',
                'path' => '/profile/validate',
                'query' => 'token',
                'name' => 'validate_email',
                'action' => [UserController::class, 'validate']
            ],
            [
                'method' => 'GET',
                'path' => '/profile',
                'name' => 'profile',
                'action' => [UserController::class, 'profile']
            ],
            [
                'method' => 'GET',
                'path' => '/users',
                'name' => 'users',
                'action' => [UserController::class, 'index']
            ],
            [
                'method' => 'GET',
                'path' => '/user/{id}/edit',
                'name' => 'edit_user',
                'action' => [UserController::class, 'edit']
            ],
            [
                'method' => 'GET',
                'path' => '/user/{id}',
                'name' => 'user',
                'action' => [UserController::class, 'show']
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
            ],
        ];
    }

    /**
     * Post routes
     */
    private static function posts() {
        return [
            [
                'method' => 'GET',
                'path' => '/posts',
                'name' => 'posts',
                'action' => [PostController::class, 'index']
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
                'method' => 'GET',
                'path' => '/post/{id}',
                'name' => 'post',
                'action' => [PostController::class, 'show']
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
    private static function pages() {
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
                'path' => '/{slug}/edit',
                'name' => 'edit_page',
                'action' => [PageController::class, 'edit']
            ],
            [
                'method' => 'GET',
                'path' => '/page/create',
                'name' => 'create_page',
                'action' => [PageController::class, 'create']
            ],
            [
                'method' => 'POST',
                'path' => '/page',
                'name' => 'store_page',
                'action' => [PageController::class, 'store']
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
    public static function errors() {
        return [
            [
                'method' => 'GET',
                'path' => '/error/{code}',
                'name' => 'error',
                'action' => [PageController::class, 'error']
            ]
        ];
    }

    /**
     * Redirect to setup page if not installed
     */
    public static function setup() {
        return [
            [
                'method' => 'GET',
                'path' => '/install',
                'name' => 'install',
                'action' => [PageController::class, 'install']
            ],
            [
                'method' => 'POST',
                'path' => '/setup',
                'name' => 'setup',
                'action' => [PageController::class, 'setup']
            ]
        ];
    }
}