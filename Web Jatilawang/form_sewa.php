<?php
session_start();
require_once "config.php";
require_once "functions.php";
require_once "app/controllers/ProductController.php";

if (!isLoggedIn()) {
    $_SESSION['last_product_url'] = $_SERVER['REQUEST_URI'];
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Anda harus login untuk melanjutkan proses sewa.'];
    header("Location: login.php");
    exit;
}

if (!isset($_GET['product_id'])) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Produk tidak valid untuk disewa.'];
    header("Location: index.php");
    exit;
}

$productId = filter_var($_GET['product_id'], FILTER_VALIDATE_INT);
if (!$productId) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'ID produk tidak valid.'];
    header("Location: index.php");
    exit;
}

// Pastikan $conn sudah didefinisikan di config.php
if (!isset($conn) || !$conn) {
    die("Koneksi database tidak tersedia.");
}

$product = getProductById($productId);

if (!$product || !isset($product['is_rentable']) || !$product['is_rentable']) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Produk tidak ditemukan atau tidak tersedia untuk disewa.'];
    header("Location: index.php");
    exit;
}

// Ambil harga sewa dari database
$stmt_rental_prices = $conn->prepare("SELECT daily_price, weekly_price, monthly_price FROM rental_prices WHERE product_id = ?");
if ($stmt_rental_prices === false) {
    die("Query prepare gagal: " . $conn->error);
}
$stmt_rental_prices->bind_param("i", $productId);
$stmt_rental_prices->execute();
$rental_prices_result = $stmt_rental_prices->get_result();
$rental_price_info = $rental_prices_result->fetch_assoc();
$stmt_rental_prices->close();


$pageTitle = "Formulir Sewa: " . htmlspecialchars($product['name']);
include "app/views/header.php";
?>

<div class="page-container">
    <h2 class="main-section-title"><?= $pageTitle; ?></h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message <?= htmlspecialchars($_SESSION['message']['type']); ?>" style="padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ccc; background-color: <?= $_SESSION['message']['type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; color: <?= $_SESSION['message']['type'] === 'success' ? '#155724' : '#721c24'; ?>;">
            <?= htmlspecialchars($_SESSION['message']['text']); ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div style="display:flex; gap: 20px;">
        <div style="flex:1;">
            <?php
            $imagePath = $product['image'];
            $imageName = $product['name'];
            $isUrl = filter_var($imagePath, FILTER_VALIDATE_URL);

            if ($isUrl) {
                echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($imageName) . '" style="width: 100%; max-width:300px; height: auto; object-fit: cover; border-radius: 4px; border: 1px solid #eee; margin-bottom:15px;">';
            } elseif (!empty($imagePath) && file_exists($imagePath)) {
                echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($imageName) . '" style="width: 100%; max-width:300px; height: auto; object-fit: cover; border-radius: 4px; border: 1px solid #eee; margin-bottom:15px;">';
            } else {
                echo '<div style="width:100%; max-width:300px; height:300px; background-color:#f0f0f0; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">No Image</div>';
            }
            ?>
            <p><?= nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>

        <div style="flex:2;">
            <h3>Detail Sewa</h3>
            <form action="proses_keranjang_sewa.php" method="post"> <?php // Anda perlu membuat proses_keranjang_sewa.php ?>
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']); ?>">
                
                <?php if ($rental_price_info): ?>
                    <div style="margin-bottom: 15px;">
                        <label for="rental_duration_type">Pilih Durasi Sewa:</label><br>
                        <select name="rental_duration_type" id="rental_duration_type" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; width:100%;">
                            <option value="">--Pilih Durasi--</option>
                            <?php if (!empty($rental_price_info['daily_price']) && $rental_price_info['daily_price'] > 0): ?>
                                <option value="daily">Harian - Rp <?= number_format($rental_price_info['daily_price'], 0, ',', '.') ?></option>
                            <?php endif; ?>
                            <?php if (!empty($rental_price_info['weekly_price']) && $rental_price_info['weekly_price'] > 0): ?>
                                <option value="weekly">Mingguan - Rp <?= number_format($rental_price_info['weekly_price'], 0, ',', '.') ?></option>
                            <?php endif; ?>
                            <?php if (!empty($rental_price_info['monthly_price']) && $rental_price_info['monthly_price'] > 0): ?>
                                <option value="monthly">Bulanan - Rp <?= number_format($rental_price_info['monthly_price'], 0, ',', '.') ?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <p>Informasi harga sewa tidak tersedia untuk produk ini.</p>
                <?php endif; ?>

                <div style="margin-bottom: 15px;">
                    <label for="rental_start_date">Tanggal Mulai Sewa:</label><br>
                    <input type="date" name="rental_start_date" id="rental_start_date" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; width:calc(100% - 18px);">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label for="quantity_rent">Jumlah Sewa:</label><br>
                    <input type="number" id="quantity_rent" name="quantity" value="1" min="1" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; width:70px;">
                </div>

                <?php if ($rental_price_info): // Hanya tampilkan tombol jika ada harga sewa ?>
                <button type="submit" style="padding: 12px 25px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em;">Tambahkan ke Keranjang Sewa</button>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div style="margin-top:30px;">
         <a href="detail.php?id=<?= htmlspecialchars($product['id']); ?>" style="color: #555;">&laquo; Kembali ke Detail Produk</a>
    </div>
</div>

<?php include "app/views/footer.php"; ?>