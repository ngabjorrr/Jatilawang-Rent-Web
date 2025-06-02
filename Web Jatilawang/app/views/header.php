<?php
// session_start(); // Pastikan session_start() sudah ada di file PHP utama yang memanggil header ini
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Jatilawang Adventure <?php echo isset($pageTitle) ? "- " . htmlspecialchars($pageTitle) : ""; ?></title>
  <link rel="stylesheet" href="public/css/main_layout.css"> </head>
<body>
  <header class="main-header">
    <div class="header-content">
      <div class="header-left">
        <a href="index.php" class="site-logo">JATILAWANG</a>
        <nav>
          <ul>
            <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' || strpos($_SERVER['PHP_SELF'], 'detail.php') !== false) ? 'active' : ''; ?>">Produk</a></li>
            <li><a href="keranjang.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'keranjang.php' || basename($_SERVER['PHP_SELF']) == 'checkout.php') ? 'active' : ''; ?>">Keranjang
                <?php
                // Pastikan session sudah dimulai di file pemanggil (index.php, keranjang.php, dll)
                if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                    echo ' <span style="background-color: #e74c3c; color: white; padding: 2px 5px; border-radius: 50%; font-size: 0.8em;">' . count($_SESSION['cart']) . '</span>';
                }
                ?>
            </a></li>
            <?php if (isset($_SESSION['user'])): ?>
              <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
              <li><a href="login.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'login.php' || basename($_SERVER['PHP_SELF']) == 'register.php') ? 'active' : ''; ?>">Login</a></li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>
      <div class="search-bar">
        <svg xmlns="http://www.w3.org/2000/svg" height="18px" viewBox="0 0 24 24" width="18px"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
        <input type="text" placeholder="Cari Barang">
      </div>
    </div>
  </header>