<?php
$pageTitle = "Kelola Pesanan";
include 'views/header.php';
// ... setelah include 'views/header.php';

if (!isAdmin()) {
    // Jika bukan admin, alihkan ke halaman login atau tampilkan pesan error
    header("Location: ../login.php"); // Asumsi halaman login ada di root
    exit;
}
// Ambil data pesanan pembelian
$buy_orders = $conn->query(
    "SELECT po.*, u.name as customer_name, p.name as product_name
     FROM product_orders po
     JOIN users u ON po.user_id = u.id
     JOIN products p ON po.product_id = p.id
     ORDER BY po.order_date DESC"
)->fetch_all(MYSQLI_ASSOC);

// Ambil data pesanan sewa
$rent_orders = $conn->query(
    "SELECT ro.*, u.name as customer_name, p.name as product_name
     FROM rental_orders ro
     JOIN users u ON ro.user_id = u.id
     JOIN products p ON ro.product_id = p.id
     ORDER BY ro.order_placed_date DESC"
)->fetch_all(MYSQLI_ASSOC);

define('FINE_PER_DAY', 20000); // Tentukan denda per hari, misal Rp 20.000
?>

<h2>Manajemen Pesanan</h2>

<?php if(isset($_SESSION['message'])): ?>
    <div style="padding:10px; border-radius:5px; background-color: <?= $_SESSION['message']['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message']['type'] === 'success' ? '#155724' : '#721c24' ?>; margin-bottom:15px;">
        <?= $_SESSION['message']['text']; ?>
    </div>
<?php unset($_SESSION['message']); endif; ?>

<h3>Pesanan Pembelian</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Pelanggan</th>
            <th>Produk</th>
            <th>Tgl Pesan</th>
            <th>Qty</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($buy_orders as $order): ?>
        <tr>
            <td><?= $order['id']; ?></td>
            <td><?= htmlspecialchars($order['customer_name']); ?></td>
            <td><?= htmlspecialchars($order['product_name']); ?></td>
            <td><?= date('d M Y', strtotime($order['order_date'])); ?></td>
            <td><?= $order['quantity']; ?></td>
            <td>Rp <?= number_format($order['total_price']); ?></td>
            <td><?= ucfirst($order['status']); ?></td>
            <td>
                <form action="process_updates.php" method="POST">
                    <input type="hidden" name="action" value="update_buy_status">
                    <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                    <select name="new_status" onchange="this.form.submit()">
                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3 style="margin-top: 40px;">Pesanan Penyewaan</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Pelanggan</th>
            <th>Produk</th>
            <th>Tgl Sewa</th>
            <th>Tgl Kembali</th>
            <th>Status</th>
            <th>Keterangan Denda</th> </tr>
    </thead>
    <tbody>
        <?php
        // Menggunakan zona waktu yang benar (sesuaikan jika perlu)
        $today = new DateTime("now", new DateTimeZone('Asia/Jakarta'));
        foreach ($rent_orders as $order):
            $endDate = new DateTime($order['rental_end_date']);
            
            // Kondisi keterlambatan: Status pesanan 'active' dan tanggal hari ini sudah lewat dari tanggal kembali
            $isOverdue = ($order['status'] === 'active') && ($today > $endDate);
        ?>
        <tr style="<?= $isOverdue ? 'background-color:#fff0f0;' : '' ?>">
            <td><?= $order['id']; ?></td>
            <td><?= htmlspecialchars($order['customer_name']); ?></td>
            <td><?= htmlspecialchars($order['product_name']); ?></td>
            <td><?= date('d M Y', strtotime($order['rental_start_date'])); ?></td>
            <td><?= date('d M Y', strtotime($order['rental_end_date'])); ?></td>
            <td>
                <form action="process_updates.php" method="POST">
                     <input type="hidden" name="action" value="update_rent_status">
                     <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                     <select name="new_status" onchange="this.form.submit()">
                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="active" <?= $order['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </form>
            </td>
            <td>
                <?php
                if ($isOverdue) {
                    $interval = $today->diff($endDate);
                    $daysOverdue = $interval->days;
                    
                    // Hitung denda: 10% dari harga sewa per item dikali jumlah hari terlambat
                    $finePerDay = $order['price_per_item_per_duration'] * 0.10;
                    $fineAmount = $daysOverdue * $finePerDay;

                    echo "Terlambat: <strong>{$daysOverdue} hari</strong><br>";
                    echo "Denda: <strong>Rp " . number_format($fineAmount) . "</strong><br>";
                    
                    // Cek apakah denda sudah dicatat di database
                    $checkFineStmt = $conn->prepare("SELECT id FROM rental_fines WHERE rental_order_id = ?");
                    $checkFineStmt->bind_param("i", $order['id']);
                    $checkFineStmt->execute();
                    $fineResult = $checkFineStmt->get_result();
                    $checkFineStmt->close();

                    if($fineResult->num_rows == 0) {
                        // Jika denda belum ada, tampilkan tombol untuk mencatatnya
                        echo '<form action="process_updates.php" method="POST" style="margin-top:5px;">
                                <input type="hidden" name="action" value="issue_fine">
                                <input type="hidden" name="order_id" value="'.$order['id'].'">
                                <input type="hidden" name="days_overdue" value="'.$daysOverdue.'">
                                <input type="hidden" name="fine_amount" value="'.$fineAmount.'">
                                <button type="submit" style="padding: 3px 8px; font-size: 0.9em; cursor:pointer;">Keluarkan Denda</button>
                              </form>';
                    } else {
                        // Jika sudah ada, tampilkan pesan
                        echo '<span style="color:green; font-size: 0.9em;">Denda sudah dicatat.</span>';
                    }
                } else {
                    echo "-";
                }
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'views/footer.php'; ?>