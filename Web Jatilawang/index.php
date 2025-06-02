<?php
// Initialization
session_start();
require_once "config.php"; //
require_once "functions.php"; //
require_once "app/controllers/ProductController.php"; //

// Data Fetching
$products = getAllProducts(); //

$pageTitle = "Daftar Produk"; // Variabel untuk judul halaman dinamis
// View Rendering
include "app/views/header.php"; //
?>

<div class="page-container">
    <div class="filters-section">
      <div class="filter-item">
        <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 0 24 24" width="16px"><path d="M0 0h24v24H0z" fill="none"/><path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/></svg>
        Filter
      </div>
      <div class="filter-item">
        <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 0 24 24" width="16px"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z"/></svg>
        Kategori
      </div>
      <div class="filter-item">
        <svg xmlns="http://www.w3.org/2000/svg" height="16px" viewBox="0 0 24 24" width="16px"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 18h6v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
        Urut Berdasarkan
      </div>
    </div>

    <div class="content-section">
      <h2 class="main-section-title">Produk Kami</h2>

      <?php
        // Mengelompokkan produk berdasarkan kategori (jika ada field 'category' di $products)
        $categories = [];
        if (!empty($products)) {
            foreach ($products as $p) {
                $categories[$p['category']][] = $p; //
            }
        }
      ?>

      <?php if (!empty($categories)): ?>
        <?php foreach ($categories as $categoryName => $categoryProducts): ?>
          <h3 class="category-title"><?= htmlspecialchars($categoryName ?: "Lain-lain") ?></h3>
          <div class="product-grid">
            <?php foreach ($categoryProducts as $p): ?>
              <a href="detail.php?id=<?= htmlspecialchars($p['id']) ?>" class="product-card">
                <div class="product-image-wrapper">
                    <?php if (!empty($p['image']) && file_exists($p['image'])): ?>
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="product-actual-image">
                    <?php else: ?>
                        <svg class="product-image-placeholder" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"></path></svg>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                    <div class="product-rating">
                        <span class="star">★</span>
                        <?= isset($p['rating']) ? htmlspecialchars($p['rating']) : 'N/A' ?> <span style="color:#999; font-size:0.8em;"><?= isset($p['reviews']) ? ' ('.htmlspecialchars($p['reviews']).' ulasan)' : '' ?></span> </div>
                    <div class="product-price">
                        Rp <?= number_format($p['price'], 0, ',', '.') ?> </div>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
          <p>Tidak ada produk yang tersedia saat ini.</p>
      <?php endif; ?>
    </div>
</div>

<?php include "app/views/footer.php"; // ?>