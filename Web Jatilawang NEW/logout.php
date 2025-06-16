<!-- logout.php -->
<?php
// Logout Logic
session_start();
session_destroy();
header('Location: index.php');
exit;
?>