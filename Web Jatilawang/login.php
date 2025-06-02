<?php
// Initialization
session_start();
if (isset($_SESSION['user'])) { // Jika pengguna sudah login, redirect ke index.php
    header("Location: index.php");
    exit;
}

// View Rendering
// File app/views/register.php (yang Anda gunakan) sudah berisi halaman HTML login yang lengkap.
// Jadi, kita hanya perlu meng-include itu.
include "app/views/register.php"; //
?>