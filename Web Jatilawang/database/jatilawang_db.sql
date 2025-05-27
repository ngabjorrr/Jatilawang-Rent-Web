
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

INSERT INTO users (email, password, name) VALUES 
('admin@jatilawang.com', '$2y$10$DUMMY_HASHED_PASSWORD', 'Admin User');  -- Ganti dengan password hash asli nanti

INSERT INTO products (name, category, image, description, price, rating, reviews, is_rentable) VALUES
('Sepatu Jenis 1', 'Sepatu Gunung', 'public/assets/sepatu1.jpg', 'Deskripsi produk...', 0.00, 4.7, 123, TRUE),
('Tenda Jenis 1', 'Tenda', 'public/assets/tenda1.jpg', 'Deskripsi produk...', 0.00, 4.7, 188, TRUE),
('Tas Jenis 3', 'Tas', 'public/assets/tas3.jpg', 'Deskripsi produk...', 150.00, 4.6, 70, FALSE);
