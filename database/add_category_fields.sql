USE saffron_spice;

-- Add category_id column (if it doesn't exist)
-- Note: Run this manually - IF NOT EXISTS is not supported for ALTER TABLE ADD COLUMN in MySQL
ALTER TABLE products ADD COLUMN category_id INT UNSIGNED NULL AFTER id;

-- Add group_id column (for subcategory) (if it doesn't exist)
ALTER TABLE products ADD COLUMN group_id INT UNSIGNED NULL AFTER category_id;

-- Add foreign key constraints (if they don't exist)
-- Note: These may fail if constraints already exist, which is fine - ignore the error

-- Foreign key for category_id
ALTER TABLE products 
ADD CONSTRAINT fk_products_category 
FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

-- Foreign key for group_id (subcategory)
ALTER TABLE products 
ADD CONSTRAINT fk_products_subcategory 
FOREIGN KEY (group_id) REFERENCES subcategories(id) ON DELETE SET NULL;
