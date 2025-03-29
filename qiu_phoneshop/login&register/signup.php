<?php
// Start session to store error messages and form data
session_start();
// Database connection
include '../main_page/db_conn.php';

$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
$confirm_password = $_POST['confirmPassword'];

// Check connection
if ($conn->connect_error) {
    $_SESSION['errors'] = ["Database connection failed: " . $conn->connect_error];
    header("Location: login&register.html");
    exit();
}

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize errors array ( an array where the eventual errors will be stored and shown at the end)
    $errors = [];

    // Store form data in session to repopulate the form
    $_SESSION['form_data'] = [
        'email' => $_POST['email'] ?? '',
        'username' => $_POST['username'] ?? ''
        // We don't store passwords in session for security reasons
    ];

    // Only proceed with validation if no empty fields
    if (empty($errors)) {
        // Get form data and sanitize inputs
        $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $username = htmlspecialchars($_POST["username"]);
        $password = $_POST["password"];
        $confirm_password = $_POST["confirmPassword"];

        if ($username !== $_POST['username']) {
            $errors[] = "Username not valid";
            header("Location: login&register.html?error=username_not_valid");
            exit();
        }
        // Validate email
        // Validate password match
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match";
            header("Location: login&register.html?error=password_mismatch");
            exit();
        }

        // Validate password strength (example: at least 8 characters)
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
            header("Location: login&register.html?error=password_length");
            exit();
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            $errors[] = "Email already registered";
            header("Location: login&register.html?error=email_exists");
            exit();
        }

        // Check if username already exists
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($row['count'] > 0) {
            $errors[] = "Username already taken";

            header("Location: login&register.html?error=username_exists");
            exit();
        }
        // If no errors, insert the new user
        if (empty($errors)) {
            try {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Prepare SQL and bind parameters
                $stmt = $conn->prepare("INSERT INTO user (username,email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $hashed_password);

                if ($stmt->execute()) {
                    // Clear any session data from previous attempts
                    unset($_SESSION['errors']);
                    unset($_SESSION['form_data']);
                    // Redirect to success page or login page
                    $_SESSION['logged_in'] = true;
                    $_SESSION['username'] = $username;
                    header("Location: ../main_page/logged_Index.php"); //nome del file in cui deve andare dopo aver creato l'user
                    exit();
                } else {
                    $errors[] = "Registration failed: " . $stmt->error;
                }
            } catch (Exception $e) {
                $errors[] = "Registration failed: " . $e->getMessage();
            }
        }
    }

    // If there are errors, store them in session and redirect back to registration page
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;

        header("Location: test.php?errors"); //nome del login_registration file
        exit();
    }
}

// Close the database connection
$conn->close();
