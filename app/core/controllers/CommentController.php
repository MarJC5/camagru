<?php

namespace Camagru\core\controllers;

use Camagru\core\models\Comment;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\middlewares\Auth;

/**
 * Class CommentController
 * Handles actions related to comments, such as storing and deleting comments.
 */
class CommentController 
{
    /**
     * Store a new comment.
     * 
     * This method expects 'comment', 'user_id', and 'post_id' to be present in the POST request.
     * It redirects to the post page with a success or error message based on the operation result.
     */
    public static function store() 
    {
        // Validate the presence of required parameters
        if (!isset($_POST['comment']) || !isset($_POST['user_id']) || !isset($_POST['post_id'])) {
            Session::set('error', 'Invalid request, missing parameters');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }

        // Prepare data for insertion
        $comment = new Comment();
        $data = [
            'comment' => $_POST['comment'],
            'user_id' => $_POST['user_id'],
            'post_id' => $_POST['post_id'],
        ];
        
        // Attempt to insert the comment
        $status = $comment->insert($data);

        // Redirect based on the result of the insertion
        if ($status) {
            Session::set('success', 'Comment added');
        } else {
            Session::set('error', 'Failed to add comment');
        }
        Router::redirect('post', ['id' => $_POST['post_id']]);
    }

    /**
     * Delete a comment.
     * 
     * This method expects 'id' to be present in the data array.
     * It checks if the comment exists and if the user has permission to delete it.
     * It redirects to the post page with a success or error message based on the operation result.
     * 
     * @param array $data The data array containing the comment ID.
     */
    public static function delete($data) 
    {
        // Retrieve the comment ID from the data array
        $id = $data['id'];
        $comment = new Comment($id);

        // Check if the comment exists
        if (empty($comment)) {
            Session::set('error', 'Invalid comment');
            Router::redirect('error', ['code' => 404]);
        }

        // Check if the user has permission to delete the comment
        if (!Auth::handle('admin|self', ['id' => $comment->user()])) {
            return;
        }

        // Attempt to delete the comment
        $status = $comment->delete();

        // Redirect based on the result of the deletion
        if ($status) {
            Session::set('success', 'Comment deleted');
        } else {
            Session::set('error', 'Failed to delete comment');
        }
        Router::redirect('post', ['id' => $comment->post()]);
    }
}
