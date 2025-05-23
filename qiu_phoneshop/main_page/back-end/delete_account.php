<?php
session_start();
require_once 'db_conn.php';

// Verifica se l'utente è loggato controllando la sessione
if (!isset($_SESSION['username'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Utente non autenticato"
    ]);
    exit();
}

// Prepara e esegue la query per ottenere l'ID utente
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Errore nella preparazione della query"
    ]);
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verifica se l'utente esiste nel database
if (!$user) {
    echo json_encode([
        "status" => "error",
        "message" => "Utente non trovato"
    ]);
    exit();
}

$user_id = $user['id'];

// Prepare and execute the delete query
$stmt = $conn->prepare("UPDATE user SET deleted_at = NOW() WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Optionally, destroy session after deletion
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete account']);
}

$stmt->close();
$conn->close();
