<?php

namespace Camagru\core\database;

use Camagru\core\database\Database;

/**
 * Class Migration
 * Handles database migrations.
 */
class Migration
{
    /**
     * @var Database The database instance for executing queries.
     */
    protected $db;

    /**
     * Migration constructor.
     * Initializes the database connection.
     */
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Creates a new table.
     *
     * @param string $tableName The name of the table to create.
     * @param array $columns The columns of the table.
     * @return bool True on success, false on failure.
     */
    protected function createTable($tableName, array $columns)
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$tableName} (";
        $sql .= implode(', ', $columns);
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        return $this->db->execute($sql);
    }

    /**
     * Drops an existing table.
     *
     * @param string $tableName The name of the table to drop.
     * @return bool True on success, false on failure.
     */
    protected function dropTable($tableName)
    {
        $sql = "DROP TABLE IF EXISTS {$tableName};";
        return $this->db->execute($sql);
    }

    /**
     * Alters an existing table.
     *
     * @param string $tableName The name of the table to alter.
     * @param array $alterations The alterations to make.
     * @return bool True on success, false on failure.
     */
    protected function alterTable($tableName, array $alterations)
    {
        $sql = "ALTER TABLE {$tableName} ";
        $sql .= implode(', ', $alterations);
        $sql .= ";";
        return $this->db->execute($sql);
    }
}
