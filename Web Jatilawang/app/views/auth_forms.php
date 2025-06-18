<?php
// File: Web Jatilawang/app/views/auth_forms.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Variabel $active_form seharusnya diatur oleh file yang meng-include (login.php atau register_page.php)
// Default ke 'login' jika tidak diatur
$container_class = (isset($active_form) && $active_form === 'signup') ? 'container active' : 'container';
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
    <link rel="stylesheet" href="public/css/login_register_style.css" /> 
    <title>Formulir Masuk & Registrasi - Jatilawang Adventure</title>
  </head>
  <body>
    <div class="<?php echo $container_class; ?>">
      <div class="forms">
        <div class="form login">
          <span class="title">Masuk</span>

          <form action="proses_login.php" method="post">
            <div class="input-field">
              <input type="email" name="email" placeholder="Enter your email" required />
              <i class="uil uil-envelope icon"></i>
            </div>
            <div class="input-field">
              <input type="password" name="password" class="password" placeholder="Enter your password" required />
              <i class="uil uil-lock icon"></i>
              <i class="uil uil-eye-slash showHidePw"></i>
            </div>

            <div class="checkbox-text">
              <div class="checkbox-content">
                <input type="checkbox" id="logCheck" />
                <label for="logCheck" class="text">Ingat saya</label>
              </div>
              </div>

            <div class="input-field button">
              <input type="submit" value="Masuk" />
            </div>
          </form>

          <div class="login-signup">
            <span class="text">Belum punya akun?
              <a href="#" class="text signup-link">Daftar Sekarang</a>
            </span>
          </div>
        </div>

        <div class="form signup">
          <span class="title">Pendaftaran</span>

          <?php if (isset($_SESSION['error_message'])): ?>
              <p style="color: red; font-size: 0.9em; margin-bottom: 1rem; text-align:center;"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
          <?php endif; ?>
          <?php if (isset($_SESSION['success_message'])): ?>
              <p style="color: green; font-size: 0.9em; margin-bottom: 1rem; text-align:center;"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
          <?php endif; ?>

          <form action="register_page.php" method="post">
            <div class="input-field">
              <input type="text" name="fullname" placeholder="Enter your name" required 
                     value="<?php echo isset($_SESSION['form_data']['fullname']) ? htmlspecialchars($_SESSION['form_data']['fullname']) : ''; ?>" />
              <i class="uil uil-user"></i>
            </div>
            <div class="input-field">
              <input type="text" name="username" placeholder="Enter your username" required
                     value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>" />
              <i class="uil uil-user-circle"></i>
            </div>
            <div class="input-field">
              <input type="email" name="email" placeholder="Enter your email" required 
                     value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" />
              <i class="uil uil-envelope icon"></i>
            </div>
            <div class="input-field">
              <input type="password" name="password" class="password" placeholder="Create a password" required />
              <i class="uil uil-lock icon"></i>
            </div>
            <div class="input-field">
              <input type="password" name="confirm_password" class="password" placeholder="Confirm a password" required />
              <i class="uil uil-lock icon"></i>
              <i class="uil uil-eye-slash showHidePw"></i>
            </div>
            <div class="input-field">
                 <input type="text" name="address" placeholder="Enter your address" required style="padding-bottom: 20px;" value="<?php echo isset($_SESSION['form_data']['address']) ? htmlspecialchars($_SESSION['form_data']['address']) : ''; ?>">
                 <i class="uil uil-map-marker"></i>
            </div>
            <div class="input-field">
              <input type="tel" name="phone" placeholder="Enter your phone number" required
                     value="<?php echo isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : ''; ?>" />
              <i class="uil uil-phone"></i>
            </div>

            <div class="checkbox-text">
              <div class="checkbox-content">
                <input type="checkbox" id="termCon" required />
                <label for="termCon" class="text">Saya menyetujui semua syarat dan ketentuan</label>
              </div>
            </div>

            <div class="input-field button">
              <input type="submit" name="register_submit" value="Daftar" />
            </div>
          </form>

          <div class="login-signup">
            <span class="text">Sudah punya akun?
              <a href="#" class="text login-link">Masuk Sekarang</a>
            </span>
          </div>
        </div>
      </div>
    </div>
    <script src="public/js/login_register_script.js"></script>
  </body>
</html>
