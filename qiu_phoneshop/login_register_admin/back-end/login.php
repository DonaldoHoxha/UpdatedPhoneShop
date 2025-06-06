<?php
// Modified login.php for admin
session_start();
include '../../main_page/back-end/db_conn.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];
    $stmt = $conn->prepare("SELECT name,password FROM administrator_user WHERE name = ? AND deleted_at IS NULL");
    $stmt->bind_param("s", $_POST['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($_POST['password'], $user['password'])) {
            // Clear any session data from previous attempts
            session_regenerate_id(true);

            // Use a different session variable name for admin
            $_SESSION['admin_user'] = $user['name'];

            if (isset($_POST['remember'])) {
                // Set secure cookie with a different name for admin
                $cookie_options = [
                    'expires'  => time() + 86400 * 30,
                    'path'     => '/',
                    'secure'   => true,    // Requires HTTPS
                    'httponly' => true,
                    'samesite' => 'Strict'
                ];
                setcookie("admin_user", $user['name'], $cookie_options);
            }

            // Redirect to dashboard
            header("Location: ../front-end/admin_dashboard.php");
        } else {
            header("Location: ../front-end/admin_login.html?error=invalid_password");
        }
    } else {
        header("Location: ../front-end/admin_login.html?error=user_not_found");
    }
}
$stmt->close();
$conn->close();
