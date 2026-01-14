<?php
/**
 * Helper Functions for Frontend
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Get full URL for uploaded image
 */
function get_image_url(string $filepath): string
{
    if (empty($filepath)) {
        return '';
    }
    
    // If already a full URL, return as is
    if (preg_match('/^https?:\/\//', $filepath)) {
        return $filepath;
    }
    
    // Clean up the path
    $filepath = str_replace('\\', '/', $filepath);
    $filepath = ltrim($filepath, '/');
    
    // Remove absolute path if present
    if (preg_match('#/(?:Applications|var|home|usr)/.*?/uploads/(.+)#', $filepath, $matches)) {
        $filepath = 'uploads/' . $matches[1];
    }
    
    // Ensure path starts with "uploads/"
    if (strpos($filepath, 'uploads/') !== 0) {
        $filepath = 'uploads/' . basename($filepath);
    }
    
    return BASE_URL . '/' . $filepath;
}

/**
 * Generate URL for a page
 */
function url(string $path = ''): string
{
    $base = BASE_URL;
    if (empty($path)) {
        return $base;
    }
    
    // Remove .php extension if present
    $path = preg_replace('/\.php$/', '', $path);
    
    // Remove leading slash and add to base
    return $base . '/' . ltrim($path, '/');
}

/**
 * Escape HTML
 */
function e(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format price
 */
function format_price(float $amount, string $currency = '₹'): string
{
    return $currency . number_format($amount, 0);
}

/**
 * Check if user is logged in
 */
function is_logged_in(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}

/**
 * Get logged in user data
 */
function get_logged_in_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'name' => $_SESSION['user_name'] ?? null
    ];
}

/**
 * Get categories with subcategories for navbar
 */
function get_navbar_categories(): array
{
    try {
        // Ensure database config is loaded
        if (!function_exists('get_db_connection')) {
            require_once __DIR__ . '/../config/database.php';
        }
        
        require_once __DIR__ . '/../models/CategoryModel.php';
        $categoryModel = new CategoryModel();
        
        // Get all active categories with their active subcategories
        $categories = $categoryModel->getAll();
        $result = [];
        
        foreach ($categories as $category) {
            $subcategories = $categoryModel->getSubcategories($category['id']);
            if (!empty($subcategories)) {
                $category['subcategories'] = $subcategories;
                $result[] = $category;
            } else {
                // Include categories without subcategories too
                $category['subcategories'] = [];
                $result[] = $category;
            }
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Error getting navbar categories: " . $e->getMessage());
        return [];
    }
}

