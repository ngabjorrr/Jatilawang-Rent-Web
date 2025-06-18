<?php
// File: app/controllers/ProductController.php
require_once __DIR__ . "/../../config.php"; // Pastikan path ke config.php benar

// Function: getAllProductsFiltered (sudah ada di index.php, kita modifikasi di sini untuk referensi)
// Jika Anda memanggil fungsi dari index.php, pastikan JOIN ini juga ada di sana atau panggil fungsi ini dari index.php
/*
// Contoh jika getAllProductsFiltered ada di ProductController.php
function getAllProductsFiltered($category = null, $sort = null, $search = null) {
    global $conn;

    Query dasar untuk produk dengan LEFT JOIN ke product_stock
    $query = "SELECT p.*, ps.stock_quantity 
              FROM products p 
              LEFT JOIN product_stock ps ON p.id = ps.product_id";

    $params = [];
    $types = [];
    $conditions = [];

    if ($category) {
        $conditions[] = "p.category = ?";
        $params[] = $category;
        $types[] = "s";
    }

    if ($search) {
        $conditions[] = "p.name LIKE ?";
        $params[] = "%{$search}%";
        $types[] = "s";
    }

    // Filter tambahan: Hanya tampilkan produk yang bisa dibeli atau disewa
    // $conditions[] = "(p.price > 0 OR p.is_rentable = 1)";


    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Sorting
    switch ($sort) {
        case 'name_asc':
            $query .= " ORDER BY p.name ASC";
            break;
        // ... (kasus sorting lainnya) ...
        default:
            // $query .= " ORDER BY p.id DESC"; // Default sort
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        // Handle error, misalnya: error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        return [];
    }
    if (!empty($params)) {
        $stmt->bind_param(implode("", $types), ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
*/


// Function: getProductById
function getProductById($id) {
    global $conn; // Pastikan $conn adalah objek koneksi yang valid dari config.php
    $stmt = $conn->prepare("SELECT p.*, ps.stock_quantity 
                            FROM products p 
                            LEFT JOIN product_stock ps ON p.id = ps.product_id 
                            WHERE p.id = ?");
    if (!$stmt) {
        // Handle error, misalnya: error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        return null;
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function: getAllProducts (Jika masih digunakan, juga perlu diupdate)
function getAllProducts() {
    global $conn;
    $result = $conn->query("SELECT p.*, ps.stock_quantity 
                            FROM products p 
                            LEFT JOIN product_stock ps ON p.id = ps.product_id");
    if (!$result) {
        // Handle error
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

?>