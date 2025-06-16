<!-- proses_login.php -->
<?php
// Initialization
session_start();
require_once "config.php";

// Form Processing
$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Redirection
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user['email'];
    $redirect = $_SESSION['last_product'] ?? 'index.php';
    unset($_SESSION['last_product']);
    header("Location: $redirect");
    exit;
} else {
    echo "Login gagal. <a href='login.php'>Coba lagi</a>";
}
?>