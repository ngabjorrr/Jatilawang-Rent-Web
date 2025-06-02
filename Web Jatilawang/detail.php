<?php
// Initialization
session_start();
require_once "config.php"; //
require_once "functions.php"; //
require_once "app/controllers/ProductController.php"; //

// Input Validation
if (!isset($_GET['id'])) { 
    header("Location: index.php"); 
    exit; 
}
$productId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($productId === false) {
    // Atau tampilkan halaman error
    header("Location: index.php"); 
    exit;
}

// Data Fetching
$product = getProductById($productId); //

if (!$product) { 
    $pageTitle = "Produk Tidak Ditemukan";
    include "app/views/header.php"; //
    echo "<div class='page-container'><p>Produk yang Anda cari tidak ditemukan.</p><div class='back-link-container'><a href='index.php' class='back-link'>Kembali ke Daftar Produk</a></div></div>";
    include "app/views/footer.php"; //
    exit; 
}

$pageTitle = htmlspecialchars($product['name']); //

// Form Processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) { //
        $_SESSION['last_product_url'] = $_SERVER['REQUEST_URI']; // Simpan URL untuk redirect setelah login
        header("Location: login.php");
        exit;
    } else {
        // Logika untuk menambahkan ke keranjang (contoh)
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($quantity < 1) $quantity = 1;

        // Simpan ke session keranjang (ini adalah contoh sederhana)
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$product['id']])) {
            $_SESSION['cart'][$product['id']]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product['id']] = [
                'name' => $product['name'], //
                'price' => $product['price'], //
                'image' => $product['image'], //
                'quantity' => $quantity
            ];
        }
        $_SESSION['message'] = ['type' => 'success', 'text' => htmlspecialchars($product['name']) . ' telah ditambahkan ke keranjang.'];
        header("Location: detail.php?id=" . $product['id']); // Redirect untuk mencegah resubmit form
        exit;
    }
}

// View Rendering
include "app/views/header.php"; //
?>

<div class="page-container">
    <div class="product-detail-container">
        <div class="product-detail-image-container">
            <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
                <div class="placeholder-wrapper">
                    <svg class="product-image-placeholder" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"></path></svg>
                </div>
            <?php endif; ?>
        </div>
        <div class="product-detail-info-container">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <div class="product-rating-detail">
                <span class="star">★</span>
                <?= isset($product['rating']) ? htmlspecialchars($product['rating']) : 'N/A' ?>
                <span style="color:#999; font-size:0.9em;"><?= isset($product['reviews']) ? ' ('.htmlspecialchars($product['reviews']).' ulasan)' : '' ?></span>
            </div>
            <p class="price">Rp <?= number_format($product['price'], 0, ',', '.') ?></p>
            <p class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <?php if(isset($_SESSION['message'])): ?>
                <div class="message <?= $_SESSION['message']['type'] === 'success' ? 'success' : 'error'; ?>">
                    <?= htmlspecialchars($_SESSION['message']['text']); ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <form method="post" action="detail.php?id=<?= htmlspecialchars($product['id']) ?>">
              <label for="quantity">Jumlah:</label>
              <input type="number" id="quantity" name="quantity" value="1" min="1"> <br>
              <button type="submit">Masukkan Keranjang</button>
            </form>
        </div>
    </div>
    <div class="back-link-container">
        <a href="index.php" class="back-link">&laquo; Kembali ke Daftar Produk</a>
    </div>
</div>

<?php include "app/views/footer.php"; // ?>