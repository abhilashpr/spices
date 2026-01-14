<?php
/**
 * Application Bootstrap
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Load environment variables
require_once __DIR__ . '/config/env.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Autoload classes
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/core/',
        __DIR__ . '/controllers/',
        __DIR__ . '/models/',
    ];

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

