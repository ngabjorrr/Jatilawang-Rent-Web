<?php
require_once 'app/controllers/RegisterController.php';

$action = $_GET['action'] ?? 'form';
$controller = new RegisterController();

if ($action === 'register') {
    $controller->register();
} else {
    $controller->showForm();
}
