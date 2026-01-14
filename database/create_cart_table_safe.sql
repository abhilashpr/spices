-- Create cart table with product_id and sku_id (Safe version without foreign key constraint)
-- Use this if product_skus table doesn't exist yet

CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    sku_id INT NOT NULL,
    unit VARCHAR(50) DEFAULT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id),
    INDEX idx_sku_id (sku_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product_sku (user_id, product_id, sku_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- After product_skus table is created, add the foreign key constraint:
-- ALTER TABLE cart ADD FOREIGN KEY (sku_id) REFERENCES product_skus(id) ON DELETE CASCADE;

