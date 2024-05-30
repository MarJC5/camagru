<?php

namespace Camagru\controllers;

use Camagru\models\Page;
use function Camagru\views_path;

class PageController {

    public static function index() {
        // Search in the database for the page with the given slug
        $page = Page::where('slug', 'home');

        // If the page does not exist, return a 404 error
        if (empty($page)) {
            return self::error(404);
        }

        ob_start();
        include views_path('page/index.php');
        echo ob_get_clean();
    }

    public static function show($slug) {
        // Search in the database for the page with the given slug
        $page = Page::where('slug', $slug);

        // If the page does not exist, return a 404 error
        if (empty($page)) {
            return self::error(404);
        }

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