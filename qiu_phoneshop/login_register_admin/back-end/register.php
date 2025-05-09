<?php
session_start();
include '../../main_page/back-end/db_conn.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];
    try {
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO administrator_user (name, password) VALUES ( ?, ?)");
        $stmt->bind_param("ss", $_POST['name'], $hashed_password);
        if ($stmt->execute()) {
            // Clear any session data from previous attempts
            unset($_SESSION['errors']);
            unset($_SESSION['form_data']);

            // Use admin-specific session variable
            $_SESSION['admin_user'] = $_POST['name'];

            header("Location: ../front-end/admin_dashboard.php");
            exit();
        } else {
            $errors[] = "Registration failed: " . $stmt->error;
        }
    } catch (Exception $e) {
        $errors[] = "Registration failed: " . $e->getMessage();
    }

    // If there are errors, store them in session and redirect back to registration page
    if (!empty($errors)) {
        header("Location: ../front-end/admin_login.html?errors");
        exit();
    }
}
