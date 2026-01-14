-- Create all order-related tables
-- Run this file to create orders, shipping_addresses, and order_details tables

-- Step 1: Create orders table with shipping address fields embedded
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    delivery_charge DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    tax DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    grand_total_payable DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    is_discount TINYINT(1) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0.00,
    coupon_code VARCHAR(50) DEFAULT NULL,
    -- Shipping Address Fields
    contact_name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(20) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255) DEFAULT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'India',
    state VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    pincode VARCHAR(20) NOT NULL,
    landmark VARCHAR(255) DEFAULT NULL,
    note TEXT DEFAULT NULL,
    -- Order Status
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_order_id (order_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Create shipping_addresses table (for reusable addresses)
-- Note: Shipping address is also embedded in orders table
CREATE TABLE IF NOT EXISTS shipping_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    contact_name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255) NOT NULL,
    contact_phone VARCHAR(20) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255) DEFAULT NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'India',
    state VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    pincode VARCHAR(20) NOT NULL,
    landmark VARCHAR(255) DEFAULT NULL,
    note TEXT DEFAULT NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 3: Create order_details table (depends on orders, products, and product_skus tables)
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

