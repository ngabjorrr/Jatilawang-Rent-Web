<?php
// File: Web Jatilawang/detail.php
// Initialization
session_start();
require_once "config.php";
require_once "functions.php";
require_once "app/controllers/ProductController.php";

// Input Validation
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$productId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($productId === false) {
    header("Location: index.php");
    exit;
}

// Data Fetching
$product = getProductById($productId);

if (!$product) {
    $pageTitle = "Produk Tidak Ditemukan";
    include "app/views/header.php";
    echo "<div class='page-container'><p>Produk yang Anda cari tidak ditemukan.</p><div class='back-link-container'><a href='index.php' class='back-link'>Kembali ke Daftar Produk</a></div></div>";
    include "app/views/footer.php";
    exit;
}

$pageTitle = htmlspecialchars($product['name']);

// Fetch rental prices if product is rentable
$rental_price_info = null;
if ($product['is_rentable'] == 1) {
    $sql_rental_query = "SELECT daily_price, weekly_price, monthly_price FROM rental_prices WHERE product_id = ?";
    $stmt_rental_prices = $conn->prepare($sql_rental_query);
    if ($stmt_rental_prices) {
        $stmt_rental_prices->bind_param("i", $product['id']);
        $stmt_rental_prices->execute();
        $rental_prices_result = $stmt_rental_prices->get_result();
        $rental_price_info = $rental_prices_result->fetch_assoc();
        $stmt_rental_prices->close();
    } else {
        // Handle error if needed, e.g., log error
        // die("Error preparing statement for rental prices: " . htmlspecialchars($conn->error));
    }
}


// Form Processing for Unified Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        $_SESSION['last_product_url'] = $_SERVER['REQUEST_URI'];
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Anda harus login untuk melakukan aksi ini.'];
        header("Location: login.php");
        exit;
    }

    $action_type = $_POST['action_type'] ?? '';
    $current_product_id = $product['id']; // ID produk dari halaman yang dilihat

    if (!isset($_SESSION['unified_cart'])) {
        $_SESSION['unified_cart'] = [];
    }

    if ($action_type === 'buy') {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($quantity < 1) $quantity = 1;

        // Panggil ulang getProductById untuk mendapatkan info stok terbaru (penting untuk konkurensi)
        $product_for_cart = getProductById($current_product_id);

        if (!$product_for_cart || !isset($product_for_cart['stock_quantity']) || $product_for_cart['stock_quantity'] < $quantity) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Stok tidak mencukupi untuk jumlah yang diminta. Stok tersedia: ' . ($product_for_cart['stock_quantity'] ?? 0)];
            header("Location: detail.php?id=" . $current_product_id);
            exit;
        }

        // Generate a unique key for the cart item
        $item_key = 'buy_' . $current_product_id;

        if (isset($_SESSION['unified_cart'][$item_key])) {
            $new_total_quantity = $_SESSION['unified_cart'][$item_key]['quantity'] + $quantity;
            if ($product_for_cart['stock_quantity'] < $new_total_quantity) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Stok tidak mencukupi untuk menambah jumlah di keranjang. Sisa stok: ' . ($product_for_cart['stock_quantity'] ?? 0)];
                header("Location: detail.php?id=" . $current_product_id);
                exit;
            }
            $_SESSION['unified_cart'][$item_key]['quantity'] = $new_total_quantity;
            $_SESSION['unified_cart'][$item_key]['subtotal'] = $_SESSION['unified_cart'][$item_key]['price'] * $_SESSION['unified_cart'][$item_key]['quantity'];
        } else {
            $_SESSION['unified_cart'][$item_key] = [
                'type' => 'buy',
                'product_id' => $current_product_id,
                'name' => $product_for_cart['name'],
                'price' => $product_for_cart['price'],
                'quantity' => $quantity,
                'image' => $product_for_cart['image'],
                'subtotal' => $product_for_cart['price'] * $quantity,
                'available_stock' => $product_for_cart['stock_quantity'] // Simpan info stok saat itu
            ];
        }
        $_SESSION['message'] = ['type' => 'success', 'text' => htmlspecialchars($product_for_cart['name']) . ' telah ditambahkan ke keranjang.'];
        header("Location: detail.php?id=" . $current_product_id);
        exit;
    } elseif ($action_type === 'add_to_rental_cart') {
        $rental_duration_type = $_POST['rental_duration_type'] ?? null;
        $rental_start_date_str = $_POST['rental_start_date'] ?? null;
        $quantity_rent = isset($_POST['quantity_rent']) ? (int)$_POST['quantity_rent'] : 1;

        if ($quantity_rent < 1) $quantity_rent = 1;

        if (!$product['is_rentable'] || !$rental_price_info) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Produk ini tidak dapat disewa atau informasi harga sewa tidak ditemukan.'];
            header("Location: detail.php?id=" . $current_product_id);
            exit;
        }
        if (empty($rental_duration_type) || empty($rental_start_date_str)) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Durasi sewa dan tanggal mulai wajib diisi.'];
            header("Location: detail.php?id=" . $current_product_id);
            exit;
        }
        // Validate start date is not in the past
        $today = new DateTime();
        $today->setTime(0, 0, 0); // Set time to midnight for day comparison
        $rental_start_date_obj = new DateTime($rental_start_date_str);
        if ($rental_start_date_obj < $today) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Tanggal mulai sewa tidak boleh tanggal yang sudah lalu.'];
            header("Location: detail.php?id=" . $current_product_id);
            exit;
        }


        $price_for_selected_duration = 0;
        switch ($rental_duration_type) {
            case 'daily':
                $price_for_selected_duration = $rental_price_info['daily_price'];
                break;
            case 'weekly':
                $price_for_selected_duration = $rental_price_info['weekly_price'];
                break;
            case 'monthly':
                $price_for_selected_duration = $rental_price_info['monthly_price'];
                break;
            default:
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Jenis durasi sewa tidak valid.'];
                header("Location: detail.php?id=" . $current_product_id);
                exit;
        }
        if ($price_for_selected_duration <= 0) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Harga untuk durasi sewa yang dipilih tidak valid.'];
            header("Location: detail.php?id=" . $current_product_id);
            exit;
        }

        // Generate a unique key for rental item, allowing same product with different rental params
        $item_key = 'rent_' . $current_product_id . '_' . strtotime($rental_start_date_str) . '_' . $rental_duration_type;

        // For simplicity, if the exact same rental configuration is added, we update quantity.
        // More complex logic could be needed if you want to treat each "add to cart" as a new line item regardless.
        if (isset($_SESSION['unified_cart'][$item_key])) {
            $_SESSION['unified_cart'][$item_key]['quantity'] += $quantity_rent;
            $_SESSION['unified_cart'][$item_key]['subtotal'] = $_SESSION['unified_cart'][$item_key]['price_per_item_per_duration'] * $_SESSION['unified_cart'][$item_key]['quantity'];
        } else {
            $_SESSION['unified_cart'][$item_key] = [
                'type' => 'rent',
                'product_id' => $current_product_id,
                'name' => $product['name'],
                'image' => $product['image'],
                'duration_type' => $rental_duration_type,
                'start_date' => $rental_start_date_str,
                'quantity' => $quantity_rent,
                'price_per_item_per_duration' => $price_for_selected_duration,
                'subtotal' => $price_for_selected_duration * $quantity_rent
            ];
        }

        $_SESSION['message'] = ['type' => 'success', 'text' => htmlspecialchars($product['name']) . ' telah ditambahkan ke keranjang sewa.'];
        header("Location: detail.php?id=" . $current_product_id);
        exit;
    }
}

