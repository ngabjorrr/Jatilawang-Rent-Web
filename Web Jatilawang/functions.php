<!-- functions.php -->
<?php
// Utility Function
function isLoggedIn() {
    return isset($_SESSION['user']);
}
?>