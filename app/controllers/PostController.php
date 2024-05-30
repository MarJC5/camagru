<?php

namespace Camagru\controllers;

use function Camagru\views_path;

class PostController {
    public static function index() {
        ob_start();
        include views_path('page/index.php');
        echo ob_get_clean();
    }

    public static function show($id) {
        ob_start();
        include views_path('post/show.php');
        echo ob_get_clean();
    }

    public static function edit($id) {
        ob_start();
        include views_path('post/edit.php');
        echo ob_get_clean();
    }

    public static function create() {
    }

    public static function store() {
    }

    public static function update($id) {
    }

    public static function delete($id) {
    }
}