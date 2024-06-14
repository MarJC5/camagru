<?php

namespace Camagru\core\controllers;

use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\models\Post;
use Camagru\core\middlewares\Validation;
use Camagru\helpers\Logger;
use Camagru\core\middlewares\Auth;
use Camagru\helpers\CSRF;

use function Camagru\loadView;

/**
 * Class PostController
 * Handles actions related to posts, such as displaying, creating, editing, and deleting posts.
 */
class PostController
{
    /**
     * Display all posts.
     *
     * @return void
     */
    public static function index()
    {
        $posts = Post::all();
        $total = Post::count();
        
        $_GET['title'] = 'Posts';

        echo loadView('post/index.php', [
            'posts' => $posts,
            'total' => $total,
        ]);
    }

    /**
     * Display a specific post based on its ID.
     *
     * @param array $data The data array containing 'id'.
     * @return void
     */
    public static function show($data)
    {
        $id = $data['id'];
        $post = new Post($id);

        if (empty($post)) {
            Session::set('error', 'Invalid post');
            Router::redirect('error', ['code' => 404]);
        }

        echo loadView('post/show.php', $post->toJSON());
    }

    /**
     * Display the post creation form.
     *
     * @return void
     */
    public static function create()
    {
        $_GET['title'] = 'New page';

        echo loadView('post/create.php');
    }

    /**
     * Store a new post in the database.
     *
     * @return void
     */
    public static function store()
    {
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_store_post'], 'csrf_store_post')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

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

    /**
     * Display the edit post form.
     *
     * @param array $data The data array containing 'id'.
     * @return void
     */
    public static function edit($data)
    {
        $id = $data['id'];
        $post = new Post($id);

        if (empty($post)) {
            Session::set('error', 'Invalid post');
            Router::redirect('error', ['code' => 404]);
        }

        // Check if user is allowed to access this page
        if (!Auth::handle('admin|self', ['id' => $post->user()])) {
            return;
        }

        echo loadView('post/edit.php', [
            'post' => $post,
        ]);
    }

    /**
     * Update a post in the database.
     *
     * @param array $data The data array containing 'id' and other post data.
     * @return void
     */
    public static function update($data)
    {
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_update_post'], 'csrf_update_post')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

        $id = $data['id'];
        $post = new Post($id);

        if (empty($post)) {
            Session::set('error', 'Invalid post');
            Router::redirect('error', ['code' => 404]);
        }

        // Check if user is allowed to access this page
        if (!Auth::handle('admin|self', ['id' => $post->user()])) {
            return;
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

    /**
     * Delete a post from the database.
     *
     * @param array $data The data array containing 'id'.
     * @return void
     */
    public static function delete($data)
    {
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_delete_post'], 'csrf_delete_post')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

        $id = $data['id'];
        $post = new Post($id);

        if (empty($post)) {
            Session::set('error', 'Invalid post');
            Router::redirect('error', ['code' => 404]);
        }

        // Check if user is allowed to access this page
        if (!Auth::handle('admin|self', ['id' => $post->user()])) {
            return;
        }

        $status = $post->delete();
        if ($status) {
            Session::set('success', 'Post deleted successfully');
        } else {
            Session::set('error', 'An error occurred while deleting the post');
        }
    }

    /**
     * Return paginated posts as JSON.
     *
     * @param array $data The data array containing pagination and filter parameters.
     * @return array
     */
    public static function json($data)
    {
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

    /**
     * Return a specific post as JSON based on its ID.
     *
     * @param array $data The data array containing 'id'.
     * @return array
     */
    public static function show_json($data)
    {
        $id = $data['id'];
        $post = new Post($id);

        if (empty($post)) {
            return ['message' => 'Post not found'];
        }

        return $post->toJSON();
    }
}
