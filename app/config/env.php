<?php
/**
 * Environment Configuration Loader
 * Loads variables from .env file
 */

function loadEnv(string $filePath): void
{
    if (!file_exists($filePath)) {
        return;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }
            
            if (!empty($key)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

// Load .env file
$envFile = dirname(dirname(__DIR__)) . '/.env';
if (file_exists($envFile)) {
    loadEnv($envFile);
}

// Helper function to get env variable
function env(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

