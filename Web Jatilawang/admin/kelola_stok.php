<?php
$pageTitle = "Kelola Stok";
include 'views/header.php';

// Proteksi halaman (pastikan ini sudah ada)
if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Ambil semua produk yang memiliki entri stok
$products_with_stock = $conn->query(
    "SELECT p.id, p.name, ps.stock_quantity
     FROM products p
     JOIN product_stock ps ON p.id = ps.product_id
     ORDER BY p.name ASC"
)->fetch_all(MYSQLI_ASSOC);
?>

<h2>Manajemen Stok Produk</h2>
<p>Perbarui jumlah stok untuk setiap produk di bawah ini.</p>

<?php if(isset($_SESSION['message'])): ?>
    <div class="message <?= $_SESSION['message']['type']; ?>" style="padding: 10px; margin-bottom: 15px; border-radius: 5px; color: #155724; background-color: #d4edda;">
        <?= $_SESSION['message']['text']; ?>
    </div>
<?php unset($_SESSION['message']); endif; ?>

<table>
    <thead>
        <tr>
            <th>Nama Produk</th>
            <th>Stok Saat Ini</th>
            <th style="width: 200px;">Update Stok</th>
            <th style="width: 100px;">Aksi</th> </tr>
    </thead>
    <tbody>
        <?php foreach ($products_with_stock as $product): ?>
        <tr>
            <td><?= htmlspecialchars($product['name']); ?></td>
            <td><?= htmlspecialchars($product['stock_quantity']); ?></td>
            <td>
                <form action="process_updates.php" method="POST" style="display:flex; gap:5px;">
                    <input type="hidden" name="action" value="update_stock">
                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                    <input type="number" name="new_stock" value="<?= $product['stock_quantity']; ?>" min="0" style="width: 80px; padding: 5px;">
                    <button type="submit" style="padding: 5px 10px;">Update</button>
                </form>
            </td>
            <td>
                <form action="process_updates.php" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus produk ini secara permanen? Aksi ini tidak dapat dibatalkan.');">
                    <input type="hidden" name="action" value="delete_product">
                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                    <button type="submit" style="padding: 5px 10px; background-color: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer;">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'views/footer.php'; ?>