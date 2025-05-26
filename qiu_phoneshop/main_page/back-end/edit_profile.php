<?php
session_start();
include 'db_conn.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "Utente non autenticato"]);
    exit();
}


// Recupera i dati dell'utente dal database
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id, shipping_address, email FROM user WHERE username = ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Errore nel preparare la query"]);
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$user_id = $user['id'] ?? null;
$current_username = $_SESSION['username'];

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$shipping_address = $_POST['shipping_address'] ?? '';

// Validation
if (empty($username) || empty($email)) {
    exit(json_encode(['success' => false, 'message' => 'Username e email sono campi obbligatori']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit(json_encode(['success' => false, 'message' => 'Formato email non valido']));
}

// Check if new username is taken
if ($username !== $current_username) {
    $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        exit(json_encode(['success' => false, 'message' => 'Username già in uso']));
    }
}

// Update user data
$stmt = $conn->prepare("UPDATE user SET username = ?, email = ?, shipping_address = ? WHERE id = ?");
$stmt->bind_param("sssi", $username, $email, $shipping_address, $user_id);

if ($stmt->execute()) {
    $_SESSION['username'] = $username;
    echo json_encode([
        'success' => true,
        'username' => $username,
        'email' => $email,
        'shipping_address' => $shipping_address
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Errore del database']);
}

$stmt->close();
$conn->close();
