<?php
// File: Web Jatilawang/keranjang.php
session_start();
require_once "config.php";
require_once "functions.php";

// Logika untuk menangani aksi di keranjang (hapus item - update via form dihilangkan sementara)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $itemKey = $_POST['item_key'] ?? null; 

    if ($itemKey && isset($_SESSION['unified_cart'][$itemKey])) {
        if ($_POST['action'] === 'remove') {
            unset($_SESSION['unified_cart'][$itemKey]);
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Item telah dihapus dari keranjang.'];
        } 
        // Logika 'update' via POST akan digantikan oleh AJAX
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
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>
    
    <div id="cart-feedback" style="padding: 10px; margin-bottom: 15px; border-radius: 4px; display:none;"></div>


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
                        $price_per_unit = ($item['type'] === 'buy') ? $item['price'] : $item['price_per_item_per_duration'];
                        $subtotal = $item['subtotal']; // Subtotal dari session
                        $grandTotal += $subtotal;
                    ?>
                    <tr id="item-row-<?= htmlspecialchars($key); ?>">
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
                            <input type="number" class="item-quantity"
                                   data-item-key="<?= htmlspecialchars($key); ?>"
                                   data-price-per-unit="<?= htmlspecialchars($price_per_unit); ?>"
                                   data-product-id="<?= htmlspecialchars($item['product_id']); ?>"
                                   value="<?= htmlspecialchars($item['quantity']); ?>" 
                                   min="1" <?php /* Pertimbangkan max stock di sini jika ada */ ?>
                                   style="width: 60px; padding: 5px; text-align: center;">
                            </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;" class="item-subtotal" id="subtotal-<?= htmlspecialchars($key); ?>">
                            Rp <?= number_format($subtotal, 0, ',', '.'); ?>
                        </td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <form action="keranjang.php" method="post" style="display: inline-block;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="item_key" value="<?= htmlspecialchars($key); ?>">
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus item ini dari keranjang?')" style="padding: 5px 10px; background-color: #e74c3c; color: white; border: none; cursor:pointer;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold;">Total Keseluruhan:</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold;" id="grand-total">
                        Rp <?= number_format($grandTotal, 0, ',', '.'); ?>
                    </td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.item-quantity');
    const cartFeedbackDiv = document.getElementById('cart-feedback');

    quantityInputs.forEach(input => {
        input.addEventListener('change', function() { // 'input' event untuk update lebih real-time, 'change' setelah fokus hilang
            const itemKey = this.dataset.itemKey;
            const pricePerUnit = parseFloat(this.dataset.pricePerUnit);
            const productId = this.dataset.productId;
            let quantity = parseInt(this.value);

            if (isNaN(quantity) || quantity < 1) {
                quantity = 1; // Reset ke 1 jika input tidak valid
                this.value = quantity;
            }

            // Update subtotal di tampilan
            const newSubtotal = pricePerUnit * quantity;
            document.getElementById('subtotal-' + itemKey).textContent = 'Rp ' + newSubtotal.toLocaleString('id-ID');

            updateGrandTotal();

            // Kirim update ke server via AJAX
            const formData = new FormData();
            formData.append('action', 'update_quantity');
            formData.append('item_key', itemKey);
            formData.append('quantity', quantity);
            formData.append('product_id', productId); // Untuk pengecekan stok

            fetch('ajax_update_cart.php', { // Anda perlu membuat file ini
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                cartFeedbackDiv.style.display = 'block';
                if (data.success) {
                    cartFeedbackDiv.textContent = data.message || 'Keranjang berhasil diperbarui.';
                    cartFeedbackDiv.style.backgroundColor = '#d4edda';
                    cartFeedbackDiv.style.color = '#155724';
                    // Jika server mengembalikan nilai stok yang disesuaikan
                    if (data.adjusted_quantity && data.adjusted_quantity !== quantity) {
                        this.value = data.adjusted_quantity;
                        const adjustedSubtotal = pricePerUnit * data.adjusted_quantity;
                        document.getElementById('subtotal-' + itemKey).textContent = 'Rp ' + adjustedSubtotal.toLocaleString('id-ID');
                        updateGrandTotal();
                    }
                     // Update Grand Total jika server mengirimkannya (opsional, bisa dihitung di client)
                    if (data.grand_total_formatted) {
                         document.getElementById('grand-total').textContent = data.grand_total_formatted;
                    }
                } else {
                    cartFeedbackDiv.textContent = data.message || 'Gagal memperbarui keranjang.';
                    cartFeedbackDiv.style.backgroundColor = '#f8d7da';
                    cartFeedbackDiv.style.color = '#721c24';
                    // Kembalikan ke kuantitas lama jika ada (misal, dari data.old_quantity dari server)
                    if(data.old_quantity) {
                        this.value = data.old_quantity;
                         const oldSubtotal = pricePerUnit * data.old_quantity;
                        document.getElementById('subtotal-' + itemKey).textContent = 'Rp ' + oldSubtotal.toLocaleString('id-ID');
                        updateGrandTotal();
                    }
                }
                 setTimeout(() => { cartFeedbackDiv.style.display = 'none'; }, 3000);
            })
            .catch(error => {
                console.error('Error:', error);
                cartFeedbackDiv.style.display = 'block';
                cartFeedbackDiv.textContent = 'Terjadi kesalahan koneksi.';
                cartFeedbackDiv.style.backgroundColor = '#f8d7da';
                cartFeedbackDiv.style.color = '#721c24';
                setTimeout(() => { cartFeedbackDiv.style.display = 'none'; }, 3000);
            });
        });
    });

    function updateGrandTotal() {
        let currentGrandTotal = 0;
        document.querySelectorAll('.item-subtotal').forEach(subtotalEl => {
            // Ekstrak angka dari string "Rp xxx.xxx"
            const subtotalValue = parseFloat(subtotalEl.textContent.replace(/[^0-9,-]+/g,"").replace(',','.'));
            if (!isNaN(subtotalValue)) {
                currentGrandTotal += subtotalValue;
            }
        });
        document.getElementById('grand-total').textContent = 'Rp ' + currentGrandTotal.toLocaleString('id-ID');
    }
});
</script>

<?php include "app/views/footer.php"; ?>