<?php
// File: ajax_update_cart.php
session_start();
require_once "config.php"; // Pastikan $conn (koneksi DB) ada di sini

header('Content-Type: application/json'); // Memberitahu browser bahwa outputnya JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_quantity') {
    $itemKey = $_POST['item_key'] ?? null;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    if (!$itemKey || $quantity < 1 || !$productId || !isset($_SESSION['unified_cart'][$itemKey])) {
        echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
        exit;
    }

    $cartItem = &$_SESSION['unified_cart'][$itemKey]; // Gunakan reference agar perubahan langsung ke session
    $oldQuantity = $cartItem['quantity']; // Simpan kuantitas lama untuk rollback jika perlu

    // --- Pengecekan Stok (Contoh untuk item 'buy') ---
    if ($cartItem['type'] === 'buy') {
        $stmt_stock = $conn->prepare("SELECT stock_quantity FROM product_stock WHERE product_id = ?");
        if ($stmt_stock) {
            $stmt_stock->bind_param("i", $productId);
            $stmt_stock->execute();
            $result_stock = $stmt_stock->get_result();
            $stock_data = $result_stock->fetch_assoc();
            $stmt_stock->close();

            if ($stock_data) {
                $availableStock = (int)$stock_data['stock_quantity'];
                if ($quantity > $availableStock) {
                    // Stok tidak cukup, sesuaikan kuantitas ke stok yang tersedia
                    $adjustedQuantity = $availableStock > 0 ? $availableStock : 0; // jika stok 0, set kuantitas ke 0 (akan dihapus) atau 1 jika min order 1
                     if ($adjustedQuantity == 0 && $availableStock == 0) { // Jika memang stok habis total
                        // Opsi: Hapus item dari keranjang jika kuantitas jadi 0
                        // unset($_SESSION['unified_cart'][$itemKey]);
                        // echo json_encode(['success' => false, 'message' => 'Stok produk habis.', 'item_removed' => true, 'old_quantity' => $oldQuantity]);
                        // exit;
                        // Atau kembalikan ke kuantitas lama dan beri pesan
                        echo json_encode(['success' => false, 'message' => "Stok tidak cukup. Tersedia: $availableStock.", 'old_quantity' => $oldQuantity, 'available_stock' => $availableStock]);
                        exit;
                    } else if ($availableStock > 0) {
                        $cartItem['quantity'] = $adjustedQuantity;
                         $message = "Kuantitas disesuaikan karena stok terbatas. Tersedia: $availableStock.";
                    }
                   
                } else {
                    $cartItem['quantity'] = $quantity;
                    $message = 'Kuantitas diperbarui.';
                }
            } else {
                // Produk tidak ditemukan di tabel stok, mungkin error atau produk memang tidak butuh stok (sewa)
                // Untuk produk beli, idealnya harus ada data stok.
                 $cartItem['quantity'] = $quantity; // Lanjutkan update jika tidak ada info stok (asumsi boleh)
                 $message = 'Kuantitas diperbarui (info stok tidak ditemukan).';
            }
        } else {
             // Gagal prepare statement, mungkin error DB
            echo json_encode(['success' => false, 'message' => 'Kesalahan database saat cek stok.', 'old_quantity' => $oldQuantity]);
            exit;
        }
    } else { // Untuk item 'rent', logika stok mungkin berbeda atau tidak ada
        $cartItem['quantity'] = $quantity;
        $message = 'Kuantitas sewa diperbarui.';
    }
    // --- Akhir Pengecekan Stok ---

    // Hitung ulang subtotal
    $price_per_unit = ($cartItem['type'] === 'buy') ? $cartItem['price'] : $cartItem['price_per_item_per_duration'];
    $cartItem['subtotal'] = $price_per_unit * $cartItem['quantity'];

    // Hitung ulang grand total dari session
    $currentGrandTotal = 0;
    foreach($_SESSION['unified_cart'] as $ci) {
        $currentGrandTotal += $ci['subtotal'];
    }

    echo json_encode([
        'success' => true, 
        'message' => $message ?? 'Keranjang berhasil diperbarui.',
        'adjusted_quantity' => $cartItem['quantity'], // Kirim kuantitas yang sudah disesuaikan
        'new_subtotal_formatted' => 'Rp ' . number_format($cartItem['subtotal'], 0, ',', '.'),
        'grand_total_formatted' => 'Rp ' . number_format($currentGrandTotal, 0, ',', '.')
    ]);
    exit;

} else {
    echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
    exit;
}
?>