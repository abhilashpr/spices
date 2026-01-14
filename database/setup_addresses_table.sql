-- Complete setup for addresses table
-- Run this file if the addresses table doesn't exist yet

-- Step 1: Create addresses table (if it doesn't exist)
CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255) DEFAULT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'India',
    post_code VARCHAR(20) NOT NULL,
    landmark VARCHAR(255) DEFAULT NULL,
    note TEXT DEFAULT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Add contact fields (if they don't exist)
-- For MySQL 5.7 and below, run these one by one if you get errors:
ALTER TABLE addresses ADD COLUMN contact_name VARCHAR(255) NULL AFTER user_id;
ALTER TABLE addresses ADD COLUMN contact_email VARCHAR(255) NULL AFTER contact_name;
ALTER TABLE addresses ADD COLUMN contact_phone VARCHAR(20) NULL AFTER contact_email;

-- For MySQL 8.0+, you can use IF NOT EXISTS (but it may not work in all versions):
-- ALTER TABLE addresses 
-- ADD COLUMN IF NOT EXISTS contact_name VARCHAR(255) NULL AFTER user_id,
-- ADD COLUMN IF NOT EXISTS contact_email VARCHAR(255) NULL AFTER contact_name,
-- ADD COLUMN IF NOT EXISTS contact_phone VARCHAR(20) NULL AFTER contact_email;

