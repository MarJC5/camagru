<?php

namespace Camagru\core\controllers;

use Camagru\core\models\Like;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\middlewares\Auth;
use Camagru\helpers\CSRF;
use Camagru\helpers\Logger;

/**
 * Class LikeController
 * Handles actions related to likes, such as storing and deleting likes.
 */
class LikeController 
{
    /**
     * Store a new like.
     * 
     * This method expects 'user_id' and 'post_id' to be present in the POST request.
     * It redirects to the post page with a success or error message based on the operation result.
     */
    public static function store() 
    {
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_like_' . $_POST['post_id'] . '_' . Session::currentUser()->id()], 'csrf_like_' . $_POST['post_id'] . '_' . Session::currentUser()->id())) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }
        // Validate the presence of required parameters
        if (!isset($_POST['user_id']) || !isset($_POST['post_id'])) {
            Session::set('error', 'Invalid request, missing parameters');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }

        // Prepare data for insertion
        $like = new Like();
        $data = [
            'user_id' => $_POST['user_id'],
            'post_id' => $_POST['post_id'],
        ];
        
        // Attempt to insert the like
        $status = $like->insert($data);

        // Redirect based on the result of the insertion
        if ($status) {
            $like->notify($_POST['user_id'], $_POST['post_id']);
            
            Session::set('success', 'Liked');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        } else {
            Session::set('error', 'Failed to add like');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }
    }

    /**
     * Delete a like.
     * 
     * This method expects 'id' to be present in the POST request.
     * It checks if the like exists and if the user has permission to delete it.
     * It redirects to the post page with a success or error message based on the operation result.
     */
    public static function delete() 
    {
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_unlike_' . $_POST['post_id'] . '_' . Session::currentUser()->id()], 'csrf_unlike_' . $_POST['post_id'] . '_' . Session::currentUser()->id())) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

        // Validate the presence of required parameters
        if (!isset($_POST['id']) || !isset($_POST['post_id'])) {
            Session::set('error', 'Invalid request, missing parameters');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }

        // Retrieve the like ID from the POST request
        $id = $_POST['id'];
        $like = new Like($id);

        // Check if the like exists
        if (empty($like)) {
            Session::set('error', 'Invalid like');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }

        // Check if the user has permission to delete the like
        if (!Auth::handle('self', ['id' => $like->user()])) {
            return;
        }

        // Attempt to delete the like
        $status = $like->delete();

        // Redirect based on the result of the deletion
        if ($status) {
            Session::set('success', 'Unlike');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        } else {
            Session::set('error', 'Failed to delete like');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }
    }
}
