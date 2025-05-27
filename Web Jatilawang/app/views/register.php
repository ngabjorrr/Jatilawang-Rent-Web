<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar</title>
    <link rel="stylesheet" href="public/css/create-account.css">
</head>
<body>
    <div class="container">
        <form action="index.php?action=register" method="POST" class="form-box">
            <img src="public/images/Logo.png" alt="Logo" class="logo">
            <h2>Daftar</h2>
            <input type="text" name="fullname" placeholder="Nama Lengkap" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password (min 8 karakter)" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="address" placeholder="Alamat" required>
            <input type="text" name="phone" placeholder="Nomor Telepon" required>
            <label>
                <input type="checkbox" required> Saya setuju dengan <a href="#">Terms of Use</a> & <a href="#">Privacy Policy</a>
            </label>
            <button type="submit">Daftar</button>
            <p>Sudah memiliki akun? <a href="#">Log in</a></p>
        </form>
        <div class="image-box">
            <img src="public/images/climber.png" alt="Climber" />
        </div>
    </div>
</body>
</html>
