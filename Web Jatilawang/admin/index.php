<?php
$pageTitle = "Dashboard";
include 'views/header.php'; // Menggunakan header admin
// ... setelah include 'views/header.php';

if (!isAdmin()) {
    // Jika bukan admin, alihkan ke halaman login atau tampilkan pesan error
    header("Location: ../login.php"); // Asumsi halaman login ada di root
    exit;
}

// Contoh query untuk statistik sederhana
$pending_buy_orders = $conn->query("SELECT COUNT(*) as count FROM product_orders WHERE status = 'pending'")->fetch_assoc()['count'];
$active_rentals = $conn->query("SELECT COUNT(*) as count FROM rental_orders WHERE status = 'active'")->fetch_assoc()['count'];
?>

<h1>Selamat Datang, Admin!</h1>
<p>Ini adalah halaman utama panel admin. Dari sini Anda bisa mengelola website.</p>

<div style="display:flex; gap: 20px; margin-top: 30px;">
    <div style="border: 1px solid #ccc; padding: 20px; flex: 1;">
        <h3>Pesanan Pembelian Pending</h3>
        <p style="font-size: 2em; font-weight: bold;"><?php echo $pending_buy_orders; ?></p>
    </div>
    <div style="border: 1px solid #ccc; padding: 20px; flex: 1;">
        <h3>Penyewaan Aktif</h3>
        <p style="font-size: 2em; font-weight: bold;"><?php echo $active_rentals; ?></p>
    </div>
</div>

<?php
include 'views/footer.php'; // Menggunakan footer admin
?>