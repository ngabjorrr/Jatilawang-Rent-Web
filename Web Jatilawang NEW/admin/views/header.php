<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../../functions.php';
require_once __DIR__ . '/../../config.php';

// Proteksi Halaman: Hanya admin yang boleh mengakses
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Mengatur halaman aktif
$activePage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel <?php echo isset($pageTitle) ? "- " . $pageTitle : ""; ?></title>
    <link rel="stylesheet" href="../public/css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="page-wrapper">
        <nav class="sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="logo">Jatilawang</a>
            </div>
            <ul class="sidebar-nav">
                <li class="<?php echo ($activePage == 'index.php') ? 'active' : ''; ?>">
                    <a href="index.php"><i class="fa-solid fa-table-columns icon"></i> Dashboard</a>
                </li>
                <li class="<?php echo ($activePage == 'kelola_stok.php') ? 'active' : ''; ?>">
                    <a href="kelola_stok.php"><i class="fa-solid fa-box-archive icon"></i> Kelola Stok</a>
                </li>
                <li class="<?php echo ($activePage == 'kelola_pesanan.php') ? 'active' : ''; ?>">
                    <a href="kelola_pesanan.php"><i class="fa-solid fa-cart-shopping icon"></i> Kelola Pesanan</a>
                </li>
                <li>
                    <a href="../logout.php"><i class="fa-solid fa-right-from-bracket icon"></i> Logout</a>
                </li>
            </ul>
        </nav>

        <div class="main-content">
            <header class="top-navbar">
                <div class="search-box">
                    <i class="fa-solid fa-search icon"></i>
                    <input type="text" placeholder="Search now">
                </div>
                <div class="navbar-right">
                    <div class="user-profile">
                        <span><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin'; ?></span>
                    </div>
                </div>
            </header>

            <main class="page-content">