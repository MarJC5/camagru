#!/usr/bin/env php
<?php

// Register the Composer autoloader...
require_once __DIR__ . '/helpers/autoloader.php';

use Camagru\database\migrations\Runner;
use Camagru\database\Database;

$migrationsPath = __DIR__ . '/database/migrations';

// Instantiate the database connection
$db = new Database();

// Track migrations if not already done
$db->trackMigration();

// Retrieve command line arguments
$args = $_SERVER['argv'];
$command = $args[1] ?? null; // The command is expected as the second argument

// Create an instance of the migration runner
$migrations = new Runner($db, $migrationsPath);

// Process the command
switch ($command) {
    case 'run':
        $migrations->run();
        break;
    case 'rollback':
        $migrations->rollback();
        break;
    case 'reset':
        $migrations->reset();
        break;
    default:
        echo "Usage: migrate.php [up|down|reset]\n";
        break;
}