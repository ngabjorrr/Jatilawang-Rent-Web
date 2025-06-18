<?php
// File: app/controllers/RegisterController.php
require_once __DIR__ . '/../models/User.php';

class RegisterController
{
    /*
    // This method is no longer the primary way the form is displayed.
    // register_page.php now directly includes app/views/auth_forms.php for GET requests.
    public function showForm()
    {
        // require __DIR__ . '/../views/register_form.php'; // Old way
    }
    */

    public function register()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
            // Simpan input form untuk diisi kembali jika ada error
            $_SESSION['form_data'] = $_POST;

            // Validasi dasar
            $fullname = trim($_POST['fullname']);
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $address = trim($_POST['address']);
            $phone = trim($_POST['phone']);

            if (empty($fullname) || empty($username) || empty($email) || empty($password) || empty($address) || empty($phone)) {
                $_SESSION['error_message'] = "Semua field wajib diisi.";
                header("Location: register_page.php"); // Kembali ke form registrasi
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error_message'] = "Format email tidak valid.";
                header("Location: register_page.php");
                exit;
            }

            if (strlen($password) < 6) { // Contoh validasi panjang password
                $_SESSION['error_message'] = "Password minimal harus 6 karakter.";
                header("Location: register_page.php");
                exit;
            }

            if ($password !== $confirm_password) {
                $_SESSION['error_message'] = "Password dan konfirmasi password tidak cocok.";
                header("Location: register_page.php");
                exit;
            }
            
            // Cek apakah username atau email sudah ada (membutuhkan metode di User model)
            $userModelCheck = new User();
            if ($userModelCheck->findByUsername($username)) {
                 $_SESSION['error_message'] = "Username sudah digunakan.";
                 header("Location: register_page.php");
                 exit;
            }
            if ($userModelCheck->findByEmail($email)) {
                 $_SESSION['error_message'] = "Email sudah terdaftar.";
                 header("Location: register_page.php");
                 exit;
            }


            $user = new User();
            $user->name = $fullname;
            $user->username = $username; 
            $user->password = password_hash($password, PASSWORD_BCRYPT);
            $user->email = $email;
            $user->address = $address;
            $user->phone = $phone;

            if ($user->save()) { 
                unset($_SESSION['form_data']); 
                $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";
                header("Location: login.php"); 
                exit;
            } else {
                $_SESSION['error_message'] = "Registrasi gagal. Username atau email mungkin sudah digunakan, atau terjadi kesalahan pada database.";
                header("Location: register_page.php"); 
                exit;
            }
        } else {
            // Jika bukan POST request atau submit tidak ditekan, arahkan untuk menampilkan form
            // This part might not be reached if register_page.php handles GET directly.
            $active_form = 'signup';
            include __DIR__ . '/../views/auth_forms.php';
            if (isset($_SESSION['form_data'])) {
                 unset($_SESSION['form_data']);
            }
        }
    }
}