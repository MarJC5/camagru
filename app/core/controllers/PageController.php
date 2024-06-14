<?php

namespace Camagru\core\controllers;

use Camagru\core\models\Page;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\middlewares\Validation;
use Camagru\core\database\Runner;
use Camagru\core\database\Database;
use Camagru\helpers\CSRF;

use function Camagru\loadView;

/**
 * Class PageController
 * Handles actions related to pages, such as displaying, creating, editing, and deleting pages.
 */
class PageController
{
    /**
     * Display the homepage.
     * 
     * @return void
     */
    public static function index()
    {
        $page = Page::where('slug', 'home')->first();

        if (empty($page)) {
            Router::redirect('error', ['code' => 404]);
        }

        $_GET['title'] = $page->title();

        echo loadView('page/index.php', [
            'page' => $page,
        ]);
    }

    /**
     * Display a specific page based on its slug.
     * 
     * @param array $data The data array containing 'slug'.
     * @return void
     */
    public static function show($data)
    {
        $page = Page::where('slug', $data['slug'])->first();

        if (empty($page)) {
            Router::redirect('error', ['code' => 404]);
        }

        $_GET['title'] = $page->title();

        echo loadView('page/show.php', [
            'page' => $page,
        ]);
    }

    /**
     * Display the page creation form.
     * 
     * @return void
     */
    public static function create()
    {
        $_GET['title'] = 'New page';

        echo loadView('page/create.php', [
            'form' => loadView('page/form/_form.php', [
                'type' => 'create',
                'old' => [
                    'title' => '',
                    'slug' => '',
                    'content' => ''
                ],
            ])
        ]);
    }

    /**
     * Store a new page in the database.
     * 
     * @return void
     */
    public static function store()
    {
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_create_page'], 'csrf_create_page')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

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

    /**
     * Display the edit page form.
     * 
     * @param array $data The data array containing 'slug'.
     * @return void
     */
    public static function edit($data)
    {
        $page = Page::where('slug', $data['slug'])->first();

        if (empty($page)) {
            Router::redirect('error', ['code' => 404]);
        }

        $_GET['title'] = $page->title() . ' - Edit';

        echo loadView('page/edit.php', [
            'page' => $page,
        ]);
    }

    /**
     * Update a page in the database.
     * 
     * @param array $data The data array containing 'slug' and other page data.
     * @return void
     */
    public static function update($data)
    {
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_update_page'], 'csrf_update_page')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

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

    /**
     * Delete a page from the database.
     * 
     * @param array $data The data array containing 'slug'.
     * @return void
     */
    public static function delete($data)
    {
        // Verify the CSRF token
        if (!CSRF::verify($_POST['csrf_delete_page'], 'csrf_delete_page')) {
            Session::set('error', 'Invalid CSRF token');
            Router::redirect('login');
        }

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

    /**
     * Display the error page.
     * 
     * @param array $data The data array containing 'code'.
     * @return void
     */
    public static function error($data)
    {
        $code = $data['code'];
        http_response_code($code);
        $_GET['title'] = $code;
        echo loadView('page/error.php', [
            'title' => $code
        ]);
    }

    /**
     * Display the installation page if the application is not migrated.
     * 
     * @return void
     */
    public static function install()
    {
        if (Runner::isMigrated()) {
            Router::redirect('home');
        }

        echo loadView('page/install.php');
    }

    /**
     * Run the setup process, including running migrations and seeders.
     * 
     * @return void
     */
    public static function setup()
    {
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
                'MediaSeeders',
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

    /**
     * Return all pages as JSON.
     * 
     * @return array
     */
    public static function json()
    {
        $pages = Page::all();

        if (empty($pages)) {
            return ['message' => 'No page found'];
        }

        return $pages;
    }

    /**
     * Return a specific page as JSON based on its slug.
     * 
     * @param array $data The data array containing 'slug'.
     * @return array
     */
    public static function show_json($data)
    {
        $slug = $data['slug'];
        $page = Page::where('slug', $slug)->first();

        if (empty($page)) {
            return ['message' => 'No page found'];
        }

        return $page->toArray();
    }
}
