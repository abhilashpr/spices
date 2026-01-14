<?php
/**
 * Frontend Configuration
 */

// Base paths
define('APP_ROOT', dirname(dirname(__DIR__)));
define('APP_CONFIG', __DIR__);
define('APP_CONTROLLERS', dirname(__DIR__) . '/controllers');
define('APP_MODELS', dirname(__DIR__) . '/models');
define('APP_VIEWS', APP_ROOT . '/views');
define('APP_CORE', dirname(__DIR__) . '/core');

// Base URL - Update this for production
define('BASE_URL', 'http://localhost/online-sp');

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'saffron_spice');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Upload settings
define('UPLOAD_DIR', APP_ROOT . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');
define('PRODUCT_UPLOAD_URL', BASE_URL . '/uploads/products');
define('SLIDER_UPLOAD_URL', BASE_URL . '/uploads/sliders');

// Session settings
define('SESSION_NAME', 'spice_session');

// Timezone
date_default_timezone_set('UTC');

