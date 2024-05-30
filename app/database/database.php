<?php

namespace Camagru\database;

use Camagru\database\Connection;

class Database {
    private $pdo;

    public function __construct() {
        $this->pdo = Connection::connect();
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            // Better error handling: log to a file or error logging service
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }

    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            // Better error handling: log to a file or error logging service
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }

    public function trackMigration() {
        // if not exists
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";
        $this->pdo->exec($sql);
    }

    public function recordMigration($migrationName, $batch) {
        $sql = "INSERT INTO migrations (migration, batch) VALUES (?, ?)";
        $this->execute($sql, [$migrationName, $batch]);
    }

    public function getLastBatchNumber() {
        $sql = "SELECT MAX(batch) as max_batch FROM migrations";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? (int) $result['max_batch'] : 0;
    }
}