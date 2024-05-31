<?php

namespace Camagru\core\controllers;

use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\models\User;
use Camagru\core\middlewares\Validation;
use Camagru\core\controllers\PageController;
use function Camagru\loadView;

class UserController {
    public static function index() {
        $users = User::all();

        $_GET['title'] = 'Users';

        echo loadView('user/index.php', [
            'users' => $users,
        ]);
    }

    public static function profile() {
        $user = new User($_SESSION['user']);

        $_GET['title'] = $user->username();

        echo loadView('user/profile.php', [
            'user' => $user,
        ]);
    }

    public static function show($id) {
        $user = new User($id);

        if (empty($user)) {
            return PageController::error(404);
        }

        $_GET['title'] = $user->username();

        echo loadView('user/show.php', [
            'user' => $user,
        ]);
    }

    public static function edit($id) {
        $user = new User($id);

        if (empty($user)) {
            return PageController::error(404);
        }

        $_GET['title'] = $user->username() . ' - Edit';

        echo loadView('user/edit.php', [
            'user' => $user,
        ]);
    }

    public static function update($id, $data) {
        $user = new User($id);

        if (empty($user)) {
            return PageController::error(404);
        }

        $validation = new Validation();
        $rules = $user->validation();
        $validation->validate($data, $rules);

        if ($validation->fails()) {
            $errors = $validation->getErrors();

            Session::set('error', $errors);
            Router::redirect('edit_user', ['id' => $id]);
        } else {
            $status = $user->update($data);

            if ($status) {
                Session::set('success', 'User updated successfully');
                Router::redirect('user', ['id' => $id]);
            } else {
                Session::set('error', 'An error occurred while updating the user');
                Router::redirect('edit_user', ['id' => $id]);
            }
        }
    }

    public static function delete($id) {
        $user = new User($id);

        if (empty($user)) {
            return PageController::error(404);
        }

        $status = $user->delete();

        if ($status) {
            Session::set('success', 'User deleted successfully');
        } else {
            Session::set('error', 'An error occurred while deleting the user');
        }
    }
}