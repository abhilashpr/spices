<?php
/**
 * Script to set up admin user
 * Run this file once to create the admin user
 */

require_once __DIR__ . '/../includes/db.php';

$pdo = get_db_connection();

// Create admins table if it doesn't exist
$pdo->exec("
    CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB
");

// Create categories table if it doesn't exist
$pdo->exec("
    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        slug VARCHAR(120) NOT NULL UNIQUE,
        description TEXT DEFAULT NULL,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB
");

// Check if admin already exists
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admins WHERE username = 'admin@gmail.com'");
$stmt->execute();
$exists = $stmt->fetch()['count'] > 0;

if ($exists) {
    echo "Admin user already exists.\n";
} else {
    // Create admin user
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->execute(['admin@gmail.com', $password]);
    echo "Admin user created successfully!\n";
    echo "Username: admin@gmail.com\n";
    echo "Password: admin123\n";
}

echo "\nSetup complete! You can now login at /online-sp/admin/login.php\n";
?>

