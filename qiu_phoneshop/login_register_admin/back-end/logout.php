<?php
// logout.php for admin
session_start();

// Clear admin-specific session variables
unset($_SESSION['admin_user']);

// Clear admin-specific cookies
if (isset($_COOKIE['admin_user'])) {
    setcookie('admin_user', '', time() - 3600, '/');
}

// Redirect to login page
header('Location: ../front-end/admin_login.html');
exit();
