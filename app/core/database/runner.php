<?php

namespace Camagru\core\database;

use Camagru\helpers\Config;

/**
 * Class Runner
 * Handles the execution and rollback of database migrations.
 */
class Runner
{
    /**
     * @var Database The database instance for executing queries.
     */
    private $db;

    /**
     * @var string The path to the migrations directory.
     */
    private $migrationsPath;

    /**
     * Runner constructor.
     * Initializes the database connection and sets the migrations path.
     *
     * @param Database $db The database instance.
     * @param string $migrationsPath The path to the migrations directory.
     */
    public function __construct(Database $db, $migrationsPath = __DIR__ . '/migrations')
    {
        $this->db = $db;
        $this->migrationsPath = $migrationsPath;
    }

    /**
     * Runs the migrations that have not been executed yet.
     */
    public function run()
    {
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
                echo "Migrated: " . $migrationFile . PHP_EOL;
            }
        }
    }

    /**
     * Rolls back the last batch of migrations.
     */
    public function rollback()
    {
        $executed = $this->getExecutedMigrations();
        $batch = $this->getCurrentBatch();

        foreach (array_reverse($executed) as $migrationFile) {
            $filePath = $this->migrationsPath . '/' . $migrationFile;
            $migration = include $filePath;

            if ($migration && method_exists($migration, 'down')) {
                $migration->down();
                $this->db->execute("DELETE FROM migrations WHERE migration = ?", [$migrationFile]);
                echo "Rolled back: " . $migrationFile . PHP_EOL;
            }
        }

        // Delete all uploaded images inside the uploads folder
        $dir = BASE_PATH . '/' . Config::get('media')['path'];
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            unlink($dir . '/' . $file);
        }
    }

    /**
     * Resets the database by rolling back all migrations and running them again.
     */
    public function reset()
    {
        $this->rollback();
        $this->run();
    }

    /**
     * Retrieves the list of migration files.
     *
     * @return array The list of migration files.
     */
    private function getMigrations()
    {
        $files = scandir($this->migrationsPath);
        return array_filter($files, function ($file) {
            return strpos($file, '.php') !== false;
        });
    }

    /**
     * Retrieves the list of executed migrations.
     *
     * @return array The list of executed migrations.
     */
    private function getExecutedMigrations()
    {
        $result = $this->db->query("SELECT migration FROM migrations");
        return array_column($result, 'migration');
    }

    /**
     * Retrieves the current batch number.
     *
     * @return int The current batch number.
     */
    private function getCurrentBatch()
    {
        $result = $this->db->query("SELECT MAX(batch) as batch FROM migrations");
        return $result[0]['batch'] ?? 0;
    }

    /**
     * Logs the migration to the migrations table.
     *
     * @param string $migrationName The name of the migration file.
     * @param int $batch The batch number.
     */
    private function logMigration($migrationName, $batch)
    {
        $this->db->execute("INSERT INTO migrations (migration, batch) VALUES (?, ?)", [$migrationName, $batch]);
    }

    /**
     * Creates the migrations table if it does not exist.
     */
    public function createMigrationsTable()
    {
        $this->db->execute("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL
        )");
    }

    /**
     * Checks if the database has been migrated.
     *
     * @return bool True if the database has been migrated, false otherwise.
     */
    public static function isMigrated()
    {
        $shouldBeMigrated = ['migrations', 'pages'];
        $db = new Database();
        
        // Check if the migrations table exists and has any records
        $tables = $db->query("SHOW TABLES");
        $tables = array_column($tables, 'Tables_in_camagru');
        $migrationsTableExists = in_array('migrations', $tables);
        $migrationsTableHasRecords = $migrationsTableExists && $db->query("SELECT COUNT(*) FROM migrations")[0]['COUNT(*)'] > 0;

        // Check if the pages table exists and has any records
        $pagesTableExists = in_array('pages', $tables);
        $pagesTableHasRecords = $pagesTableExists && $db->query("SELECT COUNT(*) FROM pages")[0]['COUNT(*)'] > 0;

        // Check if the tables that should be migrated exist
        $tablesExist = true;
        foreach ($shouldBeMigrated as $table) {
            if (!in_array($table, $tables)) {
                $tablesExist = false;
                break;
            }
        }

        return $migrationsTableHasRecords && $pagesTableHasRecords && $tablesExist;
    }
}
