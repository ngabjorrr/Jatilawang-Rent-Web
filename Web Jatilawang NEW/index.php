<?php
// Initialization
session_start();
require_once "config.php";
require_once "functions.php";
require_once "app/controllers/ProductController.php";

// Fungsi: Ambil semua kategori unik
function getAllCategories()
{
    global $conn;
    $result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> ''");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi: Ambil produk dengan filter kategori dan sort
function getAllProductsFiltered($category = null, $sort = null, $search = null)
{
    global $conn;

    $query = "SELECT * FROM products";
    $params = [];
    $types = [];
    $conditions = [];

    if ($category) {
        $conditions[] = "category = ?";
        $params[] = $category;
        $types[] = "s";
    }

    if ($search) {
        $conditions[] = "name LIKE ?";
        $params[] = "%{$search}%";
        $types[] = "s";
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    // Sorting
    switch ($sort) {
        case 'name_asc':
            $query .= " ORDER BY name ASC";
            break;
        case 'name_desc':
            $query .= " ORDER BY name DESC";
            break;
        case 'price_asc':
            $query .= " ORDER BY price ASC";
            break;
        case 'price_desc':
            $query .= " ORDER BY price DESC";
            break;
        case 'rating_desc':
            $query .= " ORDER BY rating DESC";
            break;
        default:
            // tidak ada urutan
    }

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param(implode("", $types), ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Ambil parameter dari URL
$selectedCategory = $_GET['category'] ?? null;
$selectedSort = $_GET['sort'] ?? null;
$searchKeyword = $_GET['search'] ?? null;


// Ambil data produk dan kategori
$products = getAllProductsFiltered($selectedCategory, $selectedSort);
$allCategories = getAllCategories();
$products = getAllProductsFiltered($selectedCategory, $selectedSort, $searchKeyword);


$pageTitle = "Daftar Produk";
include "app/views/header.php";
?>

<div class="page-container">
    <div class="filters-section">

        <!-- Dropdown Kategori -->
        <div class="filter-item">
            <form method="GET">
                <label for="category">Kategori:</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($allCategories as $cat): ?>
                        <?php $catName = $cat['category']; ?>
                        <option value="<?= htmlspecialchars($catName) ?>" <?= ($selectedCategory == $catName) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($catName) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="sort" value="<?= htmlspecialchars($selectedSort) ?>">
            </form>
        </div>

        <!-- Dropdown Urut Berdasarkan -->
        <div class="filter-item">
            <form method="GET">
                <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                <label for="sort">Urut Berdasarkan:</label>
                <select name="sort" id="sort" onchange="this.form.submit()">
                    <option value="">Default</option>
                    <option value="name_asc" <?= ($selectedSort == 'name_asc') ? 'selected' : '' ?>>Nama A-Z</option>
                    <option value="name_desc" <?= ($selectedSort == 'name_desc') ? 'selected' : '' ?>>Nama Z-A</option>
                    <option value="price_asc" <?= ($selectedSort == 'price_asc') ? 'selected' : '' ?>>Harga Termurah</option>
                    <option value="price_desc" <?= ($selectedSort == 'price_desc') ? 'selected' : '' ?>>Harga Termahal</option>
                    <option value="rating_desc" <?= ($selectedSort == 'rating_desc') ? 'selected' : '' ?>>Rating Tertinggi</option>
                </select>
            </form>
        </div>
    </div>

    <div class="content-section">
        <h2 class="main-section-title">Produk Kami</h2>

        <?php
        // Mengelompokkan produk berdasarkan kategori
        $categories = [];
        if (!empty($products)) {
            foreach ($products as $p) {
                $categories[$p['category']][] = $p;
            }
        }
        ?>

        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $categoryName => $categoryProducts): ?>
                <h3 class="category-title"><?= htmlspecialchars($categoryName ?: "Lain-lain") ?></h3>
                <div class="product-grid">
                    <?php foreach ($categoryProducts as $p): ?>
                        <a href="detail.php?id=<?= htmlspecialchars($p['id']) ?>" class="product-card">
                            <div class="product-image-wrapper">
                                <?php
                                $imagePath = $p['image']; // Path gambar dari produk saat ini dalam loop
                                $imageName = $p['name'];  // Nama produk untuk alt text
                                $isUrl = filter_var($imagePath, FILTER_VALIDATE_URL); // Cek apakah path adalah URL

                                if ($isUrl) { // Jika ini URL
                                    echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($imageName) . '" class="product-actual-image">';
                                } elseif (!empty($imagePath) && file_exists($imagePath)) { // Jika ini path lokal dan file ada
                                    echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($imageName) . '" class="product-actual-image">';
                                } else { // Jika tidak ada gambar atau path lokal tidak valid
                                    echo '<svg class="product-image-placeholder" viewBox="0 0 24 24">';
                                    echo '    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"></path>';
                                    echo '</svg>';
                                }
                                ?>
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($p['name']) ?></h3>
                                <div class="product-rating">
                                    <span class="star">★</span>
                                    <?= isset($p['rating']) ? htmlspecialchars($p['rating']) : 'N/A' ?>
                                    <span style="color:#999; font-size:0.8em;">
                                        <?= isset($p['reviews']) ? ' (' . htmlspecialchars($p['reviews']) . ' ulasan)' : '' ?>
                                    </span>
                                </div>
                                <div class="product-price">
                                    Rp <?= number_format($p['price'], 0, ',', '.') ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Tidak ada produk yang tersedia saat ini.</p>
        <?php endif; ?>
    </div>
</div>
<?php include "app/views/footer.php";
?>