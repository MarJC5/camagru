<?php

namespace Camagru\controllers;

use Camagru\models\Post;
use Camagru\controllers\PageController;
use function Camagru\views_path;

class PostController {
    public static function index() {
        $posts = Post::all();

        ob_start();
        include views_path('post/index.php');
        echo ob_get_clean();
    }

    public static function show($id) {
        $post = new Post($id);

        if (empty($post)) {
            return PageController::error(404);
        }

        ob_start();
        include views_path('post/show.php');
        echo ob_get_clean();
    }

    public static function edit($id) {
        $post = new Post($id);

        ob_start();
        include views_path('post/edit.php');
        echo ob_get_clean();
    }

    public static function create() {
    }

    public static function store() {
    }

    public static function update($id, $data) {
        $post = new Post($id);

        if (empty($post)) {
            return PageController::error(404);
        }

        $post->update($data);
    }

    public static function delete($id) {
        $post = new Post($id);

        if (empty($post)) {
            return PageController::error(404);
        }

        $post->delete();
    }
}