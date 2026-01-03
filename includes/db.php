<?php
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
            echo 'Database connection failed. Please try again later.';
            error_log('DB connection error: ' . $exception->getMessage());
            exit;
        }
    }

    return $pdo;
}

function fetch_all(string $query, array $params = []): array
{
    $stmt = get_db_connection()->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetch_one(string $query, array $params = []): ?array
{
    $stmt = get_db_connection()->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result === false ? null : $result;
}
