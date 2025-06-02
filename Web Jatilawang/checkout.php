<?php
// File: Web Jatilawang/checkout.php
session_start();
require_once "config.php";
require_once "functions.php";

if (!isLoggedIn()) {
    $_SESSION['last_product_url'] = 'checkout.php';
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Anda harus login untuk melanjutkan ke checkout.'];
    header("Location: login.php");
    exit;
}

$pageTitle = "Checkout";
include "app/views/header.php";

$unifiedCartItems = isset($_SESSION['unified_cart']) ? $_SESSION['unified_cart'] : [];
$grandTotal = 0;

if (empty($unifiedCartItems)) {
    $_SESSION['message'] = ['type' => 'info', 'text' => 'Keranjang Anda kosong. Tidak ada yang bisa dicheckout.'];
    // Redirect ke keranjang agar pesan bisa tampil di sana, atau langsung ke index
    header("Location: keranjang.php"); 
    exit;
}
?>

<div class="page-container">
    <h2 class="main-section-title"><?php echo $pageTitle; ?></h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?= htmlspecialchars($_SESSION['message']['type']); ?>" style="padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ccc; background-color: <?= $_SESSION['message']['type'] === 'success' ? '#d4edda' : ($_SESSION['message']['type'] === 'error' ? '#f8d7da' : '#cfe2ff') ; ?>; color: <?= $_SESSION['message']['type'] === 'success' ? '#155724' : ($_SESSION['message']['type'] === 'error' ? '#721c24' : '#084298'); ?>;">
            <?= htmlspecialchars($_SESSION['message']['text']); ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <h3>Ringkasan Pesanan</h3>
    <table style="width:100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Produk</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Detail</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Kuantitas</th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($unifiedCartItems as $key => $item): ?>
                <?php
                    $subtotal = $item['subtotal']; // Ambil subtotal yang sudah dihitung
                    $grandTotal += $subtotal;
                ?>
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        <?= htmlspecialchars($item['name']); ?>
                        <br><small>(Tipe: <?= htmlspecialchars(ucfirst($item['type'])); ?>)</small>
                    </td>
                     <td style="padding: 10px; border: 1px solid #ddd;">
                        <?php if ($item['type'] === 'buy'): ?>
                            Harga Satuan: Rp <?= number_format($item['price'], 0, ',', '.'); ?>
                        <?php elseif ($item['type'] === 'rent'): ?>
                            Durasi: <?= htmlspecialchars(ucfirst($item['duration_type'])); ?><br>
                            Mulai: <?= htmlspecialchars(date('d M Y', strtotime($item['start_date']))); ?><br>
                            Harga Sewa: Rp <?= number_format($item['price_per_item_per_duration'], 0, ',', '.'); ?> / unit
                        <?php endif; ?>
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: center;"><?= htmlspecialchars($item['quantity']); ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold;">Total Keseluruhan:</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold;">Rp <?= number_format($grandTotal, 0, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

    <form action="proses_checkout.php" method="post" style="text-align: right;">
        <input type="hidden" name="confirm_order" value="true">
        <button type="submit" style="padding: 12px 25px; background-color: #2d6a4f; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em;">Konfirmasi Pesanan & Bayar</button>
    </form>
    <div style="text-align: right; margin-top:10px;">
         <a href="keranjang.php" style="color: #555;">Kembali ke Keranjang</a>
    </div>
</div>

<?php include "app/views/footer.php"; ?>