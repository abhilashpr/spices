<?php
/**
 * Helper Functions
 */

function admin_url(string $path = ''): string
{
    $base = '/online-sp/admin';
    if (empty($path)) {
        return $base . '/index.php';
    }
    // If path starts with '?', it's a query string - append to index.php
    if (strpos($path, '?') === 0) {
        return $base . '/index.php' . $path;
    }
    // If path contains '?', it's likely index.php?page=... format
    if (strpos($path, '?') !== false) {
        return $base . '/' . ltrim($path, '/');
    }
    // Otherwise, it's a regular path
    return $base . '/' . ltrim($path, '/');
}

function admin_asset(string $path): string
{
    return '/online-sp/admin/assets/' . ltrim($path, '/');
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf_token(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function old(string $key, $default = ''): string
{
    return $_SESSION['old_input'][$key] ?? $default;
}

function flash(string $key = 'message'): ?string
{
    $value = $_SESSION['flash'][$key] ?? null;
    if ($value) {
        unset($_SESSION['flash'][$key]);
    }
    return $value;
}

function set_flash(string $key, string $value): void
{
    $_SESSION['flash'][$key] = $value;
}

function redirect(string $url): void
{
    header("Location: {$url}");
    exit;
}

/**
 * Get full URL for uploaded image
 * 
 * @param string $filepath Relative path from web root (e.g., "uploads/sliders/image.jpg")
 * @return string Full URL path
 */
function get_image_url(string $filepath): string
{
    if (empty($filepath)) {
        return '';
    }
    
    // Get base URL constant
    $baseUrl = defined('BASE_URL') ? BASE_URL : '/online-sp';
    
    // If already a full URL (starts with http:// or https://), return as is
    if (preg_match('/^https?:\/\//', $filepath)) {
        return $filepath;
    }
    
    // Clean up the path - normalize slashes
    $filepath = str_replace('\\', '/', $filepath);
    
    // Remove absolute path if present (e.g., /Applications/XAMPP/...)
    if (preg_match('#/(?:Applications|var|home|usr)/.*?/uploads/(.+)#', $filepath, $matches)) {
        $filepath = 'uploads/' . $matches[1];
    }
    
    // Remove leading slash if present
    $filepath = ltrim($filepath, '/');
    
    // If already starts with base URL, remove it
    if (strpos($filepath, $baseUrl) === 0) {
        $filepath = substr($filepath, strlen($baseUrl));
        $filepath = ltrim($filepath, '/');
    }
    
    // Ensure path starts with "uploads/" (relative path format)
    if (strpos($filepath, 'uploads/') !== 0) {
        // If it doesn't start with uploads/, it might be just a filename
        // Try to determine the correct path based on context
        // For now, assume it's in uploads/products if it's a product image
        if (strpos($filepath, 'product') !== false) {
            $filepath = 'uploads/products/' . basename($filepath);
        } else {
            $filepath = 'uploads/' . basename($filepath);
        }
    }
    
    // Prepend base URL and return
    return $baseUrl . '/' . $filepath;
}

function dd($data): void
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Handle file upload
 * 
 * @param array $file $_FILES array element
 * @param string $uploadDir Directory to upload to
 * @param array $allowedTypes Allowed MIME types
 * @param int $maxSize Maximum file size in bytes
 * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
 */
function handle_file_upload(array $file, string $uploadDir, array $allowedTypes = [], int $maxSize = 0, string $prefix = ''): array
{
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'path' => null, 'error' => 'No file was uploaded.'];
    }

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive.',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
        ];
        return ['success' => false, 'path' => null, 'error' => $errorMessages[$file['error']] ?? 'Unknown upload error.'];
    }

    // Check file size
    if ($maxSize > 0 && $file['size'] > $maxSize) {
        return ['success' => false, 'path' => null, 'error' => 'File size exceeds maximum allowed size.'];
    }

    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'path' => null, 'error' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes)];
    }

    // Normalize the upload directory path
    $uploadDir = rtrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $uploadDir), DIRECTORY_SEPARATOR);
    
    // Try to resolve the real path
    $resolvedPath = realpath($uploadDir);
    if ($resolvedPath !== false) {
        $uploadDir = $resolvedPath;
    } else {
        // If realpath fails, try resolving parent directory
        $parentDir = realpath(dirname($uploadDir));
        if ($parentDir !== false) {
            $uploadDir = $parentDir . DIRECTORY_SEPARATOR . basename($uploadDir);
        }
    }

    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        if (!@mkdir($uploadDir, 0755, true)) {
            $lastError = error_get_last();
            $errorMsg = 'Failed to create upload directory: ' . $uploadDir;
            if ($lastError) {
                $errorMsg .= ' (' . $lastError['message'] . ')';
            }
            return ['success' => false, 'path' => null, 'error' => $errorMsg];
        }
        // Make sure directory is writable
        @chmod($uploadDir, 0755);
    }

    // Verify directory is writable
    if (!is_writable($uploadDir)) {
        return ['success' => false, 'path' => null, 'error' => 'Upload directory is not writable: ' . $uploadDir];
    }

    // Generate unique filename
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    // Sanitize extension to prevent issues
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($extension, $allowedExtensions)) {
        return ['success' => false, 'path' => null, 'error' => 'Invalid file extension.'];
    }
    $prefix = !empty($prefix) ? $prefix : 'file_';
    $filename = uniqid($prefix, true) . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . DIRECTORY_SEPARATOR . $filename;

    // Move uploaded file
    if (!@move_uploaded_file($file['tmp_name'], $filepath)) {
        $lastError = error_get_last();
        $errorMsg = 'Failed to move uploaded file.';
        if ($lastError) {
            $errorMsg .= ' Error: ' . $lastError['message'];
        }
        // Additional checks
        if (!is_uploaded_file($file['tmp_name'])) {
            $errorMsg .= ' File was not uploaded properly.';
        }
        if (!file_exists($file['tmp_name'])) {
            $errorMsg .= ' Temporary file does not exist.';
        }
        if (!is_writable($uploadDir)) {
            $errorMsg .= ' Upload directory is not writable.';
        }
        return ['success' => false, 'path' => null, 'error' => $errorMsg];
    }

    // Set file permissions
    @chmod($filepath, 0644);

    // Return relative path from web root (e.g., "uploads/products/filename.jpg")
    // Extract only the relative path starting from "uploads/"
    $normalizedPath = str_replace('\\', '/', $filepath);
    
    // Find the "uploads" directory in the path
    if (preg_match('#(uploads/[^/]+/.+)$#', $normalizedPath, $matches)) {
        $relativePath = $matches[1];
    } else {
        // Fallback: try to get relative path from web root
        $webRoot = dirname(ADMIN_ROOT);
        $webRootNormalized = str_replace('\\', '/', $webRoot);
        $relativePath = str_replace($webRootNormalized . '/', '', $normalizedPath);
    }
    
    // Ensure no leading slash
    $relativePath = ltrim($relativePath, '/');
    
    // Verify it starts with "uploads/"
    if (strpos($relativePath, 'uploads/') !== 0) {
        // If not, try to extract just the filename and reconstruct
        $filename = basename($filepath);
        // Determine upload type from directory
        if (strpos($normalizedPath, '/products/') !== false) {
            $relativePath = 'uploads/products/' . $filename;
        } elseif (strpos($normalizedPath, '/sliders/') !== false) {
            $relativePath = 'uploads/sliders/' . $filename;
        } else {
            $relativePath = 'uploads/' . $filename;
        }
    }
    
    return ['success' => true, 'path' => $relativePath, 'error' => null];
}

