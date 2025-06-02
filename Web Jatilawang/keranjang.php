<?php
session_start();
require_once "config.php"; // Untuk koneksi database jika diperlukan nanti
require_once "functions.php"; // Untuk fungsi isLoggedIn() atau lainnya

// Logika untuk menangani aksi di keranjang (hapus/update kuantitas)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        if ($_POST['action'] === 'remove' && $productId > 0) {
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Produk telah dihapus dari keranjang.'];
            }
        } elseif ($_POST['action'] === 'update' && $productId > 0) {
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
            if (isset($_SESSION['cart'][$productId])) {
                if ($quantity > 0) {
                    $_SESSION['cart'][$productId]['quantity'] = $quantity;
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Kuantitas produk telah diperbarui.'];
                } else {
                    // Jika kuantitas 0 atau kurang, hapus produk
                    unset($_SESSION['cart'][$productId]);
                    $_SESSION['message'] = ['type' => 'success', 'text' => 'Produk telah dihapus dari keranjang karena kuantitas nol.'];
                }
            }
        }
        // Redirect untuk mencegah resubmit form dan membersihkan POST request
        header("Location: keranjang.php");
        exit;
    }
}

$pageTitle = "Keranjang Belanja";
include "app/views/header.php";

$cartItems = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
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

    <?php if (!empty($cartItems)): ?>
        <table style="width:100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Produk</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">Harga</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Kuantitas</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Subtotal</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $id => $item): ?>
                    <?php
                        $subtotal = $item['price'] * $item['quantity'];
                        $grandTotal += $subtotal;
                    ?>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; display: flex; align-items: center;">
                            <?php if (!empty($item['image']) && file_exists($item['image'])): ?>
                                <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; background-color: #eee; margin-right: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.8em; color: #777;">NoImg</div>
                            <?php endif; ?>
                            <?= htmlspecialchars($item['name']); ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd;">Rp <?= number_format($item['price'], 0, ',', '.'); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <form action="keranjang.php" method="post" style="display: inline-block;">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?= $id; ?>">
                                <input type="number" name="quantity" value="<?= htmlspecialchars($item['quantity']); ?>" min="0" style="width: 60px; padding: 5px; text-align: center;">
                                <button type="submit" style="padding: 5px 10px; cursor:pointer;">Update</button>
                            </form>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <form action="keranjang.php" method="post" style="display: inline-block;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?= $id; ?>">
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus produk ini dari keranjang?')" style="padding: 5px 10px; background-color: #e74c3c; color: white; border: none; cursor:pointer;">Hapus</button>
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