<?php
/**
 * Script to add category_id and group_id columns to products table
 * Run this file in your browser: http://localhost/online-sp/database/add_category_fields.php
 */

require_once __DIR__ . '/../admin/config/database.php';

try {
    $pdo = get_db_connection();
    
    echo "<h2>Adding Category and Subcategory Fields to Products Table</h2>";
    echo "<pre>";
    
    // Check if category_id column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'category_id'");
    $categoryColumnExists = $stmt->rowCount() > 0;
    
    // Check if group_id column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'group_id'");
    $groupColumnExists = $stmt->rowCount() > 0;
    
    // Add category_id column if it doesn't exist
    if (!$categoryColumnExists) {
        echo "Adding category_id column...\n";
        $pdo->exec("ALTER TABLE products ADD COLUMN category_id INT UNSIGNED NULL AFTER id");
        echo "✓ category_id column added successfully\n\n";
    } else {
        echo "✓ category_id column already exists\n\n";
    }
    
    // Add group_id column if it doesn't exist
    if (!$groupColumnExists) {
        echo "Adding group_id column...\n";
        $pdo->exec("ALTER TABLE products ADD COLUMN group_id INT UNSIGNED NULL AFTER category_id");
        echo "✓ group_id column added successfully\n\n";
    } else {
        echo "✓ group_id column already exists\n\n";
    }
    
    // Check if foreign key constraints exist and add them if they don't
    echo "Checking foreign key constraints...\n";
    
    // Get existing foreign keys
    $stmt = $pdo->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'products' 
        AND CONSTRAINT_NAME LIKE 'fk_products_%'
    ");
    $existingConstraints = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Add foreign key for category_id
    if (!in_array('fk_products_category', $existingConstraints)) {
        try {
            $pdo->exec("
                ALTER TABLE products 
                ADD CONSTRAINT fk_products_category 
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
            ");
            echo "✓ Foreign key fk_products_category added successfully\n\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate foreign key') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Foreign key fk_products_category already exists (ignoring)\n\n";
            } else {
                throw $e;
            }
        }
    } else {
        echo "✓ Foreign key fk_products_category already exists\n\n";
    }
    
    // Add foreign key for group_id
    if (!in_array('fk_products_subcategory', $existingConstraints)) {
        try {
            $pdo->exec("
                ALTER TABLE products 
                ADD CONSTRAINT fk_products_subcategory 
                FOREIGN KEY (group_id) REFERENCES subcategories(id) ON DELETE SET NULL
            ");
            echo "✓ Foreign key fk_products_subcategory added successfully\n\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate foreign key') !== false || 
                strpos($e->getMessage(), 'already exists') !== false) {
                echo "⚠ Foreign key fk_products_subcategory already exists (ignoring)\n\n";
            } else {
                throw $e;
            }
        }
    } else {
        echo "✓ Foreign key fk_products_subcategory already exists\n\n";
    }
    
    // Verify the columns were added
    echo "\n=== Verification ===\n";
    $stmt = $pdo->query("DESCRIBE products");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        if (in_array($column['Field'], ['category_id', 'group_id'])) {
            echo "Column: {$column['Field']} | Type: {$column['Type']} | Null: {$column['Null']}\n";
        }
    }
    
    echo "\n✅ All operations completed successfully!\n";
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "<pre>";
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "</pre>";
} catch (Exception $e) {
    echo "<pre>";
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "</pre>";
}
