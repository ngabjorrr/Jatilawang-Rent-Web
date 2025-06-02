<?php
// File: Web Jatilawang/keranjang.php
session_start();
require_once "config.php";
require_once "functions.php";

// Logika untuk menangani aksi di keranjang (hapus/update kuantitas)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $itemKey = $_POST['item_key'] ?? null; // Menggunakan item_key unik

    if ($itemKey && isset($_SESSION['unified_cart'][$itemKey])) {
        if ($_POST['action'] === 'remove') {
            unset($_SESSION['unified_cart'][$itemKey]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Item telah dihapus dari keranjang.'];
        } elseif ($_POST['action'] === 'update') {
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
            $itemType = $_SESSION['unified_cart'][$itemKey]['type'];

            if ($quantity > 0) {
                $_SESSION['unified_cart'][$itemKey]['quantity'] = $quantity;
                // Recalculate subtotal
                if ($itemType === 'buy') {
                    $_SESSION['unified_cart'][$itemKey]['subtotal'] = $_SESSION['unified_cart'][$itemKey]['price'] * $quantity;
                } elseif ($itemType === 'rent') {
                    $_SESSION['unified_cart'][$itemKey]['subtotal'] = $_SESSION['unified_cart'][$itemKey]['price_per_item_per_duration'] * $quantity;
                }
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Kuantitas item telah diperbarui.'];
            } else {
                unset($_SESSION['unified_cart'][$itemKey]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Item telah dihapus karena kuantitas nol.'];
            }
        }
    }
    header("Location: keranjang.php");
    exit;
}

$pageTitle = "Keranjang Belanja Terpadu";
include "app/views/header.php";

$unifiedCartItems = isset($_SESSION['unified_cart']) ? $_SESSION['unified_cart'] : [];
$grandTotal = 0;
?>

<div class="page-container">
    <h2 class="main-section-title"><?php echo $pageTitle; ?></h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?= htmlspecialchars($_SESSION['message']['type']); ?>" style="padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ccc; background-color: <?= $_SESSION['message']['type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?= $_SESSION['message']['type'] === 'success' ? '#155724' : '#721c24'; ?>;">
            <?= htmlspecialchars($_SESSION['message']['text']); ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (!empty($unifiedCartItems)): ?>
        <table style="width:100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Produk</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Detail</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Kuantitas</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Subtotal</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($unifiedCartItems as $key => $item): ?>
                    <?php
                        // Subtotal sudah dihitung saat item ditambahkan/diupdate
                        $subtotal = $item['subtotal'];
                        $grandTotal += $subtotal;
                    ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; display: flex; align-items: center;">
                            <?php if (!empty($item['image']) && (filter_var($item['image'], FILTER_VALIDATE_URL) || file_exists($item['image']))): ?>
                                <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background-color: #eee; margin-right: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.8em; color: #777;">NoImg</div>
                            <?php endif; ?>
                            <?= htmlspecialchars($item['name']); ?>
                            <br><small>(Tipe: <?= htmlspecialchars(ucfirst($item['type'])); ?>)</small>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">
                            <?php if ($item['type'] === 'buy'): ?>
                                Harga: Rp <?= number_format($item['price'], 0, ',', '.'); ?>
                            <?php elseif ($item['type'] === 'rent'): ?>
                                Durasi: <?= htmlspecialchars(ucfirst($item['duration_type'])); ?><br>
                                Mulai: <?= htmlspecialchars(date('d M Y', strtotime($item['start_date']))); ?><br>
                                Harga Sewa: Rp <?= number_format($item['price_per_item_per_duration'], 0, ',', '.'); ?> / unit / durasi
                            <?php endif; ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <form action="keranjang.php" method="post" style="display: inline-block;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="item_key" value="<?= $key; ?>">
                                <input type="number" name="quantity" value="<?= htmlspecialchars($item['quantity']); ?>" min="0" style="width: 60px; padding: 5px; text-align: center;">
                                <button type="submit" style="padding: 5px 10px; cursor:pointer;">Update</button>
                            </form>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <form action="keranjang.php" method="post" style="display: inline-block;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="item_key" value="<?= $key; ?>">
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus item ini dari keranjang?')" style="padding: 5px 10px; background-color: #e74c3c; color: white; border: none; cursor:pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold;">Total Keseluruhan:</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold;">Rp <?= number_format($grandTotal, 0, ',', '.'); ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"></td>
                </tr>
            </tfoot>
        </table>
        <div style="text-align: right;">
            <a href="index.php" style="padding: 10px 15px; background-color: #777; color: white; text-decoration: none; border-radius: 4px; margin-right:10px;">Lanjut Belanja</a>
            <a href="checkout.php" style="padding: 10px 15px; background-color: #2d6a4f; color: white; text-decoration: none; border-radius: 4px;">Lanjut ke Checkout</a>
        </div>
    <?php else: ?>
        <p>Keranjang belanja Anda masih kosong.</p>
        <a href="index.php" style="padding: 10px 15px; background-color: #2d6a4f; color: white; text-decoration: none; border-radius: 4px;">Mulai Belanja</a>
    <?php endif; ?>
</div>

<?php include "app/views/footer.php"; ?>