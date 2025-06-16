<?php
// File: Web Jatilawang/login.php
session_start();
if (isset($_SESSION['user'])) { // Jika pengguna sudah login, redirect ke index.php
    header("Location: index.php");
    exit;
}

$active_form = 'login'; // Signal to the template which form to show by default

// Include the new combined authentication view
// This replaces any previous includes of header, footer, or specific login form views.
include __DIR__ . "/app/views/auth_forms.php";
?>