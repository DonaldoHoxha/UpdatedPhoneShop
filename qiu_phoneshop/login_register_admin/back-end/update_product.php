<?php
session_start();
header('Content-Type: application/json'); // Imposta il content type JSON

// Verifica che l'utente sia loggato come admin
if (!isset($_SESSION['admin_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include '../../main_page/back-end/db_conn.php';

try {
    // Ottieni l'ID admin
    $admin_username = $_SESSION['admin_user'];
    $stmt = $conn->prepare("SELECT id FROM administrator_user WHERE name = ?");
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        throw new Exception('Admin non trovato');
    }

    $admin = $result->fetch_assoc();
    $admin_id = $admin['id'];

    // Verifica il metodo POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metodo non consentito');
    }

    // Valida i campi obbligatori
    $required_fields = ['id', 'name', 'brand', 'ram', 'rom', 'camera', 'battery', 'price', 'quantity'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Il campo $field è obbligatorio");
        }
    }

    // Assegna i valori
    $product_id = $_POST['id'];
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $ram = (int)$_POST['ram'];
    $rom = (int)$_POST['rom'];
    $camera = (float)$_POST['camera'];
    $battery = (int)$_POST['battery'];
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];

    // Prepara ed esegui la query
    $stmt = $conn->prepare("UPDATE product SET 
        name = ?, 
        brand = ?, 
        ram = ?, 
        rom = ?, 
        camera = ?, 
        battery = ?, 
        price = ?, 
        quantity = ?, 
        fk_admin = ? 
        WHERE id = ?");

    $stmt->bind_param(
        "ssiiiiiiii",
        $name,
        $brand,
        $ram,
        $rom,
        $camera,
        $battery,
        $price,
        $quantity,
        $admin_id,
        $product_id
    );

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Prodotto aggiornato con successo',
            'product_id' => $product_id
        ]);
    } else {
        throw new Exception('Errore durante l\'aggiornamento: ' . $conn->error);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) $conn->close();
}
