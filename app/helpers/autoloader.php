<?php

namespace Camagru;

/**
 * Autoload classes based on their namespace and directory structure.
 */
spl_autoload_register(function ($class) {
    // Base directory for the namespace prefix
    $baseDir = __DIR__ . '/../';

    // Namespace prefix
    $prefix = 'Camagru\\';

    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }

    // Get the relative class name
    $relativeClass = substr($class, $len);

    // Replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // If the file exists, require it
    if (file_exists($file)) {
        require_once $file;
    }
});