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
('Sepatu Gunung Alpha', 'Sepatu Gunung', 'https://via.placeholder.com/300x200.png?text=SepatuAlpha', 'Sepatu gunung yang tangguh untuk medan berat, tahan air dan nyaman.', 1250000.00, 4.8, 150, TRUE),
('Tenda Dome Pro 4P', 'Tenda', 'https://via.placeholder.com/300x200.png?text=TendaPro4P', 'Tenda dome kapasitas 4 orang, double layer, cocok untuk camping keluarga.', 850000.00, 4.7, 210, TRUE),
('Tas Carrier Explorer 60L', 'Tas', 'https://via.placeholder.com/300x200.png?text=TasExplorer60L', 'Tas carrier 60 liter dengan banyak kompartemen, ideal untuk pendakian panjang.', 950000.00, 4.6, 180, FALSE),
('Sleeping Bag ComfortMax', 'Perlengkapan Tidur', 'https://via.placeholder.com/300x200.png?text=SleepingBagComfort', 'Sleeping bag hangat dengan suhu nyaman hingga 5°C.', 350000.00, 4.5, 120, TRUE),
('Kompor Portable WindShield', 'Peralatan Masak', 'https://via.placeholder.com/300x200.png?text=KomporWindshield', 'Kompor portable mini dengan pelindung angin, efisien dan ringan.', 180000.00, 4.7, 95, FALSE),
('Matras Angin UltraLight', 'Perlengkapan Tidur', 'https://via.placeholder.com/300x200.png?text=MatrasAngin', 'Matras angin sangat ringan dan mudah dibawa, tebal dan nyaman.', 280000.00, 4.4, 75, TRUE),
('Headlamp BrightX 500', 'Pencahayaan', 'https://via.placeholder.com/300x200.png?text=HeadlampBrightX', 'Headlamp dengan 500 lumens, berbagai mode cahaya, tahan air.', 220000.00, 4.8, 110, FALSE),
('Jaket Gunung SummitPeak', 'Pakaian', 'https://via.placeholder.com/300x200.png?text=JaketSummit', 'Jaket gunung waterproof dan windproof, lapisan dalam polar hangat.', 750000.00, 4.6, 90, FALSE),
('Trekking Pole CarbonLite', 'Aksesoris Pendakian', 'https://via.placeholder.com/300x200.png?text=TrekkingPole', 'Sepasang trekking pole dari karbon, sangat ringan dan kuat.', 450000.00, 4.5, 65, TRUE),
('Nesting Set Adventure Cook', 'Peralatan Masak', 'https://via.placeholder.com/300x200.png?text=NestingSet', 'Set alat masak nesting stainless steel untuk 2-3 orang.', 320000.00, 4.7, 88, TRUE),
('Sepatu Trail Runner SpeedX', 'Sepatu Lari Trail', 'https://via.placeholder.com/300x200.png?text=SepatuTrailSpeedX', 'Sepatu lari trail dengan grip maksimal dan bantalan responsif.', 1100000.00, 4.7, 130, FALSE),
('Tenda Flysheet Hexa Shield', 'Tenda', 'https://via.placeholder.com/300x200.png?text=FlysheetHexa', 'Flysheet hexagonal ukuran 3x4m, anti UV dan tahan air.', 250000.00, 4.6, 105, TRUE),
('Tas Daypack Urban Commute 25L', 'Tas', 'https://via.placeholder.com/300x200.png?text=TasDaypackUrban', 'Tas daypack 25 liter cocok untuk harian atau perjalanan singkat, banyak kantong.', 480000.00, 4.5, 92, FALSE),
('Bantal Tiup ErgoRest', 'Perlengkapan Tidur', 'https://via.placeholder.com/300x200.png?text=BantalTiup', 'Bantal tiup ergonomis, kecil saat dikemas, nyaman digunakan.', 95000.00, 4.3, 60, TRUE),
('Pisau Lipat MultiSurvival', 'Peralatan Survival', 'https://via.placeholder.com/300x200.png?text=PisauSurvival', 'Pisau lipat multifungsi dengan berbagai alat survival terintegrasi.', 150000.00, 4.6, 78, FALSE),
('Sarung Tangan PolarWarm', 'Pakaian', 'https://via.placeholder.com/300x200.png?text=SarungTanganPolar', 'Sarung tangan polar tebal, menjaga tangan tetap hangat di cuaca dingin.', 120000.00, 4.4, 55, FALSE),
('Botol Minum ThermoSteel 1L', 'Aksesoris Pendakian', 'https://via.placeholder.com/300x200.png?text=BotolThermo', 'Botol minum stainless steel 1 liter, tahan panas dan dingin hingga 12 jam.', 280000.00, 4.7, 99, FALSE),
('Hammock SingleNest Adventure', 'Perlengkapan Tidur', 'https://via.placeholder.com/300x200.png?text=HammockSingle', 'Hammock single ringan dan kuat, lengkap dengan tali webbing.', 220000.00, 4.8, 115, TRUE),
('Gaiter Anti Pacet ProShield', 'Aksesoris Pendakian', 'https://via.placeholder.com/300x200.png?text=GaiterPacet', 'Gaiter pelindung dari pacet dan lumpur, bahan kuat dan tahan air.', 130000.00, 4.5, 70, FALSE),
('Kursi Lipat Portable CampRest', 'Perlengkapan Camping', 'https://via.placeholder.com/300x200.png?text=KursiLipat', 'Kursi lipat portable, ringan dan mudah dibawa, nyaman untuk bersantai.', 190000.00, 4.6, 85, TRUE),
('Sepatu Gunung Wanita TerraFit', 'Sepatu Gunung', 'https://via.placeholder.com/300x200.png?text=SepatuTerraFit', 'Sepatu gunung wanita dengan desain ergonomis, ringan dan stabil.', 1150000.00, 4.7, 140, TRUE),
('Tenda Ultralight SoloHiker 1P', 'Tenda', 'https://via.placeholder.com/300x200.png?text=TendaSoloHiker', 'Tenda ultralight 1 orang, cocok untuk solo hiking, berat kurang dari 1kg.', 950000.00, 4.8, 95, TRUE),
('Tas Pinggang Trail Organizer', 'Tas', 'https://via.placeholder.com/300x200.png?text=TasPinggangTrail', 'Tas pinggang untuk lari trail atau hiking singkat, banyak kompartemen kecil.', 250000.00, 4.5, 75, FALSE),
('Selimut Darurat EmergencyBlanket', 'Peralatan Survival', 'https://via.placeholder.com/300x200.png?text=SelimutDarurat', 'Selimut darurat tipis dan ringan, menahan panas tubuh.', 50000.00, 4.3, 150, FALSE),
('Water Filter LifeStraw Go', 'Peralatan Masak', 'https://via.placeholder.com/300x200.png?text=WaterFilterGo', 'Botol minum dengan filter air terintegrasi, aman minum dari sumber air alami.', 450000.00, 4.9, 180, FALSE),
('Celana QuickDry Expedition', 'Pakaian', 'https://via.placeholder.com/300x200.png?text=CelanaQuickDry', 'Celana panjang quick dry, ringan dan nyaman, bisa jadi celana pendek.', 380000.00, 4.6, 110, FALSE),
('Dry Bag Waterproof 20L', 'Aksesoris Pendakian', 'https://via.placeholder.com/300x200.png?text=DryBag20L', 'Dry bag 20 liter, menjaga barang tetap kering saat hujan atau aktivitas air.', 180000.00, 4.7, 90, TRUE),
('Lampu Tenda Solar Lantern', 'Pencahayaan', 'https://via.placeholder.com/300x200.png?text=SolarLantern', 'Lampu tenda LED dengan panel surya, bisa juga diisi via USB.', 230000.00, 4.5, 80, TRUE),
('Ponco Multifungsi RainGuard', 'Pakaian', 'https://via.placeholder.com/300x200.png?text=PoncoRainGuard', 'Ponco hujan multifungsi, bisa juga dijadikan shelter darurat atau alas.', 150000.00, 4.4, 100, FALSE),
('Topi Rimba AdventureHat', 'Pakaian', 'https://via.placeholder.com/300x200.png?text=AdventureHat', 'Topi rimba dengan tali, melindungi dari sinar matahari dan hujan ringan.', 110000.00, 4.3, 65, FALSE),
('Sepatu Air AquaStride', 'Sepatu Air', 'https://via.placeholder.com/300x200.png?text=SepatuAquaStride', 'Sepatu air dengan sol anti slip, cepat kering, cocok untuk aktivitas sungai.', 320000.00, 4.6, 77, FALSE),
('Tenda Family Dome Lux 6P', 'Tenda', 'https://via.placeholder.com/300x200.png?text=TendaFamilyLux', 'Tenda keluarga besar kapasitas 6 orang, dua ruangan, ventilasi baik.', 1850000.00, 4.8, 135, TRUE),
('Tas Kamera Outdoor ProCam', 'Tas', 'https://via.placeholder.com/300x200.png?text=TasKameraPro', 'Tas khusus kamera untuk kegiatan outdoor, padded dan tahan benturan.', 650000.00, 4.7, 55, FALSE),
('Power Bank Solar Voyager 20000mAh', 'Peralatan Elektronik', 'https://via.placeholder.com/300x200.png?text=PowerBankSolar', 'Power bank 20000mAh dengan panel surya untuk pengisian darurat.', 550000.00, 4.5, 98, FALSE),
('GPS Tracker AdventureNav', 'Peralatan Navigasi', 'https://via.placeholder.com/300x200.png?text=GPSTrackerNav', 'GPS tracker genggam dengan peta topografi, akurat dan handal.', 2500000.00, 4.9, 70, FALSE),
('First Aid Kit OutdoorMedic', 'Peralatan Survival', 'https://via.placeholder.com/300x200.png?text=FirstAidKit', 'Kit P3K lengkap untuk kegiatan outdoor, kemasan kompak dan tahan air.', 280000.00, 4.7, 102, FALSE),
('Rain Cover Bag ShieldUp 80L', 'Aksesoris Pendakian', 'https://via.placeholder.com/300x200.png?text=RainCover80L', 'Sarung tas anti hujan untuk carrier hingga 80 liter.', 90000.00, 4.6, 120, FALSE),
('Meja Lipat Mini CampTable', 'Perlengkapan Camping', 'https://via.placeholder.com/300x200.png?text=MejaLipatMini', 'Meja lipat mini berbahan aluminium, ringan dan kokoh.', 210000.00, 4.4, 60, TRUE),
('Buff Multifungsi CoolMax', 'Pakaian', 'https://via.placeholder.com/300x200.png?text=BuffCoolMax', 'Buff multifungsi bahan CoolMax, bisa jadi masker, bandana, dll.', 75000.00, 4.5, 130, FALSE),
('Teropong Binocular ExplorerView 8x42', 'Peralatan Observasi', 'https://via.placeholder.com/300x200.png?text=Teropong8x42', 'Teropong binocular 8x42, pandangan jernih dan luas, cocok untuk observasi alam.', 850000.00, 4.7, 45, FALSE);

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
