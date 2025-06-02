<?php
// File: register_page.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Jika pengguna sudah login, arahkan ke index.php
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/app/controllers/RegisterController.php';

$controller = new RegisterController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
    $controller->register();
} else {
    // Untuk menampilkan form, kita akan include header dan footer di sini
    // Ini adalah cara sederhana, sistem routing yang lebih baik akan menangani ini

    // Tidak perlu include header.php dan footer.php di sini
    // karena register_form.php sudah menjadi halaman HTML penuh.
    $controller->showForm();
}
?>