<?php
$pageTitle = "Kelola Denda";
include 'views/header.php';

if (!isAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Ambil semua data denda dan gabungkan (JOIN) dengan informasi pelanggan dan produk
$fines = $conn->query(
    "SELECT 
        rf.id as fine_id,
        rf.fine_date,
        rf.days_overdue,
        rf.fine_amount,
        rf.status as fine_status,
        ro.id as order_id,
        u.name as customer_name,
        p.name as product_name
     FROM rental_fines rf
     JOIN rental_orders ro ON rf.rental_order_id = ro.id
     JOIN users u ON ro.user_id = u.id
     JOIN products p ON ro.product_id = p.id
     ORDER BY rf.fine_date DESC, rf.id DESC"
)->fetch_all(MYSQLI_ASSOC);
?>

<h2>Manajemen Denda Penyewaan</h2>
<p>Halaman ini berisi daftar semua denda yang telah dicatat untuk penyewaan yang terlambat.</p>

<div class="card"> <table>
        <thead>
            <tr>
                <th>ID Denda</th>
                <th>ID Pesanan</th>
                <th>Pelanggan</th>
                <th>Produk Disewa</th>
                <th>Tanggal Dikeluarkan</th>
                <th>Terlambat</th>
                <th>Jumlah Denda</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($fines)): ?>
                <?php foreach ($fines as $fine): ?>
                <tr>
                    <td style="text-align: center;"><?= $fine['fine_id']; ?></td>
                    <td style="text-align: center;"><?= $fine['order_id']; ?></td>
                    <td><?= htmlspecialchars($fine['customer_name']); ?></td>
                    <td><?= htmlspecialchars($fine['product_name']); ?></td>
                    <td><?= date('d M Y', strtotime($fine['fine_date'])); ?></td>
                    <td style="text-align: center;"><?= $fine['days_overdue']; ?> hari</td>
                    <td style="text-align: right;">Rp <?= number_format($fine['fine_amount']); ?></td>
                    <td style="text-align: center;">
                        <span class="status-badge <?= $fine['fine_status'] === 'paid' ? 'status-completed' : 'status-pending'; ?>">
                            <?= ucfirst($fine['fine_status']); ?>
                        </span>
                        <?php // Anda bisa menambahkan form di sini di masa depan untuk mengubah status menjadi 'paid' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Belum ada data denda yang tercatat.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'views/footer.php'; ?>