/**
 * Delete uploaded file
 * 
 * @param string $filepath Relative path from web root
 * @return bool
 */
/**
 * Delete uploaded file
 * 
 * @param string $filepath Relative path from web root
 * @return bool
 */
function delete_uploaded_file(string $filepath): bool
{
    if (empty($filepath)) {
        return false;
    }
    
    $fullPath = ADMIN_ROOT . '/../' . ltrim($filepath, '/');
    
    if (file_exists($fullPath) && is_file($fullPath)) {
        return @unlink($fullPath);
    }
    
    return false;
}

/**
 * Generate Bootstrap pagination HTML
 * 
 * @param array $pagination Pagination data from findPaginated
 * @param string $pageParam URL parameter name for page (default: 'p')
 * @param array $additionalParams Additional query parameters to preserve
 * @return string HTML for pagination
 */
function render_pagination(array $pagination, string $pageParam = 'p', array $additionalParams = []): string
{
    if ($pagination['pages'] <= 1) {
        return '';
    }

    $currentPage = $pagination['current_page'];
    $totalPages = $pagination['pages'];
    
    // Get current URL without query string
    $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
    
    // Parse existing query parameters
    $queryParams = [];
    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
        parse_str($_SERVER['QUERY_STRING'], $queryParams);
    }
    
    // Remove existing page parameter
    unset($queryParams[$pageParam]);
    
    // Add additional parameters
    foreach ($additionalParams as $key => $value) {
        if (!empty($value)) {
            $queryParams[$key] = $value;
        }
    }
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Build URL helper
    $buildUrl = function($page) use ($baseUrl, $queryParams, $pageParam) {
        $params = array_merge($queryParams, [$pageParam => $page]);
        $queryString = http_build_query($params);
        return $baseUrl . ($queryString ? '?' . $queryString : '?' . $pageParam . '=' . $page);
    };
    
    // Previous button
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($buildUrl($currentPage - 1)) . '">Previous</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
    }
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($buildUrl(1)) . '">1</a></li>';
        if ($startPage > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($buildUrl($i)) . '">' . $i . '</a></li>';
        }
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($buildUrl($totalPages)) . '">' . $totalPages . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars($buildUrl($currentPage + 1)) . '">Next</a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

