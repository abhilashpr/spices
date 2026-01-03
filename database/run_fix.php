<?php
/**
 * Run Database Fix Script
 * This uses the same database connection as your application
 * Access via: http://localhost/online-sp/database/run_fix.php
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = get_db_connection();
    
    echo "=== Fixing Products Table Structure ===\n\n";
    
    // 1. Make price and offer_price nullable
    echo "1. Making price and offer_price nullable...\n";
    try {
        $pdo->exec("ALTER TABLE products MODIFY COLUMN price DECIMAL(10,2) NULL DEFAULT NULL");
        echo "   ✓ price column updated\n";
    } catch (PDOException $e) {
        echo "   ⚠ price: " . $e->getMessage() . "\n";
    }
    
    try {
        $pdo->exec("ALTER TABLE products MODIFY COLUMN offer_price DECIMAL(10,2) NULL DEFAULT NULL");
        echo "   ✓ offer_price column updated\n";
    } catch (PDOException $e) {
        echo "   ⚠ offer_price: " . $e->getMessage() . "\n";
    }
    
    // 2. Make old required fields nullable or provide defaults
    echo "\n2. Making old required fields nullable...\n";
    $fields = [
        'image_class' => "VARCHAR(80) NULL DEFAULT 'default'",
        'region' => "VARCHAR(60) NULL DEFAULT 'general'",
        'craft' => "VARCHAR(60) NULL DEFAULT 'blended'",
        'heat' => "VARCHAR(30) NULL DEFAULT 'mild'",
        'summary' => "TEXT NULL"
    ];
    
    foreach ($fields as $field => $definition) {
        try {
            $pdo->exec("ALTER TABLE products MODIFY COLUMN {$field} {$definition}");
            echo "   ✓ {$field} column updated\n";
        } catch (PDOException $e) {
            echo "   ⚠ {$field}: " . $e->getMessage() . "\n";
        }
    }
    
    // 3. Add new columns
    echo "\n3. Adding new admin system columns...\n";
    $newColumns = [
        'product_code' => "VARCHAR(100) NULL AFTER id",
        'main_image' => "VARCHAR(255) NULL",
        'is_active' => "TINYINT(1) NOT NULL DEFAULT 1",
        'is_out_of_stock' => "TINYINT(1) NOT NULL DEFAULT 0"
    ];
    
    foreach ($newColumns as $column => $definition) {
        // Check if column exists first
        $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE '{$column}'");
        if ($stmt->rowCount() == 0) {
            try {
                $pdo->exec("ALTER TABLE products ADD COLUMN {$column} {$definition}");
                echo "   ✓ {$column} column added\n";
            } catch (PDOException $e) {
                echo "   ⚠ {$column}: " . $e->getMessage() . "\n";
            }
        } else {
            echo "   ✓ {$column} column already exists\n";
        }
    }
    
    // 4. Show final table structure
    echo "\n4. Final table structure:\n";
    $stmt = $pdo->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n";
    printf("%-25s %-20s %-8s %-8s %-15s\n", "Field", "Type", "Null", "Key", "Default");
    echo str_repeat("-", 80) . "\n";
    foreach ($columns as $col) {
        printf("%-25s %-20s %-8s %-8s %-15s\n", 
            $col['Field'], 
            substr($col['Type'], 0, 18), 
            $col['Null'], 
            $col['Key'], 
            $col['Default'] ?? 'NULL'
        );
    }
    
    echo "\n\n✅ Products table update completed successfully!\n";
    echo "You can now create products without errors.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

