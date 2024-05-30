<?php

namespace Camagru\controllers;

use Camagru\models\User;
use function Camagru\loadView;

class UserController {
    public static function index() {
        $users = User::all();

        $_GET['title'] = 'Users';

        echo loadView('user/index.php', [
            'users' => $users,
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

    public static function create() {
    }

    public static function store() {
    }

    public static function update($id, $data) {
        $user = new User($id);

        if (empty($user)) {
            return PageController::error(404);
        }

        $user->update($data);
    }

    public static function delete($id) {
        $user = new User($id);

        if (empty($user)) {
            return PageController::error(404);
        }

        $user->delete();
    }
}