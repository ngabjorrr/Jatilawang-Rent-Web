<?php
session_start();
require_once "config.php"; // Untuk koneksi $conn
require_once "functions.php"; // Untuk isLoggedIn()

if (!isLoggedIn()) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Sesi Anda telah berakhir. Silakan login kembali untuk melanjutkan.'];
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    if (empty($_SESSION['cart'])) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Keranjang Anda kosong. Tidak ada yang bisa diproses.'];
        header("Location: keranjang.php");
        exit;
    }

    // Dapatkan user_id dari email yang tersimpan di session
    $user_email = $_SESSION['user']; // $_SESSION['user'] menyimpan email
    $stmt_user = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt_user) {
        // Error saat prepare statement
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
        // User tidak ditemukan berdasarkan email di session, ini aneh.
        session_destroy(); // Hancurkan sesi bermasalah
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Data pengguna tidak valid. Silakan login kembali.'];
        header("Location: login.php");
        exit;
    }
    $stmt_user->close();

    // Mulai transaksi database (opsional, tapi baik untuk konsistensi data)
    $conn->begin_transaction();

    try {
        $order_date = date("Y-m-d H:i:s"); // Tanggal dan waktu pesanan saat ini

        // Gunakan tabel 'product_orders' sesuai skema database Anda
        $stmt_order = $conn->prepare("INSERT INTO product_orders (user_id, product_id, order_date, quantity, total_price, status) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt_order) {
             throw new Exception("Database error (Order P1): " . $conn->error);
        }

        $default_status = 'pending'; // Status awal pesanan

        foreach ($_SESSION['cart'] as $product_id => $item) {
            $quantity = $item['quantity'];
            $total_price_item = $item['price'] * $quantity;

            $stmt_order->bind_param("iisids", $user_id, $product_id, $order_date, $quantity, $total_price_item, $default_status);
            if (!$stmt_order->execute()) {
                throw new Exception("Gagal menyimpan detail pesanan untuk produk ID: " . $product_id . ". Error: " . $stmt_order->error);
            }
        }
        $stmt_order->close();

        // Jika semua berhasil, commit transaksi
        $conn->commit();

        // Kosongkan keranjang
        unset($_SESSION['cart']);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Pesanan Anda telah berhasil dibuat! Terima kasih telah berbelanja.'];
        header("Location: index.php"); // Arahkan ke halaman utama atau halaman histori pesanan
        exit;

    } catch (Exception $e) {
        $conn->rollback(); // Batalkan semua perubahan jika ada error
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memproses pesanan: ' . $e->getMessage()];
        header("Location: checkout.php");
        exit;
    }

} else {
    // Jika akses langsung atau metode salah
    header("Location: checkout.php");
    exit;
}
?>