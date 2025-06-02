<?php
// File: Web Jatilawang/register_page.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Jika pengguna sudah login, arahkan ke index.php
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/app/controllers/RegisterController.php';

$controller = new RegisterController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
    $controller->register(); // Handles form submission logic
} else {
    // GET request: show the registration form (which is now part of the combined view)
    $active_form = 'signup'; // Signal to the template to show signup form by default
    
    // Include the new combined authentication view
    // This replaces the direct call to $controller->showForm() or including a separate register_form.php
    include __DIR__ . '/app/views/auth_forms.php';
    
    // Clean up session form data if we are just displaying the form,
    // to prevent old data from appearing if the user navigates away and back.
    // This was originally at the end of the old register_form.php.
    if (isset($_SESSION['form_data'])) {
        unset($_SESSION['form_data']);
    }
}
?>