-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jun 2025 pada 19.32
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jatilawang_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `reviews` int(11) DEFAULT NULL,
  `is_rentable` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `image`, `description`, `price`, `rating`, `reviews`, `is_rentable`) VALUES
(1, 'Sepatu Gunung\r\n', 'Sepatu Gunung', 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full//catalog-image/102/MTA-121296684/br-m036969-08988_sepatu-gunung-505-grey-red-sports-hiking-outdoor-89-waterproof-size-39-40-41-42-43-44_full01-0fa1c4e2.jpg', 'Deskripsi produk...', 20000.00, 4.7, 123, 1),
(2, 'Tenda Jenis 1', 'Tenda', 'public/assets/tenda1.jpg', 'Deskripsi produk...', 0.00, 4.7, 188, 1),
(3, 'Tas Jenis 3', 'Tas', 'public/assets/tas3.jpg', 'Deskripsi produk...', 150.00, 4.6, 70, 0),
(4, 'Sepatu Gunung Pro', 'Sepatu Gunung', 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c', 'Sepatu gunung tahan air dengan sol anti-selip, cocok untuk pendakian berat.', 850000.00, 4.8, 95, 1),
(5, 'Tenda Dome 4 Orang', 'Tenda', 'https://images.unsplash.com/photo-1504274066651-8d31a536b11a', 'Tenda dome kapasitas 4 orang, mudah dipasang dan tahan cuaca ekstrem.', 1200000.00, 4.7, 110, 1),
(6, 'Tas Carrier 60L', 'Tas', 'https://images.unsplash.com/photo-1558980664-10b1c7e8c7b4', 'Tas carrier berkapasitas 60 liter dengan bantalan punggung ergonomis.', 650000.00, 4.6, 80, 0),
(7, 'Kompor Portable Gas', 'Peralatan Masak', 'https://images.pexels.com/photos/1234567/pexels-photo-1234567.jpeg', 'Kompor gas portable ringan, cocok untuk camping dan hiking.', 250000.00, 4.5, 60, 1),
(8, 'Sleeping Bag Musim Dingin', 'Sleeping Bag', 'https://images.unsplash.com/photo-1523413651479-597eb2da0ad6', 'Sleeping bag tahan suhu hingga -10°C, ideal untuk camping musim dingin.', 400000.00, 4.9, 70, 0),
(9, 'Matras Lipat Ringan', 'Matras', 'https://images.pexels.com/photos/2345678/pexels-photo-2345678.jpeg', 'Matras lipat ringan dan mudah dibawa, memberikan kenyamanan saat tidur.', 150000.00, 4.4, 50, 0),
(10, 'Lampu Tenda LED', 'Penerangan', 'https://antarestar.com/wp-content/uploads/2022/08/Antarestar-Lampu-Camping-Model-Bakpao-3.jpg', 'Lampu LED hemat energi dengan gantungan, menerangi tenda secara optimal.', 100000.00, 4.6, 65, 1),
(11, 'Jaket Gunung Waterproof', 'Pakaian', 'https://images.pexels.com/photos/3456789/pexels-photo-3456789.jpeg', 'Jaket gunung tahan air dengan ventilasi udara, menjaga tubuh tetap kering.', 550000.00, 4.7, 90, 0),
(12, 'Trekking Pole Aluminium', 'Aksesoris', 'https://images.unsplash.com/photo-1508780709619-79562169bc64', 'Tongkat trekking ringan dari aluminium, memberikan stabilitas saat hiking.', 200000.00, 4.5, 55, 1),
(13, 'Filter Air Portable', 'Peralatan Survival', 'https://images.pexels.com/photos/4567890/pexels-photo-4567890.jpeg', 'Filter air portable yang mampu menyaring bakteri dan kotoran, aman untuk diminum.', 300000.00, 4.8, 75, 0),
(14, 'Nama Produk Baru', 'Kategori Produk', 'public/assets/nama_gambar.jpg', 'Deskripsi singkat produk ini.', 250000.00, 4.5, 50, 0),
(15, 'Alat Kemah Pro', 'Peralatan', 'public/assets/alatkemah.jpg', 'Peralatan kemah profesional untuk petualangan Anda.', 0.00, 4.8, 120, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_orders`
--

CREATE TABLE `product_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `quantity` int(11) DEFAULT 1,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','shipped','delivered','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `product_orders`
--

INSERT INTO `product_orders` (`id`, `user_id`, `product_id`, `order_date`, `quantity`, `total_price`, `status`) VALUES
(1, 1, 5, '2025-06-02 17:55:46', 1, 1200000.00, 'pending'),
(2, 1, 6, '2025-06-02 18:06:38', 1, 650000.00, 'pending'),
(3, 1, 4, '2025-06-02 18:06:38', 1, 850000.00, 'pending'),
(4, 1, 4, '2025-06-02 18:22:53', 2, 1700000.00, 'pending'),
(5, 1, 5, '2025-06-02 18:22:53', 1, 1200000.00, 'pending'),
(6, 1, 6, '2025-06-02 18:22:53', 1, 650000.00, 'pending'),
(7, 1, 4, '2025-06-02 18:22:53', 1, 850000.00, 'pending'),
(8, 1, 4, '2025-06-02 18:49:13', 1, 850000.00, 'pending');

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_stock`
--

CREATE TABLE `product_stock` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `product_stock`
--

INSERT INTO `product_stock` (`id`, `product_id`, `stock_quantity`) VALUES
(1, 1, 15),
(2, 2, 10),
(3, 3, 25),
(4, 4, 20),
(5, 5, 8),
(6, 6, 30),
(7, 7, 18),
(8, 10, 22),
(9, 12, 12),
(10, 14, 40),
(11, 15, 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `rental_orders`
--

CREATE TABLE `rental_orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rental_start_date` datetime DEFAULT NULL,
  `rental_end_date` datetime DEFAULT NULL,
  `order_placed_date` datetime DEFAULT current_timestamp(),
  `duration_type` varchar(20) DEFAULT NULL,
  `price_per_item_per_duration` decimal(10,2) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `status` enum('pending','active','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rental_orders`
--

INSERT INTO `rental_orders` (`id`, `user_id`, `product_id`, `rental_start_date`, `rental_end_date`, `order_placed_date`, `duration_type`, `price_per_item_per_duration`, `total_price`, `quantity`, `status`) VALUES
(1, 1, 1, '2025-06-02 00:00:00', '2025-07-02 00:00:00', '2025-06-02 18:49:13', 'monthly', 500000.00, 500000.00, 1, 'pending'),
(2, 1, 1, '2025-06-18 00:00:00', '2025-06-25 00:00:00', '2025-06-02 18:49:13', 'weekly', 150000.00, 150000.00, 1, 'pending'),
(3, 1, 1, '2025-06-26 00:00:00', '2025-07-26 00:00:00', '2025-06-02 18:49:13', 'monthly', 500000.00, 500000.00, 1, 'pending'),
(4, 1, 1, '2025-06-02 00:00:00', '2025-06-09 00:00:00', '2025-06-02 18:52:57', 'weekly', 150000.00, 150000.00, 1, 'pending');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rental_prices`
--

CREATE TABLE `rental_prices` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `daily_price` decimal(10,2) DEFAULT NULL,
  `weekly_price` decimal(10,2) DEFAULT NULL,
  `monthly_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rental_prices`
--

INSERT INTO `rental_prices` (`id`, `product_id`, `daily_price`, `weekly_price`, `monthly_price`) VALUES
(1, 1, 25000.00, 150000.00, 500000.00),
(2, 2, 75000.00, 450000.00, 1500000.00),
(3, 5, 50000.00, 300000.00, 1000000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `address`, `phone`, `name`, `username`) VALUES
(1, 'admin@jatilawang.com', '$2y$10$/pzj5c9Gm9GhRxZWerXs6Ok6eKEto55wqByhaoii0aSbcUpqawGyy', NULL, NULL, 'Admin User', ''),
(2, 'briflywattimury@gmail.com', '$2y$10$Hxx5iyQO.TvXYIxOAQgY4u/JaV/gIYJRdp90HnPTkymPb4yxAwU2.', 'Jln. Christina Martha Tiahahu, Maruru, Amahai, Maluku Tengah, Maluku', '081247443119', 'Brifly Anthon Wattimury', 'Bilabong29');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `product_orders`
--
ALTER TABLE `product_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `product_stock`
--
ALTER TABLE `product_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `rental_orders`
--
ALTER TABLE `rental_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `rental_prices`
--
ALTER TABLE `rental_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rental_product` (`product_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `product_orders`
--
ALTER TABLE `product_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `product_stock`
--
ALTER TABLE `product_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `rental_orders`
--
ALTER TABLE `rental_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `rental_prices`
--
ALTER TABLE `rental_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `product_orders`
--
ALTER TABLE `product_orders`
  ADD CONSTRAINT `product_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `product_orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ketidakleluasaan untuk tabel `product_stock`
--
ALTER TABLE `product_stock`
  ADD CONSTRAINT `product_stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ketidakleluasaan untuk tabel `rental_orders`
--
ALTER TABLE `rental_orders`
  ADD CONSTRAINT `rental_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `rental_orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Ketidakleluasaan untuk tabel `rental_prices`
--
ALTER TABLE `rental_prices`
  ADD CONSTRAINT `fk_rental_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
