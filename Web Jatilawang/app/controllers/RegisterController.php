<?php
require_once __DIR__ . '/../models/User.php';

class RegisterController {
    public function showForm() {
        require __DIR__ . '/../views/register.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $user->name = $_POST['fullname'];
            $user->username = $_POST['username'];
            $user->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $user->email = $_POST['email'];
            $user->address = $_POST['address'];
            $user->phone = $_POST['phone'];

            if ($user->save()) {
                echo "Registrasi berhasil!";
            } else {
                echo "Registrasi gagal.";
            }
        }
    }
}
