<?php
// File: Web Jatilawang/app/views/header.php
// Pastikan session_start() sudah ada di file PHP utama yang memanggil header ini
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jatilawang Adventure <?php echo isset($pageTitle) ? "- " . htmlspecialchars($pageTitle) : ""; ?></title>
  <link rel="stylesheet" href="public/css/main_layout.css">
  <style>
    .site-logo img {
      max-height: 40px; 
      width: auto;
      vertical-align: middle; 
    }
    /* Style tambahan untuk highlight navigasi yang lebih menonjol */
    header.main-header nav ul li a.active {
      font-weight: bold; /* Membuat teks tebal */
      /* Anda juga bisa menambahkan properti lain, contoh: */
      /* border-bottom: 2px solid #2d6a4f; */
      /* background-color: #e0f2e9; */ /* Warna latar belakang lembut */
      /* padding-bottom: 3px; */
    }
  </style>
</head>
<body>
  <header class="main-header">
    <div class="header-content">
      <div class="header-left">
        <a href="index.php" class="site-logo">
          <img src="public/assets/Logo.png" alt="Jatilawang Adventure Logo">
        </a>
        <nav>
          <ul>
            <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' || strpos($_SERVER['PHP_SELF'], 'detail.php') !== false) ? 'active' : ''; ?>">Produk</a></li>
            <li><a href="keranjang.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'keranjang.php' || basename($_SERVER['PHP_SELF']) == 'checkout.php') ? 'active' : ''; ?>">Keranjang
                <?php
                if (isset($_SESSION['unified_cart']) && count($_SESSION['unified_cart']) > 0) {
                    echo ' <span style="background-color: #e74c3c; color: white; padding: 2px 5px; border-radius: 50%; font-size: 0.8em;">' . count($_SESSION['unified_cart']) . '</span>';
                }
                ?>
            </a></li>
            <?php if (isset($_SESSION['user'])): ?>
              <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
              <li><a href="login.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'login.php' || basename($_SERVER['PHP_SELF']) == 'register_page.php') ? 'active' : ''; ?>">Login</a></li> <?php // Koreksi di sini: 'register.php' menjadi 'register_page.php' ?>
            <?php endif; ?>
          </ul>
        </nav>
      </div>
      <div class="search-bar">
        <form method="GET" action="index.php" style="display: flex; align-items: center;">
          <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px">
            <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
          </svg>
          <input type="text" name="search" placeholder="Cari Barang" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </form>
      </div>
    </div>
  </header>