<!-- config.php -->
<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "jatilawang_db");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>