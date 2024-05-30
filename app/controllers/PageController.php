<?php

namespace Camagru\controllers;

use Camagru\models\Page;
use function Camagru\loadView;

class PageController {

    public static function index() {
        $page = Page::where('slug', 'home')->first();

        if (empty($page)) {
            return self::error(404);
        }

        $_GET['title'] = $page->title();

        echo loadView('page/index.php', [
            'page' => $page,
        ]);
    }

    public static function show($slug) {
        $page = Page::where('slug', $slug)->first();

        if (empty($page)) {
            return self::error(404);
        }

        $_GET['title'] = $page->title();

        echo loadView('page/show.php', [
            'page' => $page,
        ]);
    }

    public static function edit($slug) {
        $page = Page::where('slug', $slug)->first();

        if (empty($page)) {
            return self::error(404);
        }

        $_GET['title'] = $page->title() . ' - Edit';

        echo loadView('page/edit.php', [
            'page' => $page,
        ]);
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

        $_GET['title'] = $code;

        echo loadView('page/error.php', [
            'title' => $code
        ]);
    }

}