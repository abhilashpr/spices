-- Fix Products Table Structure (Manual Version - for MySQL versions that don't support IF NOT EXISTS)
-- Run these commands one by one in phpMyAdmin or MySQL terminal

USE saffron_spice;

-- 1. Make price and offer_price nullable (prices are now handled in product_skus table)
ALTER TABLE products MODIFY COLUMN price DECIMAL(10,2) NULL DEFAULT NULL;
ALTER TABLE products MODIFY COLUMN offer_price DECIMAL(10,2) NULL DEFAULT NULL;

-- 2. Check if product_code exists, then add if needed
-- Run: SHOW COLUMNS FROM products LIKE 'product_code';
-- If it doesn't exist, run:
ALTER TABLE products ADD COLUMN product_code VARCHAR(100) NULL AFTER id;

-- 3. Check if main_image exists, then add if needed
-- Run: SHOW COLUMNS FROM products LIKE 'main_image';
-- If it doesn't exist, run:
ALTER TABLE products ADD COLUMN main_image VARCHAR(255) NULL AFTER description;

-- 4. Check if is_active exists, then add if needed
-- Run: SHOW COLUMNS FROM products LIKE 'is_active';
-- If it doesn't exist, run:
ALTER TABLE products ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER main_image;

-- 5. Check if is_out_of_stock exists, then add if needed
-- Run: SHOW COLUMNS FROM products LIKE 'is_out_of_stock';
-- If it doesn't exist, run:
ALTER TABLE products ADD COLUMN is_out_of_stock TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active;

