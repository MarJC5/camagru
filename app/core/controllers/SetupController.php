<?php

namespace Camagru\core\controllers;

use Camagru\helpers\Sanitize;
use Camagru\helpers\Session;
use Camagru\routes\Router;
use Camagru\core\database\Runner;
use Camagru\core\database\Database;

use function Camagru\loadView;

/**
 * Class SetupController
 * Handles actions related to setting up the application.
 */
class SetupController
{
    /**
     * Run the setup process, including running migrations and seeders.
     * 
     * @return void
     */
    public static function setup()
    {
        // Sanitize the data
        $_POST = Sanitize::escapeArray($_POST);

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
     * Display the installation page if the application is not migrated.
     * 
     * @return void
     */
    public static function install()
    {
        if (Runner::isMigrated()) {
            Router::redirect('home');
        }

        echo loadView('setup/install.php');
    }
}
