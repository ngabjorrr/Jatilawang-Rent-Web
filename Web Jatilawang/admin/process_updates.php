<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';

// 1. FUNGSI PROTEKSI (didefinisikan sekali di atas)
function isAdmin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// 2. PERLINDUNGAN HALAMAN (PENTING!)
// Pastikan hanya admin yang bisa menjalankan kode di bawah ini.
if (!isAdmin()) {
    // Jika bukan admin, kirim pesan error dan hentikan eksekusi.
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Akses ditolak. Anda tidak memiliki izin.'];
    header("Location: ../login.php");
    exit;
}

// 3. Memastikan ini adalah request POST dan ada 'action' yang dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $action = $_POST['action'];

    // AKSI: Update Stok
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

    // FITUR BARU: Hapus Produk
    elseif ($action === 'delete_product') {
        $product_id = (int)$_POST['product_id'];

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

            $conn->commit();
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Produk berhasil dihapus secara permanen.'];
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal menghapus produk. Kemungkinan produk masih tertaut dengan data pesanan.'];
        }

        header("Location: kelola_stok.php");
        exit;
    }

    // AKSI: Update Status Pesanan Beli
    elseif ($action === 'update_buy_status') {
        $order_id = (int)$_POST['order_id'];
        $new_status = $_POST['new_status'];

        $stmt = $conn->prepare("UPDATE product_orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Status pesanan pembelian berhasil diubah.'];
        } else {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal mengubah status pesanan pembelian.'];
        }
        header("Location: kelola_pesanan.php");
        exit;
    }

    // AKSI: Update Status Pesanan Sewa
    elseif ($action === 'update_rent_status') {
        $order_id = (int)$_POST['order_id'];
        $new_status = $_POST['new_status'];

        // Gunakan transaksi untuk memastikan kedua operasi (update order dan denda) berhasil
        $conn->begin_transaction();

        try {
            // Langkah 1: Update status pesanan sewa di tabel `rental_orders`
            $stmt_order = $conn->prepare("UPDATE rental_orders SET status = ? WHERE id = ?");
            $stmt_order->bind_param("si", $new_status, $order_id);
            $stmt_order->execute();
            $stmt_order->close();

            // Langkah 2: Jika status baru adalah 'completed', update denda terkait di tabel `rental_fines`
            if ($new_status === 'completed') {
                // Kueri yang lebih kuat: Langsung ubah status denda menjadi 'paid' untuk pesanan ini.
                // Ini akan berhasil meskipun status denda sebelumnya bukan 'pending'.
                // Jika tidak ada denda untuk pesanan ini, kueri tidak akan error.
                $stmt_fine = $conn->prepare("UPDATE rental_fines SET status = 'paid' WHERE rental_order_id = ?");
                $stmt_fine->bind_param("i", $order_id);
                $stmt_fine->execute();
                $stmt_fine->close();
            }

            // Jika semua langkah berhasil, simpan perubahan
            $conn->commit();
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Status pesanan sewa berhasil diperbarui.'];
        } catch (mysqli_sql_exception $exception) {
            // Jika ada kesalahan di salah satu langkah, batalkan semua perubahan
            $conn->rollback();
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Gagal memperbarui status: ' . $exception->getMessage()];
        }

        header("Location: kelola_pesanan.php");
        exit;
    }
    // AKSI: Keluarkan Denda
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

// Jika file ini diakses tanpa metode POST atau tanpa aksi yang valid, kembalikan ke dashboard.
header("Location: index.php");
exit;
