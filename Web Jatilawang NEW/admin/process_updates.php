<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// Memuat file konfigurasi dan fungsi (termasuk fungsi isAdmin())
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';

// 1. KEAMANAN: Memastikan hanya admin yang bisa menjalankan file ini
function isAdmin() {
    // Pastikan session sudah dimulai sebelum mengakses $_SESSION
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Memastikan ini adalah request POST dan ada 'action' yang dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $action = $_POST['action'];

    // 2. AKSI: Update Stok
    if ($action === 'update_stock') {
        $product_id = (int)$_POST['product_id'];
        $new_stock = (int)$_POST['new_stock'];

        $stmt = $conn->prepare("UPDATE product_stock SET stock_quantity = ? WHERE product_id = ?");
        $stmt->bind_param("ii", $new_stock, $product_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Stok berhasil diperbarui.'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memperbarui stok.'];
        }
        header("Location: kelola_stok.php");
        exit;
    }

    // 3. FITUR BARU: Hapus Produk
    elseif ($action === 'delete_product') {
        $product_id = (int)$_POST['product_id'];

        // Menggunakan transaksi untuk menjaga data tetap konsisten
        $conn->begin_transaction();
        try {
            // Hapus dari tabel stok
            $stmt1 = $conn->prepare("DELETE FROM product_stock WHERE product_id = ?");
            $stmt1->bind_param("i", $product_id);
            $stmt1->execute();
            $stmt1->close();

            // Hapus dari tabel produk utama
            $stmt2 = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt2->bind_param("i", $product_id);
            $stmt2->execute();
            $stmt2->close();

            // Jika berhasil, simpan perubahan
            $conn->commit();
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Produk berhasil dihapus secara permanen.'];
        } catch (mysqli_sql_exception $exception) {
            // Jika gagal, batalkan semua perubahan
            $conn->rollback();
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal menghapus produk. Kemungkinan produk masih tertaut dengan data pesanan.'];
        }
        
        header("Location: kelola_stok.php");
        exit;
    }

    // 4. AKSI: Update Status Pesanan Beli
    elseif ($action === 'update_buy_status') {
        $order_id = (int)$_POST['order_id'];
        $new_status = $_POST['new_status'];
        
        $stmt = $conn->prepare("UPDATE product_orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Status pesanan pembelian berhasil diubah.'];
        header("Location: kelola_pesanan.php");
        exit;
    }

    // 5. AKSI: Update Status Pesanan Sewa
    elseif ($action === 'update_rent_status') {
        $order_id = (int)$_POST['order_id'];
        $new_status = $_POST['new_status'];
        
        $stmt = $conn->prepare("UPDATE rental_orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Status pesanan sewa berhasil diubah.'];
        header("Location: kelola_pesanan.php");
        exit;
    }

    // 6. AKSI: Keluarkan Denda
    elseif ($action === 'issue_fine') {
        $order_id = (int)$_POST['order_id'];
        $days_overdue = (int)$_POST['days_overdue'];
        $fine_amount = (float)$_POST['fine_amount'];

        $stmt = $conn->prepare("INSERT INTO rental_fines (rental_order_id, days_overdue, fine_amount) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $order_id, $days_overdue, $fine_amount);
        if ($stmt->execute()) {
             $_SESSION['message'] = ['type' => 'success', 'text' => 'Denda berhasil dicatat.'];
        } else {
             $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal mencatat denda.'];
        }
        header("Location: kelola_pesanan.php");
        exit;
    }
}

// Jika file ini diakses tanpa aksi yang valid, kembalikan ke dashboard.
header("Location: index.php");
exit;