include "app/views/header.php";
?>

<div class="page-container">
    <div class="product-detail-container">
        <div class="product-detail-image-container">
            <?php
            $imagePath = $product['image'];
            $imageName = $product['name'];
            $isUrl = filter_var($imagePath, FILTER_VALIDATE_URL);

            if ($isUrl) {
                echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($imageName) . '" style="width: 100%; height: auto; max-height: 400px; object-fit: contain; border-radius: 4px; border: 1px solid #eee;">';
            } elseif (!empty($imagePath) && file_exists($imagePath)) {
                echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($imageName) . '" style="width: 100%; height: auto; max-height: 400px; object-fit: contain; border-radius: 4px; border: 1px solid #eee;">';
            } else {
                echo '<div class="placeholder-wrapper">';
                echo '    <svg class="product-image-placeholder" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"></path></svg>';
                echo '</div>';
            }
            ?>
        </div>
        <div class="product-detail-info-container">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <div class="product-rating-detail">
                <span class="star">★</span>
                <?= isset($product['rating']) ? htmlspecialchars($product['rating']) : 'N/A' ?>
                <span style="color:#999; font-size:0.9em;"><?= isset($product['reviews']) ? ' (' . htmlspecialchars($product['reviews']) . ' ulasan)' : '' ?></span>
            </div>

            <p class="price">Rp <?= number_format($product['price'], 0, ',', '.') ?> <?php if ($product['is_rentable'] == 0 && $product['price'] > 0.00) {
                                                                                            echo "(Harga Beli)";
                                                                                        } ?></p>
            <p class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <?php
            // Menampilkan Stok di Halaman Detail
            $stock_detail_display = "Informasi stok tidak tersedia.";
            $can_be_bought = $product['price'] > 0; // Anggap bisa dibeli jika ada harga
            $stock_available_for_purchase = false;

            if (isset($product['stock_quantity'])) {
                if ($product['stock_quantity'] > 0) {
                    $stock_detail_display = "Ketersediaan: <span style='color:green;'>" . htmlspecialchars($product['stock_quantity']) . " unit</span>";
                    $stock_available_for_purchase = true;
                } else {
                    $stock_detail_display = "Ketersediaan: <span style='color:red;'>Stok Habis</span>";
                }
            } else if ($product['is_rentable'] == 1 && !$can_be_bought) {
                $stock_detail_display = "Produk ini hanya untuk disewakan.";
            }

            // Tampilkan info stok jika produk bisa dibeli atau memang ada data stoknya
            if ($can_be_bought || isset($product['stock_quantity'])) {
                echo "<p class='product-stock-detail' style='margin-bottom: 15px;'>" . $stock_detail_display . "</p>";
            }
            ?>


            <?php if (isset($_SESSION['message'])): ?>
                <div class="message <?= $_SESSION['message']['type'] === 'success' ? 'success' : ($_SESSION['message']['type'] === 'error' ? 'error' : 'info'); ?>"
                    style="padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ccc;
                background-color: <?= $_SESSION['message']['type'] === 'success' ? '#d4edda' : ($_SESSION['message']['type'] === 'error' ? '#f8d7da' : '#cfe2ff'); ?>;
                color: <?= $_SESSION['message']['type'] === 'success' ? '#155724' : ($_SESSION['message']['type'] === 'error' ? '#721c24' : '#084298'); ?>;">
                    <?= htmlspecialchars($_SESSION['message']['text']); ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <div class="product-actions" style="margin-top: 20px;">
                <?php
                // Logika untuk menampilkan tombol beli disederhanakan
                if ($can_be_bought && $stock_available_for_purchase):
                ?>
                    <form method="post" action="detail.php?id=<?= htmlspecialchars($product['id']) ?>" style="margin-bottom: 15px;">
                        <input type="hidden" name="action_type" value="buy">
                        <label for="quantity">Jumlah Beli:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?= htmlspecialchars($product['stock_quantity']); ?>" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 70px; margin-bottom: 10px; margin-right: 5px;">
                        <button type="submit" style="background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em;">Masukkan Keranjang</button>
                    </form>
                <?php elseif ($can_be_bought && !$stock_available_for_purchase): ?>
                    <p><em>Produk ini bisa dibeli, namun stok sedang habis.</em></p>
                <?php endif; ?>


                <?php if ($product['is_rentable'] == 1): ?>
                    <div class="rental-options" style="padding: 15px; border: 1px solid #007bff; border-radius: 5px; background-color: #f8f9fa; margin-top:15px;">
                        <h4>Opsi Sewa:</h4>
                        <?php if ($rental_price_info && ($rental_price_info['daily_price'] > 0 || $rental_price_info['weekly_price'] > 0 || $rental_price_info['monthly_price'] > 0)): ?>
                            <p>Harga Sewa Tersedia:</p>
                            <ul>
                                <?php if (!empty($rental_price_info['daily_price']) && $rental_price_info['daily_price'] > 0): ?>
                                    <li>Harian: Rp <?= number_format($rental_price_info['daily_price'], 0, ',', '.') ?></li>
                                <?php endif; ?>
                                <?php if (!empty($rental_price_info['weekly_price']) && $rental_price_info['weekly_price'] > 0): ?>
                                    <li>Mingguan: Rp <?= number_format($rental_price_info['weekly_price'], 0, ',', '.') ?></li>
                                <?php endif; ?>
                                <?php if (!empty($rental_price_info['monthly_price']) && $rental_price_info['monthly_price'] > 0): ?>
                                    <li>Bulanan: Rp <?= number_format($rental_price_info['monthly_price'], 0, ',', '.') ?></li>
                                <?php endif; ?>
                            </ul>

                            <form action="detail.php?id=<?= htmlspecialchars($product['id']); ?>" method="post" style="margin-top: 15px;">
                                <input type="hidden" name="action_type" value="add_to_rental_cart">

                                <div style="margin-bottom: 15px;">
                                    <label for="rental_duration_type">Pilih Durasi Sewa:</label><br>
                                    <select name="rental_duration_type" id="rental_duration_type" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; width:100%;">
                                        <option value="">--Pilih Durasi--</option>
                                        <?php if (!empty($rental_price_info['daily_price']) && $rental_price_info['daily_price'] > 0): ?>
                                            <option value="daily">Harian</option>
                                        <?php endif; ?>
                                        <?php if (!empty($rental_price_info['weekly_price']) && $rental_price_info['weekly_price'] > 0): ?>
                                            <option value="weekly">Mingguan</option>
                                        <?php endif; ?>
                                        <?php if (!empty($rental_price_info['monthly_price']) && $rental_price_info['monthly_price'] > 0): ?>
                                            <option value="monthly">Bulanan</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="rental_start_date">Tanggal Mulai Sewa:</label><br>
                                    <input type="date" name="rental_start_date" id="rental_start_date" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; width:calc(100% - 18px);" min="<?= date('Y-m-d') ?>">
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label for="quantity_rent">Jumlah Sewa:</label><br>
                                    <input type="number" id="quantity_rent" name="quantity_rent" value="1" min="1" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; width:70px;">
                                </div>

                                <button type="submit" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em;">Tambahkan ke Keranjang Sewa</button>
                            </form>
                        <?php else: ?>
                            <p>Informasi harga sewa belum tersedia untuk produk ini atau hanya bisa di beli.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="back-link-container">
        <a href="index.php" class="back-link">&laquo; Kembali ke Daftar Produk</a>
    </div>
</div>

<?php include "app/views/footer.php"; ?>