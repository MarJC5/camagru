<?php

namespace Camagru\core\database\migrations;

use Camagru\core\database\Database;

class Runner {
    private $db;
    private $migrationsPath;

    public function __construct(Database $db, $migrationsPath = __DIR__) {
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
            $migration = include $filePath;
        
            if ($migration && method_exists($migration, 'up')) {
                $migration->up();
                $this->logMigration($migrationFile, $batch);
                echo "Migrated: " . $migrationFile . "\n";
            }
        }
    }

    public function rollback() {
        $executed = $this->getExecutedMigrations();
        $batch = $this->getCurrentBatch();

        foreach (array_reverse($executed) as $migrationFile) {
            $filePath = $this->migrationsPath . '/' . $migrationFile;
            $migration = include $filePath;
        
            if ($migration && method_exists($migration, 'down')) {
                $migration->down();
                $this->db->execute("DELETE FROM migrations WHERE migration = ?", [$migrationFile]);
                echo "Rolled back: " . $migrationFile . "\n";
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
}
