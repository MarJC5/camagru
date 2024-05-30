<?php

namespace Camagru\controllers;

use Camagru\models\Post;
use Camagru\controllers\PageController;
use function Camagru\loadView;

class PostController {
    public static function index() {
        $posts = Post::all();

        $_GET['title'] = 'Posts';

        echo loadView('post/index.php', [
            'posts' => $posts,
        ]);
    }

    public static function show($id) {
        $post = new Post($id);

        if (empty($post)) {
            return PageController::error(404);
        }

        echo loadView('post/show.php', [
            'post' => $post,
        ]);
    }

    public static function edit($id) {
        $post = new Post($id);

        if (empty($post)) {
            return PageController::error(404);
        }

        echo loadView('post/edit.php', [
            'post' => $post,
        ]);
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