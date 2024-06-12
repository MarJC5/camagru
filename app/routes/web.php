<?php 

namespace Camagru\routes;

use Camagru\core\controllers\PageController;
use Camagru\core\controllers\PostController;
use Camagru\core\controllers\UserController;
use Camagru\core\controllers\AuthController;
use Camagru\core\controllers\MediaController;

class Web {
    public static function routes() {
        return array_merge(
            self::errors(),
            self::setup(),
            self::users(),
            self::posts(),
            self::pages(),
            self::media(),
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
                'path' => '/profile/toggle-notification',
                'name' => 'toggle_notification',
                'action' => [UserController::class, 'toggle_notification']
            ],
            [
                'method' => 'POST',
                'path' => '/profile/edit-role',
                'name' => 'edit_role',
                'action' => [UserController::class, 'edit_role']
            ],
            [
                'method' => 'POST',
                'path' => '/profile/resend-email-validation',
                'name' => 'resend_email_validation',
                'action' => [UserController::class, 'resend_email_validation']
            ],
            [
                'method' => 'POST',
                'path' => '/profile/reset-password',
                'name' => 'reset_password_request',
                'action' => [UserController::class, 'reset_password_request']
            ],
            [
                'method' => 'POST',
                'path' => '/profile/new-password',
                'name' => 'new_password',
                'action' => [UserController::class, 'new_password']
            ],
            [
                'method' => 'GET',
                'path' => '/profile/reset-password',
                'name' => 'reset_password',
                'query' => ['token', 'id'],
                'action' => [UserController::class, 'reset_password']
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
                'query' => ['token'],
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
                'method' => 'POST',
                'path' => '/user',
                'name' => 'update_user',
                'action' => [UserController::class, 'update']
            ],
            [
                'method' => 'POST',
                'path' => '/user',
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
                'secure' => 'admin|author',
                'action' => [PostController::class, 'edit']
            ],
            [
                'method' => 'GET',
                'path' => '/post/create',
                'name' => 'create_post',
                'secure' => 'authentified',
                'action' => [PostController::class, 'create']
            ],
            [
                'method' => 'POST',
                'path' => '/post',
                'name' => 'store_post',
                'secure' => 'authentified',
                'action' => [PostController::class, 'store']
            ],
            [
                'method' => 'GET',
                'path' => '/post/{id}',
                'name' => 'post',
                'action' => [PostController::class, 'show']
            ],
            [
                'method' => 'POST',
                'path' => '/post/{id}',
                'name' => 'update_post',
                'secure' => 'authentified',
                'action' => [PostController::class, 'update']
            ],
            [
                'method' => 'POST',
                'path' => '/post/{id}',
                'name' => 'delete_post',
                'secure' => 'admin|author',
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
                'path' => '/{id}/edit',
                'name' => 'edit_page',
                'secure' => 'admin',
                'action' => [PageController::class, 'edit']
            ],
            [
                'method' => 'GET',
                'path' => '/page/create',
                'name' => 'create_page',
                'secure' => 'admin',
                'action' => [PageController::class, 'create']
            ],
            [
                'method' => 'POST',
                'path' => '/page',
                'name' => 'store_page',
                'secure' => 'admin',
                'action' => [PageController::class, 'store']
            ],
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

    /**
     * Media routes
     */
    public static function media() {
        return [
            [
                'method' => 'POST',
                'path' => '/media/upload',
                'name' => 'upload_media',
                'action' => [MediaController::class, 'upload']
            ],
            [
                'method' => 'POST',
                'path' => '/media/delete',
                'name' => 'delete_media',
                'action' => [MediaController::class, 'delete']
            ],
            [
                'method' => 'GET',
                'path' => '/media/{id}',
                'name' => 'media_show',
                'action' => [MediaController::class, 'show']
            ],
        ];
    }
}