-- Create order_details table
CREATE TABLE IF NOT EXISTS order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    sku_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit VARCHAR(50) DEFAULT NULL,
    price DECIMAL(10, 2) NOT NULL,
    is_offer TINYINT(1) NOT NULL DEFAULT 0,
    offer_price DECIMAL(10, 2) DEFAULT NULL,
    payable_amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id),
    INDEX idx_sku_id (sku_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (sku_id) REFERENCES product_skus(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

