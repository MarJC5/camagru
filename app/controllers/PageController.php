<?php

namespace Camagru\controllers;

use function Camagru\views_path;

class PageController {

    public static function index() {
        ob_start();
        include views_path('page/index.php');
        echo ob_get_clean();
    }

    public static function show($slug) {
        ob_start();
        include views_path('page/show.php');
        echo ob_get_clean();
    }

    public static function error($code) {
        http_response_code($code);
        ob_start();
        include views_path('page/error.php');
        echo ob_get_clean();
    }

}