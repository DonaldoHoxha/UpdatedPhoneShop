<?php
session_start();
header('Content-Type: application/json'); // Aggiungi questa riga

// Verifica che l'utente sia loggato come admin
if (!isset($_SESSION['admin_user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

include '../../main_page/back-end/db_conn.php';

try {
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? null;
        $brand = $_POST['brand'] ?? null;
        $ram = $_POST['ram'] ?? null;
        $rom = $_POST['rom'] ?? null;
        $camera = $_POST['camera'] ?? null;
        $battery = $_POST['battery'] ?? null;
        $price = $_POST['price'] ?? null;
        $quantity = $_POST['quantity'] ?? null;

        // Validazione campi obbligatori
        $required = ['name', 'brand', 'ram', 'rom', 'camera', 'battery', 'price', 'quantity'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Il campo $field è obbligatorio");
            }
        }
        // Prepara la query SQL
        $stmt = $conn->prepare("INSERT INTO product (name, brand, ram, rom, camera, battery, price, quantity, fk_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiiiiii", $name, $brand, $ram, $rom, $camera, $battery, $price, $quantity, $admin_id);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Prodotto aggiunto con successo',
                'product_id' => $conn->insert_id
            ]);
        } else {
            throw new Exception('Errore nel database: ' . $conn->error);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
