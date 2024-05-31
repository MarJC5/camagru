<?php

namespace Camagru\core\database;

use PDO;
use PDOException;
use Camagru\helpers\Env;

class Connection {
    public static function connect() {
        $host = Env::get('DB_HOST', 'mysql8_camagru');
        $dbname = Env::get('DB_DATABASE', 'camagru');
        $username = Env::get('DB_USERNAME', 'camagru');
        $password = Env::get('DB_PASSWORD', 'camagru');

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            return $pdo;
        } catch (PDOException $e) {
            die("Could not connect to the database $dbname: " . $e->getMessage());
        }
    }
}