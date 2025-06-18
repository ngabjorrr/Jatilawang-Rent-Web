<?php
$passwordYangInginDiHash = 'admin123'; // Ini adalah password yang ingin Anda gunakan untuk admin@jatilawang.com

$hashHasilnya = password_hash($passwordYangInginDiHash, PASSWORD_DEFAULT);

echo "Password Asli: " . htmlspecialchars($passwordYangInginDiHash) . "<br>";
echo "Hash untuk Disimpan di Database: " . htmlspecialchars($hashHasilnya);
?>