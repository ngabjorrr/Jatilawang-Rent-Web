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
                $rental_end_date_obj = new DateTime($item['start_date']); // Salin tanggal mulai

                switch ($item['duration_type']) {
                    case 'daily':
                        $rental_end_date_obj->modify('+'.$item['quantity'].' day'); // Misal, sewa 2 unit "harian" tetap masing-masing 1 hari berakhirnya sama. 
                                                                                 // Jika "quantity" di sini maksudnya "jumlah hari sewa", maka logikanya beda.
                                                                                 // Asumsi 'quantity' adalah jumlah unit barang, durasi tetap per unit.
                                                                                 // Jika mau 1 item sewa untuk N hari (paket harian N kali), maka struktur cart perlu diubah
                                                                                 // atau 'quantity' di form rental berarti 'jumlah hari/minggu/bulan'.
                                                                                 // Untuk saat ini, asumsikan 'quantity' adalah unit barang.
                        $endDateModifier = '+1 day'; // Setiap unit disewa untuk 1 hari
                        if ($item['duration_type'] == 'daily') $endDateModifier = '+1 day'; // default 1 day per unit
                        // Logic to calculate actual end date based on duration and quantity if needed.
                        // For simplicity, let's assume the price_per_item_per_duration is for ONE duration period.
                        // And quantity means number of such items for that one period.
                        $tempEndDate = new DateTime($item['start_date']);
                        if ($item['duration_type'] == 'daily') $tempEndDate->modify('+1 day');
                        else if ($item['duration_type'] == 'weekly') $tempEndDate->modify('+1 week');
                        else if ($item['duration_type'] == 'monthly') $tempEndDate->modify('+1 month');
                        $rental_end_date_str = $tempEndDate->format('Y-m-d H:i:s');

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


                $stmt_rental_order->bind_param("iiisssssds", 
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
?>