<?php

namespace Camagru\core\controllers;

use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\models\Media;
use Camagru\core\models\Post;
use Camagru\core\models\User;
use Camagru\core\middlewares\Validation;
use Camagru\helpers\Logger;

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

    public static function show($data) {
        $id = $data['id'];
        $post = new Post($id);

        if (empty($post)) {
            Session::set('error', 'Invalid post');
            Router::redirect('error', ['code' => 404]);
        }

        echo loadView('post/show.php', [
            'post' => $post,
        ]);
    }

    public static function edit($id) {
        // TODO : Check csrf_token validity

        $post = new Post($id);

        if (empty($post)) {
            Session::set('error', 'Invalid post');
            Router::redirect('error', ['code' => 404]);
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
        // TODO : Check csrf_token validity

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
        // TODO : Check csrf_token validity

        $post = new Post($id);

        if (empty($post)) {
            Session::set('error', 'Invalid post');
            Router::redirect('error', ['code' => 404]);
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
        // TODO : Check csrf_token validity
        
        $post = new Post($id);

        if (empty($post)) {
            Session::set('error', 'Invalid post');
            Router::redirect('error', ['code' => 404]);
        }

        $status = $post->delete();

        if ($status) {
            Session::set('success', 'Post deleted successfully');
        } else {
            Session::set('error', 'An error occurred while deleting the post');
        }
    }

    public static function json($data) {
        $user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
        $page = isset($data['page']) ? (int)$data['page'] : 1;
        $limit = isset($data['limit']) ? $data['limit'] : Post::PAGINATE; // Number of posts per page
        $offset = ($page - 1) * $limit;
        $params = [];

        if ($user_id) {
            $params['key'] = 'user_id';
            $params['value'] = $user_id;
        }

        $posts = [];
        if ($user_id) {
            $posts = Post::paginate($offset, $limit, $params);
        } else {
            $posts = Post::paginate($offset, $limit);
        }

        if (empty($posts)) {
            return ['message' => 'No posts found'];
        }

        // Map each user_id to the actual user
        foreach ($posts as &$post) {
            $post = Post::where('id', $post['id'])->first()->toJSON();
        }
        unset($post); 

        return $posts;
    }
}