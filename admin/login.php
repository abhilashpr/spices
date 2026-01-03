<?php
/**
 * Admin Login Entry Point
 */

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/controllers/AuthController.php';

// If already logged in, redirect to dashboard
if (Auth::isLoggedIn()) {
    header('Location: ' . ADMIN_LOGIN_REDIRECT);
    exit;
}

$controller = new AuthController();
$controller->login();
