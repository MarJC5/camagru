<?php

namespace Camagru\core\database;

use Camagru\core\database\Database;
use Camagru\helpers\Logger;

class Runner {
    private $db;
    private $migrationsPath;

    public function __construct(Database $db, $migrationsPath = __DIR__ . '/migrations') {
        $this->db = $db;
        $this->migrationsPath = $migrationsPath;
    }

    public function run() {
        $migrations = $this->getMigrations();
        $executed = $this->getExecutedMigrations();
        $toRun = array_diff($migrations, $executed);
        $batch = $this->getCurrentBatch() + 1;

        foreach ($toRun as $migrationFile) {
            $filePath = $this->migrationsPath . '/' . $migrationFile;
            $migration = include_once $filePath;
        
            if ($migration && method_exists($migration, 'up')) {
                $migration->up();
                $this->logMigration($migrationFile, $batch);
                Logger::log("Migrated: " . $migrationFile);
            }
        }
    }

    public function rollback() {
        $executed = $this->getExecutedMigrations();
        $batch = $this->getCurrentBatch();

        foreach (array_reverse($executed) as $migrationFile) {
            $filePath = $this->migrationsPath . '/' . $migrationFile;
            $migration = include_once $filePath;
        
            if ($migration && method_exists($migration, 'down')) {
                $migration->down();
                $this->db->execute("DELETE FROM migrations WHERE migration = ?", [$migrationFile]);
                Logger::log("Rolled back: " . $migrationFile);
            }
        }
    }

    public function reset() {
        $this->rollback();
        $this->run();
    }

    private function getMigrations() {
        $files = scandir($this->migrationsPath);
        return array_filter($files, function ($file) {
            return strpos($file, '.php') !== false;
        });
    }

    private function getExecutedMigrations() {
        $result = $this->db->query("SELECT migration FROM migrations");
        return array_column($result, 'migration');
    }

    private function getCurrentBatch() {
        $result = $this->db->query("SELECT MAX(batch) as batch FROM migrations");
        return $result[0]['batch'] ?? 0;
    }

    private function logMigration($migrationName, $batch) {
        $this->db->execute("INSERT INTO migrations (migration, batch) VALUES (?, ?)", [$migrationName, $batch]);
    }

    public function createMigrationsTable() {
        $this->db->execute("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL
        )");
    }

    public static function isMigrated()
    {
        $shouldBeMigrated = ['migrations', 'pages'];
        $db = new Database();
        // Check if the migrations table exists and has any records
        $tables = $db->query("SHOW TABLES");
        $tables = array_column($tables, 'Tables_in_camagru');
        $migrationsTableExists = in_array('migrations', $tables);
        $migrationsTableHasRecords = $migrationsTableExists && $db->query("SELECT COUNT(*) FROM migrations")[0]['COUNT(*)'] > 0;

        // Check if the tables that should be migrated exist
        $tablesExist = true;
        foreach ($shouldBeMigrated as $table) {
            if (!in_array($table, $tables)) {
                $tablesExist = false;
                break;
            }
        }

        return $migrationsTableHasRecords && $tablesExist;
    }
}
