<?php
// File: Web Jatilawang/proses_checkout.php
session_start();
require_once "config.php";
require_once "functions.php";

if (!isLoggedIn()) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Sesi Anda telah berakhir. Silakan login kembali untuk melanjutkan.'];
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    if (empty($_SESSION['unified_cart'])) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Keranjang Anda kosong. Tidak ada yang bisa diproses.'];
        header("Location: keranjang.php");
        exit;
    }

    // Dapatkan user_id dari email yang tersimpan di session
    $user_email = $_SESSION['user'];
    $stmt_user = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt_user) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Terjadi kesalahan database (User P1). Silakan coba lagi. Error: ' . $conn->error];
        header("Location: checkout.php");
        exit;
    }
    $stmt_user->bind_param("s", $user_email);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    if ($result_user->num_rows > 0) {
        $user_data = $result_user->fetch_assoc();
        $user_id = $user_data['id'];
    } else {
        session_destroy();
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Data pengguna tidak valid. Silakan login kembali.'];
        header("Location: login.php");
        exit;
    }
    $stmt_user->close();

    $conn->begin_transaction();

    try {
        $current_datetime_for_order = date("Y-m-d H:i:s"); // Waktu saat order dikonfirmasi
        $processed_product_order_ids = [];
        $processed_rental_order_ids = [];
        $has_product_orders = false;
        $has_rental_orders = false;
        // --- VALIDASI STOK SEBELUM PROSES ---
        foreach ($_SESSION['unified_cart'] as $item_key => $item) {
            if ($item['type'] === 'buy') {
                $stmt_check_stock = $conn->prepare("SELECT stock_quantity FROM product_stock WHERE product_id = ?");
                if (!$stmt_check_stock) throw new Exception("Database error (Stock Check P1): " . $conn->error);

                $stmt_check_stock->bind_param("i", $item['product_id']);
                $stmt_check_stock->execute();
                $result_stock = $stmt_check_stock->get_result();
                $stock_data = $result_stock->fetch_assoc();
                $stmt_check_stock->close();

                if (!$stock_data || $stock_data['stock_quantity'] < $item['quantity']) {
                    $_SESSION['message'] = [
                        'type' => 'error',
                        'text' => "Stok untuk produk '" . htmlspecialchars($item['name']) . "' tidak mencukupi (tersedia: " . ($stock_data['stock_quantity'] ?? 0) . ", diminta: " . $item['quantity'] . "). Silakan perbarui keranjang Anda."
                    ];
                    header("Location: keranjang.php"); // Arahkan kembali ke keranjang
                    exit;
                }
            }
        }
        // Prepare statements
        $stmt_product_order = $conn->prepare("INSERT INTO product_orders (user_id, product_id, order_date, quantity, total_price, status) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt_product_order) {
            throw new Exception("Database error (Product Order P1): " . $conn->error);
        }

        // Pastikan tabel rental_orders ada dan memiliki kolom yang diperlukan
        // (id, user_id, product_id, quantity, rental_start_date, rental_end_date, order_placed_date, duration_type, price_per_item_per_duration, total_price, status)
        $stmt_rental_order = $conn->prepare("INSERT INTO rental_orders (user_id, product_id, quantity, rental_start_date, rental_end_date, order_placed_date, duration_type, price_per_item_per_duration, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt_rental_order) {
            throw new Exception("Database error (Rental Order P1): " . $conn->error);
        }

        $default_status = 'pending';

        foreach ($_SESSION['unified_cart'] as $item_key => $item) {
            if ($item['type'] === 'buy') {
                $has_product_orders = true;
                $stmt_product_order->bind_param("iisids", $user_id, $item['product_id'], $current_datetime_for_order, $item['quantity'], $item['subtotal'], $default_status);
                if (!$stmt_product_order->execute()) {
                    throw new Exception("Gagal menyimpan detail pesanan beli untuk produk ID: " . $item['product_id'] . ". Error: " . $stmt_product_order->error);
                }
                $processed_product_order_ids[] = $conn->insert_id;
            } elseif ($item['type'] === 'rent') {
                $has_rental_orders = true;
                $rental_start_date_obj = new DateTime($item['start_date']);
                // Buat salinan objek untuk dimodifikasi
                $rental_end_date_obj = new DateTime($item['start_date']);

                // Logika yang disederhanakan dan diperbaiki
                switch ($item['duration_type']) {
                    case 'daily':
                        $rental_end_date_obj->modify('+1 day');
                        break;
                    case 'weekly':
                        $rental_end_date_obj->modify('+1 week');
                        break;
                    case 'monthly':
                        $rental_end_date_obj->modify('+1 month');
                        break;
                    default:
                        throw new Exception("Durasi sewa tidak dikenal: " . $item['duration_type']);
                }

                // Format tanggal untuk DB
                $rental_start_date_db = $rental_start_date_obj->format('Y-m-d H:i:s');
                $rental_end_date_db = $rental_end_date_obj->format('Y-m-d H:i:s');


                $stmt_rental_order->bind_param(
                    "iiisssssds",
                    $user_id,
                    $item['product_id'],
                    $item['quantity'],
                    $rental_start_date_db,
                    $rental_end_date_db,
                    $current_datetime_for_order, // order_placed_date
                    $item['duration_type'],
                    $item['price_per_item_per_duration'],
                    $item['subtotal'],
                    $default_status
                );
                if (!$stmt_rental_order->execute()) {
                    throw new Exception("Gagal menyimpan detail pesanan sewa untuk produk ID: " . $item['product_id'] . ". Error: " . $stmt_rental_order->error);
                }
                $processed_rental_order_ids[] = $conn->insert_id;
            }
        }

        $stmt_product_order->close();
        $stmt_rental_order->close();
        $conn->commit();
        if ($has_product_orders) { // Anda sudah punya flag $has_product_orders
            foreach ($_SESSION['unified_cart'] as $item_key => $item) {
                if ($item['type'] === 'buy') {
                    $stmt_update_stock = $conn->prepare("UPDATE product_stock SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
                    if (!$stmt_update_stock) {
                        // Ini adalah masalah serius, order sudah dibuat tapi stok gagal diupdate
                        // Perlu ada mekanisme logging atau notifikasi admin
                        error_log("KRITIKAL: Gagal prepare update stok untuk produk ID " . $item['product_id'] . " setelah order berhasil. Error: " . $conn->error);
                        // Mungkin jangan throw exception di sini agar user tidak lihat error jika order sudah masuk,
                        // tapi admin HARUS tahu.
                    } else {
                        $stmt_update_stock->bind_param("ii", $item['quantity'], $item['product_id']);
                        if (!$stmt_update_stock->execute()) {
                            error_log("KRITIKAL: Gagal execute update stok untuk produk ID " . $item['product_id'] . ". Error: " . $stmt_update_stock->error);
                        }
                        $stmt_update_stock->close();
                    }
                }
            }
        }

        // Persiapkan data untuk receipt.php
        // Kita simpan timestamp umum dan flag apakah ada order beli/sewa
        $_SESSION['last_order_details'] = [
            'user_id' => $user_id,
            'order_timestamp' => $current_datetime_for_order, // Timestamp umum
            'has_product_orders' => $has_product_orders,
            'has_rental_orders' => $has_rental_orders
            // Anda bisa juga menyimpan array ID order jika diperlukan:
            // 'product_order_ids' => $processed_product_order_ids,
            // 'rental_order_ids' => $processed_rental_order_ids,
        ];

        unset($_SESSION['unified_cart']);
        header("Location: receipt.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memproses pesanan: ' . $e->getMessage()];
        error_log("Checkout Error: " . $e->getMessage()); // Log error ke server log
        header("Location: checkout.php");
        exit;
    }
} else {
    header("Location: checkout.php");
    exit;
}
