<?php

namespace Camagru\core\database;

use Camagru\core\database\Database;

abstract class Migration {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    protected function createTable($tableName, array $columns) {
        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (";
        $sql .= implode(', ', $columns);
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $this->db->execute($sql);
    }

    protected function dropTable($tableName) {
        $sql = "DROP TABLE IF EXISTS {$tableName};";
        return $this->db->execute($sql);
    }
}