<?php 

namespace Camagru\routes;

use Camagru\core\controllers\PageController;
use Camagru\core\controllers\PostController;
use Camagru\core\controllers\UserController;
use Camagru\core\controllers\AuthController;
use Camagru\core\controllers\MediaController;
use Camagru\core\controllers\CommentController;
use Camagru\core\controllers\LikeController;
use Camagru\core\controllers\ErrorController;
use Camagru\core\controllers\SetupController;

/**
 * Class Web
 * Defines the Web routes for the application.
 */
class Web {

     /**
     * Get all API routes.
     *
     * @return array
     */
    public static function routes() {
        return array_merge(
            self::setup(),
            self::errors(),
            self::comments(),
            self::likes(),
            self::media(),
            self::auth(),
            self::users(),
            self::posts(),
            self::pages(),
        );
    }

    /**
     * Get the routes for authentication.
     * 
     * @return array
     */
    private static function auth() {
        return [
            [
                'method' => 'GET',
                'path' => '/login',
                'name' => 'login',
                'action' => [AuthController::class, 'login']
            ],
            [
                'method' => 'GET',
                'path' => '/register',
                'name' => 'register_user',
                'action' => [AuthController::class, 'register']
            ],
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
     * Get the routes for users.
     * 
     * @return array
     */
    private static function users() {
        return [
            [
                'method' => 'POST',
                'path' => '/profile/toggle-notification',
                'name' => 'toggle_notification',
                'secure' => 'authentified',
                'action' => [UserController::class, 'toggle_notification']
            ],
            [
                'method' => 'POST',
                'path' => '/profile/edit-role',
                'name' => 'edit_role',
                'secure' => 'admin',
                'action' => [UserController::class, 'edit_role']
            ],
            [
                'method' => 'POST',
                'path' => '/profile/resend-email-validation',
                'name' => 'resend_email_validation',
                'secure' => 'authentified',
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
                'secure' => 'authentified',
                'action' => [UserController::class, 'validation_needed']
            ],
            [
                'method' => 'GET',
                'path' => '/profile/validate',
                'query' => ['token'],
                'name' => 'validate_email',
                'secure' => 'authentified',
                'action' => [UserController::class, 'validate']
            ],
            [
                'method' => 'GET',
                'path' => '/profile',
                'name' => 'profile',
                'secure' => 'authentified',
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
                'secure' => 'authentified',
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
                'secure' => 'authentified',
                'action' => [UserController::class, 'update']
            ],
            [
                'method' => 'POST',
                'path' => '/user',
                'name' => 'delete_user',
                'secure' => 'authentified',
                'action' => [UserController::class, 'delete']
            ],
        ];
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
                'path' => '/posts',
                'name' => 'posts',
                'action' => [PostController::class, 'index']
            ],
            [
                'method' => 'GET',
                'path' => '/post/{id}/edit',
                'name' => 'edit_post',
                'secure' => 'authentified',
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
                'path' => '/post/store',
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
                'path' => '/post/delete',
                'name' => 'delete_post',
                'secure' => 'authentified',
                'action' => [PostController::class, 'delete']
            ]
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
            [
                'method' => 'POST',
                'path' => '/page/{id}',
                'name' => 'update_page',
                'secure' => 'admin',
                'action' => [PageController::class, 'update']
            ],
            [
                'method' => 'POST',
                'path' => '/page/{id}',
                'name' => 'delete_page',
                'secure' => 'admin',
                'action' => [PageController::class, 'delete']
            ]
        ];
    }

    /**
     * Get the routes for comments.
     * 
     * @return array
     */
    private static function comments() {
        return [
            [
                'method' => 'POST',
                'path' => '/comment',
                'name' => 'comment',
                'secure' => 'authentified',
                'action' => [CommentController::class, 'store']
            ],
            [
                'method' => 'POST',
                'path' => '/comment/{id}',
                'name' => 'delete_comment',
                'secure' => 'authentified',
                'action' => [CommentController::class, 'delete']
            ]
        ];
    }

    /**
     * Get the routes for likes.
     * 
     * @return array
     */
    private static function likes() {
        return [
            [
                'method' => 'POST',
                'path' => '/like',
                'name' => 'like',
                'secure' => 'authentified',
                'action' => [LikeController::class, 'store']
            ],
            [
                'method' => 'POST',
                'path' => '/unlike',
                'name' => 'unlike',
                'secure' => 'authentified',
                'action' => [LikeController::class, 'delete']
            ]
        ];
    }

    /**
     * Get the routes for errors.
     * 
     * @return array
     */
    public static function errors() {
        return [
            [
                'method' => 'GET',
                'path' => '/error/{code}',
                'name' => 'error',
                'action' => [ErrorController::class, 'error']
            ]
        ];
    }

    /**
     * Get the routes for setup.
     * 
     * @return array
     */
    private static function setup() {
        return [
            [
                'method' => 'GET',
                'path' => '/install',
                'name' => 'install',
                'action' => [SetupController::class, 'install']
            ],
            [
                'method' => 'POST',
                'path' => '/setup',
                'name' => 'setup',
                'action' => [SetupController::class, 'setup']
            ]
        ];
    }

    /**
     * Get the routes for media.
     * 
     * @return array
     */
    private static function media() {
        return [
            [
                'method' => 'POST',
                'path' => '/media/upload',
                'name' => 'upload_media',
                'secure' => 'authentified',
                'action' => [MediaController::class, 'upload']
            ],
            [
                'method' => 'POST',
                'path' => '/media/delete',
                'name' => 'delete_media',
                'secure' => 'authentified',
                'action' => [MediaController::class, 'delete']
            ],
            [
                'method' => 'GET',
                'path' => '/medias/{filename}',
                'name' => 'media_show',
                'action' => [MediaController::class, 'show']
            ],
        ];
    }
}