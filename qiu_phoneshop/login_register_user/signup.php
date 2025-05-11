<?php
// Avvia la sessione per memorizzare messaggi di errore e dati del form
session_start();

// Connessione al database
include '../main_page/back-end/db_conn.php';

// Verifica la connessione al database
if ($conn->connect_error) {
    $_SESSION['errors'] = ["Database connection failed: " . $conn->connect_error];
    header("Location: login_register.html");
    exit();
}

// Processa i dati del form quando viene inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Inizializza l'array degli errori
    $errors = [];

    // Memorizza i dati del form nella sessione per ripopolare il form
    $_SESSION['form_data'] = [
        'email' => $_POST['email'] ?? '',
        'username' => $_POST['username'] ?? ''
        // Non memorizziamo le password nella sessione per sicurezza
    ];

    // Ottieni e sanifica i dati del form
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $username = htmlspecialchars($_POST["username"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirmPassword"];
    $shipping_address = $_POST["shipping_address"];

    // Validazione username
    if ($username !== $_POST['username']) {
        header("Location: login_register.html?error=username_not_valid");
        exit();
    }

    // Validazione corrispondenza password
    if ($password !== $confirm_password) {
        header("Location: login_register.html?error=password_mismatch");
        exit();
    }

    // Validazione forza password (almeno 8 caratteri)
    if (strlen($password) < 8) {
        header("Location: login_register.html?error=password_length");
        exit();
    }

    // Verifica se l'email esiste già
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        header("Location: login_register.html?error=email_exists");
        exit();
    }

    // Verifica se lo username esiste già
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        header("Location: login_register.html?error=username_exists");
        exit();
    }

    // Inserisce l'utente nel database
    try {
        // Hash della password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepara la SQL e lega i parametri
        $stmt = $conn->prepare("INSERT INTO user (username, email, password, shipping_address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $shipping_address);

        if ($stmt->execute()) {
            // Pulisce i dati di sessione dei tentativi precedenti
            unset($_SESSION['errors']);
            unset($_SESSION['form_data']);
            
            // Imposta lo stato di login e reindirizza
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;
            header("Location: ../main_page/front-end/logged_Index.php");
            exit();
        } else {
            $errors[] = "Registration failed: " . $stmt->error;
        }
    } catch (Exception $e) {
        $errors[] = "Registration failed: " . $e->getMessage();
    }

    // Se ci sono errori, memorizzali in sessione e reindirizza
    if (!empty($errors)) {
        header("Location: test.php?errors");
        exit();
    }
}

// Chiude la connessione al database
$conn->close();