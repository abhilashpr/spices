<?php
/**
 * Fix Products Table Structure
 * Run this script to update the products table to match the admin system
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

try {
    $pdo = get_db_connection();
    
    echo "Starting products table update...\n\n";
    
    // 1. Make price and offer_price nullable (prices are now in SKUs)
    echo "1. Making price and offer_price nullable...\n";
    try {
        $pdo->exec("ALTER TABLE products MODIFY COLUMN price DECIMAL(10,2) NULL DEFAULT NULL");
        echo "   ✓ price column updated\n";
    } catch (PDOException $e) {
        echo "   ⚠ price column: " . $e->getMessage() . "\n";
    }
    
    try {
        $pdo->exec("ALTER TABLE products MODIFY COLUMN offer_price DECIMAL(10,2) NULL DEFAULT NULL");
        echo "   ✓ offer_price column updated\n";
    } catch (PDOException $e) {
        echo "   ⚠ offer_price column: " . $e->getMessage() . "\n";
    }
    
    // 2. Add product_code column if it doesn't exist
    echo "\n2. Adding product_code column...\n";
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN product_code VARCHAR(100) NULL AFTER id");
        echo "   ✓ product_code column added\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "   ✓ product_code column already exists\n";
        } else {
            echo "   ⚠ product_code column: " . $e->getMessage() . "\n";
        }
    }
    
    // 3. Add main_image column if it doesn't exist
    echo "\n3. Adding main_image column...\n";
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN main_image VARCHAR(255) NULL AFTER description");
        echo "   ✓ main_image column added\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "   ✓ main_image column already exists\n";
        } else {
            echo "   ⚠ main_image column: " . $e->getMessage() . "\n";
        }
    }
    
    // 4. Add is_active column if it doesn't exist
    echo "\n4. Adding is_active column...\n";
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER main_image");
        echo "   ✓ is_active column added\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "   ✓ is_active column already exists\n";
        } else {
            echo "   ⚠ is_active column: " . $e->getMessage() . "\n";
        }
    }
    
    // 5. Add is_out_of_stock column if it doesn't exist
    echo "\n5. Adding is_out_of_stock column...\n";
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN is_out_of_stock TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active");
        echo "   ✓ is_out_of_stock column added\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "   ✓ is_out_of_stock column already exists\n";
        } else {
            echo "   ⚠ is_out_of_stock column: " . $e->getMessage() . "\n";
        }
    }
    
    // 6. Show final table structure
    echo "\n6. Final table structure:\n";
    $stmt = $pdo->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n";
    printf("%-20s %-20s %-10s %-10s %-10s %-10s\n", "Field", "Type", "Null", "Key", "Default", "Extra");
    echo str_repeat("-", 90) . "\n";
    foreach ($columns as $col) {
        printf("%-20s %-20s %-10s %-10s %-10s %-10s\n", 
            $col['Field'], 
            $col['Type'], 
            $col['Null'], 
            $col['Key'], 
            $col['Default'] ?? 'NULL',
            $col['Extra']
        );
    }
    
    echo "\n\n✅ Products table update completed successfully!\n";
    echo "You can now create products without price fields.\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

