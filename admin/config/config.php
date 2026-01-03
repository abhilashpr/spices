<?php
/**
 * Admin Configuration
 */

// Base paths
define('ADMIN_ROOT', __DIR__ . '/..');
define('ADMIN_CONFIG', __DIR__);
define('ADMIN_CONTROLLERS', ADMIN_ROOT . '/controllers');
define('ADMIN_MODELS', ADMIN_ROOT . '/models');
define('ADMIN_VIEWS', ADMIN_ROOT . '/views');
define('ADMIN_INCLUDES', ADMIN_ROOT . '/includes');
define('ADMIN_ASSETS', ADMIN_ROOT . '/assets');

// Base URL
define('BASE_URL', 'http://localhost/online-sp');

// Admin settings
define('ADMIN_SESSION_NAME', 'admin_session');
define('ADMIN_LOGIN_REDIRECT', BASE_URL . '/admin/index.php');
define('ADMIN_LOGOUT_REDIRECT', BASE_URL . '/admin/login.php');

// Upload settings
define('UPLOAD_DIR', ADMIN_ROOT . '/../uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');
define('SLIDER_UPLOAD_DIR', UPLOAD_DIR . '/sliders');
define('SLIDER_UPLOAD_URL', BASE_URL . '/uploads/sliders');
define('PRODUCT_UPLOAD_DIR', UPLOAD_DIR . '/products');
define('PRODUCT_UPLOAD_URL', BASE_URL . '/uploads/products');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']);

// Timezone
date_default_timezone_set('UTC');

