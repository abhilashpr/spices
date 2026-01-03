<?php
require_once __DIR__ . '/includes/db.php';

$sql = file_get_contents(__DIR__ . '/database.sql');
if ($sql === false) {
    http_response_code(500);
    echo 'Unable to read database schema file.';
    exit;
}

try {
    $pdo = get_db_connection();
    $pdo->exec($sql);
    echo 'Database installed successfully.';
} catch (PDOException $exception) {
    http_response_code(500);
    echo 'Database installation failed. Check logs for details.';
    error_log('Database install error: ' . $exception->getMessage());
}
