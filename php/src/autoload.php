<?php

declare(strict_types=1);

/**
 * Autoloader for Places namespace.
 */
spl_autoload_register(function (string $class): void {
    // Only handle Places namespace
    $prefix = 'Places\\';
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR;
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load functions file
require_once __DIR__ . DIRECTORY_SEPARATOR . 'functions.php';
