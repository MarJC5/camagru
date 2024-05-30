<?php

namespace Camagru\controllers;

use Camagru\models\Page;
use function Camagru\views_path;

class PageController {

    public static function index() {
        $page = Page::where('slug', 'home')->first();

        if (empty($page)) {
            return self::error(404);
        }

        ob_start();
        include views_path('page/index.php');
        echo ob_get_clean();
    }

    public static function show($slug) {
        $page = Page::where('slug', $slug)->first();

        if (empty($page)) {
            return self::error(404);
        }

        ob_start();
        include views_path('page/show.php');
        echo ob_get_clean();
    }

    public static function edit($slug) {
        $page = Page::where('slug', $slug)->first();

        if (empty($page)) {
            return self::error(404);
        }

        ob_start();
        include views_path('page/edit.php');
        echo ob_get_clean();
    }

    public static function create() {
    }

    public static function store() {
    }

    public static function update($slug, $data) {
        $page = Page::where('slug', $slug)->first();

        if (empty($page)) {
            return self::error(404);
        }

        $page->update($data);
    }

    public static function error($code) {
        http_response_code($code);
        ob_start();
        include views_path('page/error.php');
        echo ob_get_clean();
    }

}