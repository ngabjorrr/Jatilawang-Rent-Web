<!-- login.php -->
<?php
// Initialization
session_start();
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// View Rendering
include "app/views/header.php";
?>
<h2>Login</h2>
<form action="proses_login.php" method="post">
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Login</button>
</form>
<p>Belum punya akun? <a href="register.php">Daftar</a></p>
<?php include "app/views/footer.php"; ?>