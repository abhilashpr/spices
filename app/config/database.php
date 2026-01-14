<?php
/**
 * Database Connection
 */

require_once __DIR__ . '/config.php';

function get_db_connection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $exception) {
            http_response_code(500);
            if (ini_get('display_errors')) {
                echo "<!DOCTYPE html><html><head><title>Database Error</title><style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
                echo ".error-box{background:white;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);max-width:800px;margin:0 auto;}";
                echo "h1{color:#dc3545;margin-top:0;}pre{background:#f8f9fa;padding:15px;border-radius:4px;overflow:auto;}</style></head><body>";
                echo "<div class='error-box'><h1>❌ Database Connection Failed</h1>";
                echo "<p><strong>Error:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
                echo "<p><strong>DSN:</strong> " . htmlspecialchars($dsn) . "</p>";
                echo "<p><strong>User:</strong> " . htmlspecialchars(DB_USER) . "</p>";
                echo "<h3>Check:</h3><ul>";
                echo "<li>Database server is running</li>";
                echo "<li>Database name is correct: " . htmlspecialchars(DB_NAME) . "</li>";
                echo "<li>Username and password are correct</li>";
                echo "<li>Database exists and is accessible</li>";
                echo "</ul></div></body></html>";
            } else {
                echo 'Database connection failed. Please try again later.';
            }
            error_log('DB connection error: ' . $exception->getMessage());
            exit;
        }
    }

    return $pdo;
}

