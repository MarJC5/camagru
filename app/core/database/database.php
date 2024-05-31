<?php

namespace Camagru\core\database;

use Camagru\core\database\Connection;

class Database
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::connect();
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }

    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    // Method to safely escape and quote strings for SQL queries
    public function quote($value)
    {
        return $this->pdo->quote($value);
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

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

    public function recordMigration($migrationName, $batch)
    {
        $sql = "INSERT INTO migrations (migration, batch) VALUES (?, ?)";
        $this->execute($sql, [$migrationName, $batch]);
    }

    public function getLastBatchNumber()
    {
        $sql = "SELECT MAX(batch) as max_batch FROM migrations";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int) $result['max_batch'] : 0;
    }

    public function insert($table, $data)
    {
        $keys = array_keys($data);
        $values = array_map([$this->pdo, 'quote'], array_values($data));

        $columns = implode(', ', $keys);
        $valueStr = implode(', ', $values);

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$valueStr})";
        return $this->pdo->exec($sql);
    }

    public function insertIfNotExists($table, $data, $uniqueKey)
    {
        // Check if the entry already exists based on the unique key
        $checkSql = "SELECT COUNT(*) FROM {$table} WHERE {$uniqueKey} = " . $this->quote($data[$uniqueKey]);
        $stmt = $this->pdo->query($checkSql);
        $exists = $stmt->fetchColumn();

        if ($exists == 0) {  // If the entry does not exist, perform the insert
            $keys = array_keys($data);
            $values = array_map([$this->pdo, 'quote'], array_values($data));

            $columns = implode(', ', $keys);
            $valueStr = implode(', ', $values);

            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$valueStr})";
            return $this->pdo->exec($sql);
        } else {
            return false;
        }
    }
}
