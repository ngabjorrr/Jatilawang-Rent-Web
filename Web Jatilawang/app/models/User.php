<?php
class User
{
    public $name, $username, $password, $email, $address, $phone;

    public function save()
    {
        try {
            // Periksa baris ini:
            $pdo = new PDO('mysql:host=localhost;dbname=jatilawang_db', 'root', ''); // Pastikan dbname=jatilawang_db
            // Baris selanjutnya adalah perintah untuk memasukkan data
            $stmt = $pdo->prepare("INSERT INTO users (name, username, password, email, address, phone) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([$this->name, $this->username, $this->password, $this->email, $this->address, $this->phone]);
        } catch (PDOException $e) {
            error_log("PDO Save Error: " . $e->getMessage()); // Baris ini membantu mencatat error jika terjadi
            return false;
        }
    }

    // Fungsi untuk mengecek apakah email sudah ada (contoh dengan PDO)
    public function findByEmail($email)
    {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=jatilawang_db', 'root', ''); // Sesuaikan dbname
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetchColumn(); // Mengembalikan id jika ditemukan, false jika tidak
        } catch (PDOException $e) {
            // error_log("Error findByEmail: " . $e->getMessage()); // Catat error
            return false; // Sebaiknya tangani error dengan lebih baik
        }
    }

    // Fungsi untuk mengecek apakah username sudah ada (contoh dengan PDO)
    public function findByUsername($username)
    {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=jatilawang_db', 'root', ''); // Sesuaikan dbname
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->fetchColumn(); // Mengembalikan id jika ditemukan, false jika tidak
        } catch (PDOException $e) {
            // error_log("Error findByUsername: " . $e->getMessage()); // Catat error
            return false;
        }
    }
}
