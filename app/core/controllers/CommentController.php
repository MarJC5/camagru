<?php

namespace Camagru\core\controllers;

use Camagru\core\models\Comment;
use Camagru\helpers\Logger;
use Camagru\helpers\Session;
use Camagru\routes\Router;

class CommentController {
    public static function store() {
        if (!isset($_POST['comment']) || !isset($_POST['user_id']) || !isset($_POST['post_id'])) {
            Session::set('error', 'Invalid request, missing parameters');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }

        $comment = new Comment();
        $data = [
            'comment' => $_POST['comment'],
            'user_id' => $_POST['user_id'],
            'post_id' => $_POST['post_id'],
        ];
        
        $status = $comment->insert($data);

        if ($status) {
            Session::set('success', 'Comment added');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        } else {
            Session::set('error', 'Failed to add comment');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }
    }

    public static function destroy($data) {
        $id = $data['id'];
        $comment = new Comment($id);

        if (empty($comment)) {
            Session::set('error', 'Invalid comment');
            Router::redirect('error', ['code' => 404]);
        }

        $status = $comment->delete();

        if ($status) {
            Session::set('success', 'Comment deleted');
            Router::redirect('post', ['id' => $comment->post()]);
        } else {
            Session::set('error', 'Failed to delete comment');
            Router::redirect('post', ['id' => $comment->post()]);
        }
    }
}