-- Create addresses table for user shipping addresses (Complete version with contact fields)
CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    contact_name VARCHAR(255) NULL,
    contact_email VARCHAR(255) NULL,
    contact_phone VARCHAR(20) NULL,
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

