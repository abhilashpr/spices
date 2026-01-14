<?php
/**
 * Frontend Router
 * This file handles routing for the frontend application
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Load configuration and bootstrap
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/helpers/helpers.php';

// Get the route from URL path
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Remove query string from path but keep it for later use
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'] ?? '/';

// Remove base path if exists (e.g., /online-sp)
$basePath = dirname($scriptName);
if ($basePath !== '/' && $basePath !== '\\' && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Handle .php files in URL (backward compatibility - e.g., categories.php -> categories)
if (preg_match('#/(\w+)\.php$#', $path, $matches)) {
    $path = '/' . $matches[1];
} elseif (preg_match('#^(\w+)\.php$#', $path, $matches)) {
    $path = '/' . $matches[1];
}

// Remove leading/trailing slashes and get route segments
$path = trim($path, '/');
$segments = $path ? explode('/', $path) : [];

// Route mapping
$routes = [
    '' => ['HomeController', 'index'],
    'index' => ['HomeController', 'index'],
    'index.php' => ['HomeController', 'index'],
    'home' => ['HomeController', 'index'],
    'product' => ['ProductController', 'detail'],
    'categories' => ['CategoryController', 'index'],
    'cart' => ['CartController', 'index'],
    'cart/add' => ['CartController', 'add'],
    'checkout' => ['CheckoutController', 'index'],
    'checkout/guest-checkout' => ['CheckoutController', 'guestCheckout'],
    'checkout/verify-guest-otp' => ['CheckoutController', 'verifyGuestOTP'],
    'checkout/add-address' => ['CheckoutController', 'addAddress'],
    'checkout/update-address' => ['CheckoutController', 'updateAddress'],
    'checkout/delete-address' => ['CheckoutController', 'deleteAddress'],
    'checkout/set-default-address' => ['CheckoutController', 'setDefaultAddress'],
    'checkout/save-guest-address' => ['CheckoutController', 'saveGuestAddress'],
    'login' => ['AuthController', 'login'],
    'register' => ['AuthController', 'register'],
    'verify-otp' => ['AuthController', 'verifyOTP'],
    'resend-otp' => ['AuthController', 'resendOTP'],
    'logout' => ['AuthController', 'logout'],
    'profile' => ['ProfileController', 'index'],
    'wishlist' => ['WishlistController', 'index'],
];

// Determine controller and action
$controllerName = 'HomeController';
$actionName = 'index';

// First, try to match the full path (for multi-segment routes like "checkout/add-address")
$fullPath = $path;
if (isset($routes[$fullPath])) {
    $controllerName = $routes[$fullPath][0];
    $actionName = $routes[$fullPath][1];
} else {
    // Get the first segment as route key (for single-segment routes)
    $routeKey = $segments[0] ?? '';

    // Check if route exists
    if (isset($routes[$routeKey])) {
        $controllerName = $routes[$routeKey][0];
        $actionName = $routes[$routeKey][1];
    } elseif (!empty($routeKey) && strpos($routeKey, '.php') === false && strpos($routeKey, '.') === false) {
        // Try direct controller mapping (skip files with extensions)
        $controllerName = ucfirst($routeKey) . 'Controller';
        $actionName = $segments[1] ?? 'index';
    }
}

// Handle query parameters for actions (e.g., ?action=edit)
if (isset($_GET['action'])) {
    $actionName = $_GET['action'];
}

try {
    // Load controller
    $controllerFile = APP_CONTROLLERS . '/' . $controllerName . '.php';
    
    // Debug: Show actual path being checked
    if (!file_exists($controllerFile)) {
        throw new Exception("Controller not found: {$controllerName} at path: {$controllerFile}");
    }

    require_once $controllerFile;

    if (!class_exists($controllerName)) {
        throw new Exception("Controller class not found: {$controllerName}");
    }

    $controller = new $controllerName();

    if (!method_exists($controller, $actionName)) {
        throw new Exception("Action not found: {$actionName} in {$controllerName}");
    }

    // Call the action
    $controller->$actionName();

} catch (Exception $e) {
    http_response_code(500);
    echo "<!DOCTYPE html><html><head><title>Error</title><style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
    echo ".error-box{background:white;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);max-width:800px;margin:0 auto;}";
    echo "h1{color:#dc3545;margin-top:0;}pre{background:#f8f9fa;padding:15px;border-radius:4px;overflow:auto;font-size:12px;}";
    echo "strong{color:#333;}ul{line-height:1.8;}</style></head><body>";
    echo "<div class='error-box'>";
    echo "<h1>❌ Error: " . htmlspecialchars($e->getMessage()) . "</h1>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div></body></html>";
    error_log("Router Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    error_log("Stack trace: " . $e->getTraceAsString());
} catch (Error $e) {
    http_response_code(500);
    echo "<!DOCTYPE html><html><head><title>Fatal Error</title><style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
    echo ".error-box{background:white;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);max-width:800px;margin:0 auto;}";
    echo "h1{color:#dc3545;margin-top:0;}pre{background:#f8f9fa;padding:15px;border-radius:4px;overflow:auto;font-size:12px;}";
    echo "strong{color:#333;}ul{line-height:1.8;}</style></head><body>";
    echo "<div class='error-box'>";
    echo "<h1>❌ Fatal Error: " . htmlspecialchars($e->getMessage()) . "</h1>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div></body></html>";
    error_log("Router Fatal Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    error_log("Stack trace: " . $e->getTraceAsString());
}
