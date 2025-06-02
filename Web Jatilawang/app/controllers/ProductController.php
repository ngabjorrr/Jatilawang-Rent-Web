<!-- app/controllers/ProductController.php -->
<?php
require_once "config.php";

// Function: getAllProducts
function getAllProducts() {
    global $conn;
    $result = $conn->query("SELECT * FROM products");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function: getProductById
function getProductById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>