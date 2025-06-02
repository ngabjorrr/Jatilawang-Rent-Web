CREATE DATABASE IF NOT EXISTS jatilawang_db;
USE jatilawang_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    category VARCHAR(50),
    image VARCHAR(255),
    description TEXT,
    price DECIMAL(10,2),
    rating DECIMAL(2,1),
    reviews INT,
    is_rentable BOOLEAN DEFAULT TRUE
);

ALTER TABLE users
ADD COLUMN username VARCHAR(100) NOT NULL UNIQUE AFTER name,
ADD COLUMN address TEXT NULL AFTER password,
ADD COLUMN phone VARCHAR(20) NULL AFTER address;

INSERT INTO users (email, password, name) VALUES 
('admin@jatilawang.com', 'admin123', 'Admin User');  -- Ganti dengan password hash asli nanti

INSERT INTO products (name, category, image, description, price, rating, reviews, is_rentable) VALUES
('Sepatu Jenis 1', 'Sepatu Gunung', 'public/assets/sepatu1.jpg', 'Deskripsi produk...', 0.00, 4.7, 123, TRUE),
('Tenda Jenis 1', 'Tenda', 'public/assets/tenda1.jpg', 'Deskripsi produk...', 0.00, 4.7, 188, TRUE),
('Tas Jenis 3', 'Tas', 'public/assets/tas3.jpg', 'Deskripsi produk...', 150.00, 4.6, 70, FALSE);
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantity INT DEFAULT 1,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    review_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    added_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS rental_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    rental_start_date DATETIME,
    rental_end_date DATETIME,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS rental_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    daily_price DECIMAL(10,2),
    weekly_price DECIMAL(10,2),
    monthly_price DECIMAL(10,2),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS product_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    view_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    added_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image_url VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    tag VARCHAR(50),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_attributes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    attribute_name VARCHAR(50),
    attribute_value VARCHAR(100),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    stock_quantity INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_discounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    discount_percentage DECIMAL(5,2),
    start_date DATETIME,
    end_date DATETIME,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    user_id INT,
    question TEXT,
    answer TEXT,
    question_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS product_comparisons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_ids TEXT,  -- Simpan ID produk yang dibandingkan sebagai string
    comparison_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS product_recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    recommendation_reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantity_sold INT DEFAULT 1,
    total_revenue DECIMAL(10,2),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    return_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    reason TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    added_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    subscription_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    feedback TEXT,
    feedback_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_warranty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    warranty_period INT,  -- Dalam bulan
    warranty_details TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_shipping (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    shipping_cost DECIMAL(10,2),
    estimated_delivery_time VARCHAR(50),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_bulk_discounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    min_quantity INT,
    discount_percentage DECIMAL(5,2),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_affiliates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    affiliate_id INT,
    commission_rate DECIMAL(5,2),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    views INT DEFAULT 0,
    clicks INT DEFAULT 0,
    conversions INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    action ENUM('create', 'update', 'delete'),
    action_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS product_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    log_message TEXT,
    log_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    promotion_type ENUM('discount', 'bundle', 'flash_sale'),
    promotion_details TEXT,
    start_date DATETIME,
    end_date DATETIME,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_licenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    license_key VARCHAR(100) UNIQUE,
    activation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    expiration_date DATETIME,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_certifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    certification_name VARCHAR(100),
    certification_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    suggestion TEXT,
    suggestion_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS product_maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    maintenance_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    maintenance_details TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_compatibility (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    compatible_with VARCHAR(100),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_customizations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    customization_details TEXT,
    customization_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_inventories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    inventory_location VARCHAR(100),
    stock_quantity INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    supplier_name VARCHAR(100),
    contact_info VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantity INT DEFAULT 1,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
-- Duplicate table definitions removed for product_subscriptions, product_warranties, product_shipping, product_bulk_discounts, product_affiliates, product_analytics, and product_audits.
CREATE TABLE IF NOT EXISTS product_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    log_message TEXT,
    log_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    promotion_type ENUM('discount', 'bundle', 'flash_sale'),
    promotion_details TEXT,
    start_date DATETIME,
    end_date DATETIME,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_licenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    license_key VARCHAR(100) UNIQUE,
    activation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    expiration_date DATETIME,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_certifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    certification_name VARCHAR(100),
    certification_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    suggestion TEXT,
    suggestion_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS product_maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    maintenance_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    maintenance_details TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_compatibility (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    compatible_with VARCHAR(100),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_customizations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    customization_details TEXT,
    customization_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_inventories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    inventory_location VARCHAR(100),
    stock_quantity INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    supplier_name VARCHAR(100),
    contact_info VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    quantity INT DEFAULT 1,
    total_price DECIMAL(10,2),
    status ENUM('pending', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    subscription_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_warranties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    warranty_period INT,  -- Dalam bulan
    warranty_details TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_shipping (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    shipping_cost DECIMAL(10,2),
    estimated_delivery_time VARCHAR(50),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_bulk_discounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    min_quantity INT,
    discount_percentage DECIMAL(5,2),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_affiliates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    affiliate_id INT,
    commission_rate DECIMAL(5,2),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    views INT DEFAULT 0,
    clicks INT DEFAULT 0,
    conversions INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
CREATE TABLE IF NOT EXISTS product_audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    action ENUM('create', 'update', 'delete'),
    action_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);