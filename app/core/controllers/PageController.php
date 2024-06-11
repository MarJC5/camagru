<?php

namespace Camagru\core\controllers;

use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\models\Page;
use Camagru\core\database\Runner;
use Camagru\core\middlewares\Validation;
use Camagru\core\database\Database;
use function Camagru\loadView;

class PageController {

    public static function index() {
        $page = Page::where('slug', 'home')->first();

        if (empty($page)) {
            Router::redirect('error', ['code' => 404]);
        }

        $_GET['title'] = $page->title();

        echo loadView('page/index.php', [
            'page' => $page,
        ]);
    }

    public static function show($data) {
        $page = Page::where('slug', $data['slug'])->first();

        if (empty($page)) {
            Router::redirect('error', ['code' => 404]);
        }

        $_GET['title'] = $page->title();

        echo loadView('page/show.php', [
            'page' => $page,
        ]);
    }

    public static function edit($data) {
        $page = Page::where('slug', $data['slug'])->first();

        if (empty($page)) {
            Router::redirect('error', ['code' => 404]);
        }

        $_GET['title'] = $page->title() . ' - Edit';

        echo loadView('page/edit.php', [
            'page' => $page,
        ]);
    }

    public static function create() {
        $_GET['title'] = 'New page';

        echo loadView('page/create.php');
    }

    public static function store() {
        $validation = new Validation();
        $page = new Page();

        $data = $_POST;
        $rules = $page->validation();

        $validation->validate($data, $rules);

        if ($validation->fails()) {
            $errors = $validation->getErrors();

            Session::set('error', $errors);
            Router::redirect('create_page');
        } else {
            $status = $page->insert($data);

            if ($status) {
                Session::set('success', 'Page created successfully');
                Router::redirect('page', ['slug' => $data['slug']]);
            } else {
                Session::set('error', 'An error occurred while creating the page');
                Router::redirect('create_page');
            }
        }
    }

    public static function update($data) {
        $slug = $data['slug'];

        $page = Page::where('slug', $slug)->first();

        if (empty($page)) {
            Router::redirect('error', ['code' => 404]);
        }

        $validation = new Validation();
        $rules = $page->validation();
        $validation->validate($data, $rules);

        if ($validation->fails()) {
            $errors = $validation->getErrors();

            Session::set('error', $errors);
            Router::redirect('edit_page', ['slug' => $slug]);
        } else {
            $status = $page->update($data);
            
            if ($status) {
                Session::set('success', 'Page updated successfully');
                Router::redirect('page', ['slug' => $slug]);
            } else {
                Session::set('error', 'An error occurred while updating the page');
                Router::redirect('edit_page', ['slug' => $slug]);
            }
        }
    }

    public static function error($code) {
        if (is_array($code)) {
            $code = 404;
        }

        http_response_code($code);

        $_GET['title'] = $code;

        echo loadView('page/error.php', [
            'title' => $code
        ]);
    }

    public static function install()
    {
        if (Runner::isMigrated()) {
            Router::redirect('home');
        }

        echo loadView('page/install.php');
    }

    public static function setup() {
        $data = $_POST;

        // Check if the application has been migrated
        if (Runner::isMigrated()) {
            Session::set('error', 'Application has already been migrated');
            Router::redirect('home');
        }
        
        if (isset($data['install']) && Runner::isMigrated() === false) {
            $db = new Database();
            $db->trackMigration();

            $runner = new Runner($db);
            $runner->run();

            $seeders = [
                'UserSeeders',
                'PageSeeders',
                'PostSeeders',
                'CommentSeeders',
                'LikeSeeders',
            ];
    
            foreach ($seeders as $seeder) {
                $seeder = 'Camagru\\core\\database\\seeders\\' . $seeder;
                $seeder = new $seeder();
                if (method_exists($seeder, 'run')) {
                    $seeder->run();
                }
            }

            Session::set('success', 'Database migration successful');
            Router::redirect('home');
        }
    }

    public static function delete($data) {
        $slug = $data['slug'];

        $page = Page::where('slug', $slug)->first();

        if (empty($page)) {
            Router::redirect('error', ['code' => 404]);
        }

        $status = $page->delete();

        if ($status) {
            Session::set('success', 'Page deleted successfully');
        } else {
            Session::set('error', 'An error occurred while deleting the page');
        }
    }

    public static function json() {
        $pages = Page::all();

        if (empty($pages)) {
            return ['message' => 'No page found'];
        }

        return $pages;
    }

    public static function show_json($data) {
        $slug = $data['slug'];

        $page = Page::where('slug', $slug)->first();

        if (empty($page)) {
            return ['message' => 'No page found'];
        }

        return $page->toArray();
    }
}