<?php

namespace Camagru\core\database;

use PDO;
use PDOException;
use Camagru\helpers\Env;

/**
 * Class Connection
 * Handles the database connection using PDO.
 */
class Connection
{
    /**
     * Establishes a connection to the database.
     *
     * @return PDO The PDO instance representing the database connection.
     * @throws PDOException If the connection fails.
     */
    public static function connect()
    {
        // Retrieve database connection settings from environment variables
        $host = Env::get('DB_HOST', 'mysql8_camagru');
        $dbname = Env::get('DB_DATABASE', 'camagru');
        $username = Env::get('DB_USERNAME', 'camagru');
        $password = Env::get('DB_PASSWORD', 'camagru');

        try {
            // Create a new PDO instance and set the required attributes
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $pdo;
        } catch (PDOException $e) {
            // Handle connection failure
            die("Could not connect to the database $dbname: " . $e->getMessage());
        }
    }
}
