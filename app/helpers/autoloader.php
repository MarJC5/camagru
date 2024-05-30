<?php

namespace Camagru;

/**
 * Autoload classes based on their namespace and directory structure.
 */
spl_autoload_register(function ($class) {
    $filename = explode('\\', $class);
    $namespace = array_shift($filename);

    // Start from the base path of the application
    $baseDir = __DIR__ . '/../';

    // Debugging: Output the expected path
    // echo "Loading class: $class\n";
    // echo "Expected path: " . $baseDir . implode(DIRECTORY_SEPARATOR, $filename) . '.php' . "\n";

    // Check if the namespace matches the directory structure
    array_unshift($filename, $baseDir);

    $filePath = implode(DIRECTORY_SEPARATOR, $filename) . '.php';
    // If the namespace matches the directory structure, concatenate the filename and include the file
    if ($namespace === __NAMESPACE__ && file_exists($filePath)) {
        include $filePath;
    } else {
        echo "Failed to load: $filePath\n";
    }
});