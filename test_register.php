<?php
/**
 * Test Register Page
 * This will show any errors
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/bootstrap.php';

try {
    echo "Testing register route...<br>";
    
    // Check if router exists
    if (file_exists(__DIR__ . '/router.php')) {
        echo "Router exists<br>";
        require __DIR__ . '/router.php';
    } else {
        echo "ERROR: router.php not found<br>";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString();
} catch (Error $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString();
}

