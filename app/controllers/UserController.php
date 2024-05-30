<?php

namespace Camagru\controllers;

use Camagru\models\User;
use function Camagru\views_path;

class UserController {
    public static function index() {
        $users = User::all();

        ob_start();
        include views_path('user/index.php');
        echo ob_get_clean();
    }

    public static function show($id) {
        $user = new User($id);

        ob_start();
        include views_path('user/show.php');
        echo ob_get_clean();
    }

    public static function edit($id) {
        $user = new User($id);

        ob_start();
        include views_path('user/edit.php');
        echo ob_get_clean();
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