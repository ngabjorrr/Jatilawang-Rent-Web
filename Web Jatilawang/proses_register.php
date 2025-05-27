<!-- proses_register.php -->
<?php
// Initialization
require_once "config.php";

// Form Processing
$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $password);
$stmt->execute();

// Redirection
header("Location: login.php");
exit;
?>