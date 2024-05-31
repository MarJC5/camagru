<?php 

namespace Camagru;

use Camagru\routes\Router;
use Camagru\helpers\Env;
use Camagru\helpers\Session;

// Define the base path of the application one level above the public directory
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/helpers/autoloader.php';
require_once BASE_PATH . '/main.php';

// Load environment variables
Env::load(BASE_PATH . '/.env');

// Define the base URL of the application
define('BASE_URL', Env::get('APP_URL'));

// Configure error reporting based on the environment
if (Env::get('APP_ENV') === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Start the session
Session::start();

// Route the request using the HTTP method and URI
Router::route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);