<?php
/**
 * Frontend Entry Point - Single Entry Point
 * All requests are routed through this file
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/router.php';
