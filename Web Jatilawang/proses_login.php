<?php
session_start();
require_once "config.php";

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user'] = $user['email'];
    $_SESSION['user_role'] = $user['role']; 

    if ($user['role'] === 'admin') {
        header("Location: admin/index.php");
        exit;
    } else {
        $redirect = $_SESSION['last_product_url'] ?? 'index.php';
        unset($_SESSION['last_product_url']);
        header("Location: $redirect");
        exit;
    }

} else {
    echo "Login gagal. <a href='login.php'>Coba lagi</a>";
}
?>