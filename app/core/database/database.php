<?php

namespace Camagru\core\database;

use PDO;
use PDOException;

/**
 * Class Database
 * Manages database operations using PDO.
 */
class Database
{
    /**
     * @var PDO The PDO instance for database connection.
     */
    private $pdo;

    /**
     * Database constructor.
     * Initializes the database connection.
     */
    public function __construct()
    {
        $this->pdo = Connection::connect();
    }

    /**
     * Executes a SELECT query and returns the result.
     *
     * @param string $sql The SQL query.
     * @param array $params The parameters for the query.
     * @return array The query result.
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Executes an INSERT, UPDATE, or DELETE query.
     *
     * @param string $sql The SQL query.
     * @param array $params The parameters for the query.
     * @return bool True on success, false on failure.
     */
    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Safely escapes and quotes a value for SQL queries.
     *
     * @param mixed $value The value to quote.
     * @return string The quoted value.
     */
    public function quote($value)
    {
        return $this->pdo->quote($value);
    }

    /**
     * Begins a database transaction.
     *
     * @return bool True on success, false on failure.
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commits a database transaction.
     *
     * @return bool True on success, false on failure.
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Rolls back a database transaction.
     *
     * @return bool True on success, false on failure.
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     * Creates the migrations table if it doesn't exist.
     */
    public function trackMigration()
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $this->pdo->exec($sql);
    }

    /**
     * Records a migration in the migrations table.
     *
     * @param string $migrationName The name of the migration.
     * @param int $batch The batch number of the migration.
     */
    public function recordMigration($migrationName, $batch)
    {
        $sql = "INSERT INTO migrations (migration, batch) VALUES (?, ?)";
        $this->execute($sql, [$migrationName, $batch]);
    }

    /**
     * Retrieves the highest batch number from the migrations table.
     *
     * @return int The highest batch number.
     */
    public function getLastBatchNumber()
    {
        $sql = "SELECT MAX(batch) as max_batch FROM migrations";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int) $result['max_batch'] : 0;
    }

    /**
     * Inserts a record into a table.
     *
     * @param string $table The table name.
     * @param array $data The data to insert.
     * @return int The number of affected rows.
     */
    public function insert($table, $data)
    {
        $keys = array_keys($data);
        $values = array_map([$this->pdo, 'quote'], array_values($data));

        $columns = implode(', ', $keys);
        $valueStr = implode(', ', $values);

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$valueStr})";
        return $this->pdo->exec($sql);
    }

    /**
     * Inserts a record into a table if it doesn't already exist.
     *
     * @param string $table The table name.
     * @param array $data The data to insert.
     * @param string $uniqueKey The column to check for uniqueness.
     * @return int|false The number of affected rows, or false on failure.
     */
    public function insertIfNotExists($table, $data, $uniqueKey)
    {
        $checkSql = "SELECT COUNT(*) FROM {$table} WHERE {$uniqueKey} = " . $this->quote($data[$uniqueKey]);
        $stmt = $this->pdo->query($checkSql);
        $exists = $stmt->fetchColumn();

        if ($exists == 0) {
            return $this->insert($table, $data);
        } else {
            return false;
        }
    }

    /**
     * Retrieves the ID of the last inserted row.
     *
     * @return string The ID of the last inserted row.
     */
    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}
