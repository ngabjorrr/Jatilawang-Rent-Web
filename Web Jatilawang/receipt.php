<?php
// File: Web Jatilawang/receipt.php
session_start();
require_once "config.php";
require_once "functions.php";

if (!isLoggedIn() || !isset($_SESSION['last_order_details'])) {
    header("Location: index.php");
    exit;
}

$last_order_info = $_SESSION['last_order_details'];
$user_id_receipt = $last_order_info['user_id'];
$order_timestamp_receipt = $last_order_info['order_timestamp']; // Timestamp umum

unset($_SESSION['last_order_details']); // Hapus setelah diambil

$ordered_buy_items = [];
$grand_total_buy = 0;
if ($last_order_info['has_product_orders']) {
    $stmt_buy_items = $conn->prepare(
        "SELECT po.*, p.name as product_name, p.image as product_image 
         FROM product_orders po
         JOIN products p ON po.product_id = p.id
         WHERE po.user_id = ? AND po.order_date = ?"
    );
    if ($stmt_buy_items) {
        $stmt_buy_items->bind_param("is", $user_id_receipt, $order_timestamp_receipt);
        $stmt_buy_items->execute();
        $result_buy_items = $stmt_buy_items->get_result();
        while ($item = $result_buy_items->fetch_assoc()) {
            $ordered_buy_items[] = $item;
            $grand_total_buy += $item['total_price'];
        }
        $stmt_buy_items->close();
    } else { /* Handle error */ }
}

$ordered_rent_items = [];
$grand_total_rent = 0;
if ($last_order_info['has_rental_orders']) {
    // Asumsi tabel rental_orders memiliki kolom order_placed_date yang sama dengan order_timestamp_receipt
    // dan kolom quantity, price_per_item_per_duration, duration_type
    $stmt_rent_items = $conn->prepare(
        "SELECT ro.*, p.name as product_name, p.image as product_image 
         FROM rental_orders ro
         JOIN products p ON ro.product_id = p.id
         WHERE ro.user_id = ? AND ro.order_placed_date = ?" // Menggunakan order_placed_date
    );
     if ($stmt_rent_items) {
        $stmt_rent_items->bind_param("is", $user_id_receipt, $order_timestamp_receipt);
        $stmt_rent_items->execute();
        $result_rent_items = $stmt_rent_items->get_result();
        while ($item = $result_rent_items->fetch_assoc()) {
            $ordered_rent_items[] = $item;
            $grand_total_rent += $item['total_price'];
        }
        $stmt_rent_items->close();
    } else { /* Handle error */ }
}

$stmt_user_info = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$user_info = ['name' => 'Pelanggan', 'email' => 'Tidak diketahui'];
if($stmt_user_info) {
    $stmt_user_info->bind_param("i", $user_id_receipt);
    $stmt_user_info->execute();
    $result_user_info = $stmt_user_info->get_result();
    $user_info_data = $result_user_info->fetch_assoc();
    if($user_info_data) $user_info = $user_info_data;
    $stmt_user_info->close();
}

$grand_total_receipt = $grand_total_buy + $grand_total_rent;

$pageTitle = "Konfirmasi Pesanan Terpadu";
include "app/views/header.php";
?>

<div class="page-container" style="max-width: 800px; margin: 20px auto; padding: 20px; background-color: #fff; border: 1px solid #ddd; border-radius: 5px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <img src="public/assets/Logo.png" alt="Logo Toko" style="max-width:100px; margin-bottom:15px;">
        <h2 style="color: #2d6a4f; margin-bottom:10px;">Terima Kasih Sudah Berbelanja & Menyewa!</h2>
        <p>Pesanan Anda telah berhasil kami terima dan akan segera diproses.</p>
    </div>

    <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">Detail Transaksi</h3>
    <div style="margin-bottom: 20px;">
        <p><strong>Waktu Transaksi:</strong> <?= htmlspecialchars(date("d M Y, H:i", strtotime($order_timestamp_receipt))); ?></p>
        <p><strong>Pelanggan:</strong> <?= htmlspecialchars($user_info['name']); ?> (<?= htmlspecialchars($user_info['email']); ?>)</p>
        <p><strong>Total Pembayaran Keseluruhan:</strong> <strong style="color: #e74c3c;">Rp <?= number_format($grand_total_receipt, 0, ',', '.'); ?></strong></p>
    </div>

    <?php if (!empty($ordered_buy_items)): ?>
        <h4>Item yang Dibeli:</h4>
        <table style="width:100%; border-collapse: collapse; margin-bottom: 30px;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Produk</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Kuantitas</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Harga Satuan</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordered_buy_items as $item): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; display: flex; align-items: center;">
                            <img src="<?= htmlspecialchars($item['product_image'] ?? 'public/assets/placeholder.png'); ?>" alt="<?= htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 3px;">
                            <?= htmlspecialchars($item['product_name']); ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;"><?= htmlspecialchars($item['quantity']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp <?= number_format($item['total_price'] / $item['quantity'], 0, ',', '.'); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp <?= number_format($item['total_price'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (!empty($ordered_rent_items)): ?>
        <h4>Item yang Disewa:</h4>
         <table style="width:100%; border-collapse: collapse; margin-bottom: 30px;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Produk</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Kuantitas</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Detail Sewa</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordered_rent_items as $item): ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; display: flex; align-items: center;">
                             <img src="<?= htmlspecialchars($item['product_image'] ?? 'public/assets/placeholder.png'); ?>" alt="<?= htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 3px;">
                            <?= htmlspecialchars($item['product_name']); ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;"><?= htmlspecialchars($item['quantity']); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: left;">
                            Durasi: <?= htmlspecialchars(ucfirst($item['duration_type'])); ?><br>
                            Mulai: <?= htmlspecialchars(date('d M Y', strtotime($item['rental_start_date']))); ?><br>
                            Selesai: <?= htmlspecialchars(date('d M Y', strtotime($item['rental_end_date']))); ?><br>
                            Harga: Rp <?= number_format($item['price_per_item_per_duration'], 0, ',', '.'); ?> /unit/durasi
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp <?= number_format($item['total_price'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>


    <div style="text-align: center; margin-top: 30px;">
        <a href="index.php" style="padding: 12px 25px; background-color: #2d6a4f; color: white; text-decoration: none; border-radius: 5px; font-size: 1em;">Lanjut Belanja</a>
    </div>
</div>

<?php include "app/views/footer.php"; ?>