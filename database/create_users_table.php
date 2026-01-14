<?php
/**
 * Script to create users table
 * Run this file in browser: http://localhost/online-sp/database/create_users_table.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/config/database.php';

try {
    $pdo = get_db_connection();
    
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `firstname` VARCHAR(100) NOT NULL,
      `lastname` VARCHAR(100) NOT NULL,
      `email` VARCHAR(255) NOT NULL,
      `password` VARCHAR(255) DEFAULT NULL,
      `status` ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
      `login_type` ENUM('web', 'gmail') DEFAULT 'web',
      `gmail_id` VARCHAR(255) DEFAULT NULL,
      `otp` VARCHAR(6) DEFAULT NULL,
      `otp_expires_at` DATETIME DEFAULT NULL,
      `email_verified` TINYINT(1) DEFAULT 0,
      `auth_token` VARCHAR(255) DEFAULT NULL,
      `token_expires_at` DATETIME DEFAULT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`),
      UNIQUE KEY `gmail_id` (`gmail_id`),
      KEY `auth_token` (`auth_token`),
      KEY `email_verified` (`email_verified`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $pdo->exec($sql);
    
    echo "<h2>✅ Users table created successfully!</h2>";
    echo "<p>You can now close this page and try registering again.</p>";
    echo "<p><a href='" . BASE_URL . "/register'>Go to Register Page</a></p>";
    
    // Show table structure
    echo "<h3>Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Error creating users table:</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h3>Details:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
} catch (Exception $e) {
    echo "<h2>❌ Error:</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
