<?php
session_start();
require_once '../../main_page/back-end/db_conn.php';

// Verifica se l'utente è loggato controllando la sessione
if (!isset($_SESSION['admin_user'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Utente non autenticato"
    ]);
    exit();
}
// Prepara ed esegue la query per ottenere l'ID dell'admin
$admin_id = $_SESSION['admin_user'];
$stmt = $conn->prepare("SELECT id FROM administrator_user WHERE name = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    throw new Exception('Admin non trovato');
}

$admin = $result->fetch_assoc();
$admin_id = $admin['id'];

// Prepare and execute the delete query
$stmt = $conn->prepare("UPDATE administrator_user SET deleted_at = NOW() WHERE id = ?");
$stmt->bind_param("i", $admin_id);

if ($stmt->execute()) {
    // Optionally, destroy session after deletion
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete account']);
}

$stmt->close();
$conn->close();
