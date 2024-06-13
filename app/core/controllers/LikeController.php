<?php

namespace Camagru\core\controllers;

use Camagru\core\models\Like;
use Camagru\helpers\Session;
use Camagru\routes\Router;

class LikeController {
    public static function store() {
        if (!isset($_POST['user_id']) || !isset($_POST['post_id'])) {
            Session::set('error', 'Invalid request, missing parameters');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }

        $like = new Like();
        $data = [
            'user_id' => $_POST['user_id'],
            'post_id' => $_POST['post_id'],
        ];
        
        $status = $like->insert($data);

        if ($status) {
            Router::redirect('post', ['id' => $_POST['post_id']]);
        } else {
            Session::set('error', 'Failed to add like');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }
    }

    public static function delete() {
        $id = $_POST['id'];
        $like = new Like($id);

        if (empty($like)) {
            Session::set('error', 'Invalid like');
            Router::redirect('post', ['id' => $_POST['post_id']]);
        }

        $status = $like->delete();

        if ($status) {
            Router::redirect('post', ['id' => $like->post()]);
        } else {
            Session::set('error', 'Failed to delete like');
            Router::redirect('post', ['id' => $like->post()]);
        }
    }
}