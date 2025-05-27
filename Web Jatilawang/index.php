<!-- index.php -->
<?php
// Initialization
session_start();
require_once "config.php";
require_once "functions.php";
require_once "app/controllers/ProductController.php";

// Data Fetching
$products = getAllProducts();

// View Rendering
include "app/views/header.php";
?>
<h2>Alat Sewa</h2>
<div class="product-list">
<?php foreach ($products as $p): ?>
  <div class="product-card">
    <img src="<?= $p['image'] ?>" alt="<?= $p['name'] ?>" width="150" />
    <h3><?= $p['name'] ?></h3>
    <p><?= $p['description'] ?></p>
    <p><strong>Rp <?= number_format($p['price'], 2) ?></strong></p>
    <a href="detail.php?id=<?= $p['id'] ?>">Lihat Detail</a>
  </div>
<?php endforeach; ?>
</div>
<?php include "app/views/footer.php"; ?>