<?php

namespace Camagru\core\controllers;

use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\models\Post;
use Camagru\core\middlewares\Validation;
use Camagru\core\controllers\PageController;
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

            Session::set('error', $errors);
            Router::redirect('create_post');
        } else {
            $status = $page->insert($data);

            if ($status) {
                Session::set('success', 'Post created successfully');
                Router::redirect('post', ['id' => $page->id()]);
            } else {
                Session::set('error', 'An error occurred while creating the post');
                Router::redirect('create_post');
            }
        }
    }

    public static function update($id, $data) {
        $post = new Post($id);

        if (empty($post)) {
            return PageController::error(404);
        }

        $validation = new Validation();
        $rules = $post->validation();
        $validation->validate($data, $rules);

        if ($validation->fails()) {
            $errors = $validation->getErrors();

            Session::set('error', $errors);
            Router::redirect('edit_post', ['id' => $id]);
        } else {
            $status = $post->update($data);

            if ($status) {
                Session::set('success', 'Post updated successfully');
                Router::redirect('post', ['id' => $id]);
            } else {
                Session::set('error', 'An error occurred while updating the post');
                Router::redirect('edit_post', ['id' => $id]);
            }
        }
    }

    public static function delete($id) {
        $post = new Post($id);

        if (empty($post)) {
            return PageController::error(404);
        }

        $status = $post->delete();

        if ($status) {
            Session::set('success', 'Post deleted successfully');
        } else {
            Session::set('error', 'An error occurred while deleting the post');
        }
    }

    public static function json() {
        $posts = Post::all();

        if (empty($posts)) {
            return ['message' => 'No posts found'];
        }

        return $posts;
    }
}