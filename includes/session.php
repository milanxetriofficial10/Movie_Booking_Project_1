<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check login
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
?>
