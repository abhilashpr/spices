-- Fix Products Table Structure
-- Run this SQL in phpMyAdmin or MySQL terminal to fix the products table
-- This makes price/offer_price nullable and adds required columns for the admin system

USE saffron_spice;

-- 1. Make price and offer_price nullable (prices are now handled in product_skus table)
ALTER TABLE products MODIFY COLUMN price DECIMAL(10,2) NULL DEFAULT NULL;
ALTER TABLE products MODIFY COLUMN offer_price DECIMAL(10,2) NULL DEFAULT NULL;

-- 2. Make old required fields nullable or provide defaults
ALTER TABLE products MODIFY COLUMN image_class VARCHAR(80) NULL DEFAULT 'default';
ALTER TABLE products MODIFY COLUMN region VARCHAR(60) NULL DEFAULT 'general';
ALTER TABLE products MODIFY COLUMN craft VARCHAR(60) NULL DEFAULT 'blended';
ALTER TABLE products MODIFY COLUMN heat VARCHAR(30) NULL DEFAULT 'mild';
ALTER TABLE products MODIFY COLUMN summary TEXT NULL;

-- 3. Add product_code column if it doesn't exist
-- Check first: SHOW COLUMNS FROM products LIKE 'product_code';
ALTER TABLE products ADD COLUMN product_code VARCHAR(100) NULL AFTER id;

-- 4. Add main_image column if it doesn't exist
-- Check first: SHOW COLUMNS FROM products LIKE 'main_image';
ALTER TABLE products ADD COLUMN main_image VARCHAR(255) NULL AFTER description;

-- 5. Add is_active column if it doesn't exist
-- Check first: SHOW COLUMNS FROM products LIKE 'is_active';
ALTER TABLE products ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER main_image;

-- 6. Add is_out_of_stock column if it doesn't exist
-- Check first: SHOW COLUMNS FROM products LIKE 'is_out_of_stock';
ALTER TABLE products ADD COLUMN is_out_of_stock TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active;

-- Note: If you get "Duplicate column name" errors, those columns already exist - that's fine!

