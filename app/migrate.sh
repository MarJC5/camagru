#!/usr/bin/env php
<?php

// Register the Composer autoloader...
require __DIR__ . '/helpers/autoloader.php';

use Camagru\database\migrations\Runner;
use Camagru\database\Database;

$migrationsPath = __DIR__ . '/database/migrations';

// Track migrations if not already done
$db = new Database();
$db->trackMigration();

$runner = new Runner($db, $migrationsPath);
$runner->run();