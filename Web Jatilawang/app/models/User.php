<?php
class User {
    public $name, $username, $password, $email, $address, $phone;

    public function save() {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=your_db', 'root', '');
            $stmt = $pdo->prepare("INSERT INTO users (name, username, password, email, address, phone) VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([$this->name, $this->username, $this->password, $this->email, $this->address, $this->phone]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
