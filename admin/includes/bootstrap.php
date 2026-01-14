<?php
/**
 * Admin Bootstrap - Load all required files
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Load core files
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/View.php';

// Load helpers
require_once __DIR__ . '/helpers.php';



