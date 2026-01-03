-- Update products table to match admin system structure
-- Products should NOT have price/offer_price (those are in SKUs)

USE saffron_spice;

-- Check if columns exist and add them if they don't
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS product_code VARCHAR(100) NULL AFTER id,
ADD COLUMN IF NOT EXISTS main_image VARCHAR(255) NULL AFTER description,
ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER main_image,
ADD COLUMN IF NOT EXISTS is_out_of_stock TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER is_out_of_stock,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- Make sure name column exists (it should)
-- Make sure slug column exists and is unique
ALTER TABLE products 
MODIFY COLUMN name VARCHAR(255) NOT NULL,
MODIFY COLUMN slug VARCHAR(255) NOT NULL UNIQUE;

-- Make description nullable if it's not already
ALTER TABLE products 
MODIFY COLUMN description TEXT NULL;

-- Note: We keep the old price/offer_price columns for backward compatibility
-- but they won't be used by the admin system (prices are in product_skus table)

