<?php

namespace Camagru\controllers;

use Camagru\models\Post;
use Camagru\middlewares\Validation;
use Camagru\controllers\PageController;
use function Camagru\loadView;

class PostController {
    public static function index() {
        $posts = Post::all();

        // Count all posts
        $total = Post::count();

        $_GET['title'] = 'Posts';

        echo loadView('post/index.php', [
            'posts' => $posts,
            'total' => $total,
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
        $_GET['title'] = 'New page';

        echo loadView('post/create.php');
    }

    public static function store() {
        $validation = new Validation();
        $page = new Post();

        $data = $_POST;
        $rules = $page->validation();

        $validation->validate($data, $rules);

        if ($validation->fails()) {
            $errors = $validation->getErrors();

            echo loadView('post/create.php', [
                'errors' => $errors,
                'old' => $data
            ]);
        }

        $page->insert($data);
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

    public static function json() {
        $posts = Post::all();

        if (empty($posts)) {
            return ['message' => 'No posts found'];
        }

        return $posts;
    }
}