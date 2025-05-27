<!-- detail.php -->
<?php
// Initialization
session_start();
require_once "config.php";
require_once "functions.php";
require_once "app/controllers/ProductController.php";

// Input Validation
if (!isset($_GET['id'])) { 
    header("Location: index.php"); 
    exit; 
}

// Data Fetching
$product = getProductById($_GET['id']);
if (!$product) { 
    echo "Produk tidak ditemukan."; 
    exit; 
}

// Form Processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) {
        $_SESSION['last_product'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit;
    } else {
        // Placeholder for adding to cart
        echo "<script>alert('Produk ditambahkan ke keranjang!');</script>";
    }
}

// View Rendering
include "app/views/header.php";
?>
<h2><?= $product['name'] ?></h2>
<img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" width="300">
<p><?= $product['description'] ?></p>
<p><strong>Harga: Rp <?= number_format($product['price'], 2) ?></strong></p>
<form method="post">
  <button type="submit">Masukkan Keranjang</button>
</form>
<a href="index.php">Kembali</a>
<?php include "app/views/footer.php"; ?>