<!-- login.php -->
<?php
// Initialization
session_start();
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// View Rendering
include "app/views/register.php";
?>
<?php include "app/views/footer.php"; ?>