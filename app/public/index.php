<?php

namespace Camagru;

use Camagru\routes\Router;
use Camagru\helpers\Env;
use Camagru\helpers\Session;
use Camagru\helpers\CSRF;
use Camagru\helpers\Config;

// Define the base path of the application one level above the public directory
define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/helpers/autoloader.php';
require_once BASE_PATH . '/main.php';

// Load environment variables
Env::load(BASE_PATH . '/.env');

// Load the application configuration
Config::load(BASE_PATH . '/config/app.php');

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

// Set the Content-Security-Policy header
$nonce = CSRF::generate();
Session::set('nonce', $nonce);
// header("Content-Security-Policy: default-src 'self'; img-src 'self';script-src 'self' 'nonce-{$nonce}'; style-src 'self' 'nonce-{$nonce}' https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css https://fonts.googleapis.com; font-src https://fonts.gstatic.com;");

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
    // you want to allow, and if so:
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

// Set the session cookie to be httponly and secure
setcookie('name', 'value', ['httponly' => true, 'secure' => true]);

// Route the request using the HTTP method and URI
Router::route($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
