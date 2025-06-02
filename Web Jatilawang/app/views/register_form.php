<?php
// File: app/views/auth/register_form.php
// Nanti di sini bisa ditambahkan variabel untuk pesan error atau data lama jika validasi gagal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = "Buat Akun Baru";
// Kita asumsikan header.php dan footer.php bisa diakses dari sini
// atau path-nya disesuaikan di file pemanggil controller.
// Untuk contoh ini, kita akan include header dan footer nanti di file entry point.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Jatilawang Adventure</title>
    <link href="public/css/styles.css" rel="stylesheet" /> 
    <style>
        /* Beberapa style tambahan atau override jika diperlukan untuk form registrasi */
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f9fafb;}
        .form-section { width: 100%; max-width: 500px; padding: 2rem; }
        .form-container h2 { margin-bottom: 0.5rem; }
        .form-container .subtitle { margin-bottom: 1.5rem; }
        .form-container input { margin-bottom: 1rem; }
        .error-message { color: red; font-size: 0.9em; margin-bottom: 1rem; }
        .success-message { color: green; font-size: 0.9em; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="form-section">
        <div class="form-container">
            <img src="public/assets/Logo.png" alt="Company Logo" class="logo-img" style="max-width:100px; margin-bottom:1rem; border-radius:0%;" />
            <h2>Buat Akun Baru</h2>
            <p class="subtitle">Isi detail di bawah ini untuk membuat akun.</p>

            <?php if (isset($_SESSION['error_message'])): ?>
                <p class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
            <?php endif; ?>
            <?php if (isset($_SESSION['success_message'])): ?>
                <p class="success-message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
            <?php endif; ?>

            <form action="register_page.php" method="post">
                <input type="text" name="fullname" placeholder="Nama Lengkap" required 
                       value="<?php echo isset($_SESSION['form_data']['fullname']) ? htmlspecialchars($_SESSION['form_data']['fullname']) : ''; ?>">

                <input type="text" name="username" placeholder="Username" required
                       value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>">

                <input type="email" name="email" placeholder="Alamat Email" required
                       value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">

                <input type="password" name="password" placeholder="Password" required>

                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>

                <textarea name="address" placeholder="Alamat" rows="3" style="width: 100%; padding: 0.75rem; margin-bottom: 1rem; border: 1px solid #d1d5db; border-radius: 0.375rem;" required><?php echo isset($_SESSION['form_data']['address']) ? htmlspecialchars($_SESSION['form_data']['address']) : ''; ?></textarea>

                <input type="tel" name="phone" placeholder="Nomor Telepon" required
                       value="<?php echo isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : ''; ?>">

                <button type="submit" name="register_submit">Daftar</button>
            </form>
            <p class="signup" style="margin-top: 1rem;">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>
        </div>
    </div>
</body>
</html>
<?php
// Hapus data form lama dari session setelah ditampilkan
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>