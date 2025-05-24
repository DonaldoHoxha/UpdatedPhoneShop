<?php
// logout.php for admin
session_start();

// Clear admin-specific session variables
unset($_SESSION['username']);

// Clear admin-specific cookies
if (isset($_COOKIE['user'])) {
    setcookie('user', '', time() - 3600, '/');
}

// Redirect to login page
header('Location: ../main_page/front-end/index.php');
exit();
