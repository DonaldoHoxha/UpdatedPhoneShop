<?php
session_start();

// Verifica che l'utente sia loggato come admin
if (!isset($_SESSION['username'])) {
    header('Location: admin_login.html');
    exit();
}

// Connessione al database
include '../../main_page/back-end/db_conn.php';

// Otteniamo l'id dell'admin loggato
$admin_id = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM administrator_user WHERE name = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    $admin_id = $admin['id'];
} else {
    // Se non troviamo l'admin, reindirizza
    header('Location: admin_login.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prendi i dati dal form
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $ram = $_POST['ram'];
    $rom = $_POST['rom'];
    $camera = $_POST['camera'];
    $battery = $_POST['battery'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Prepara la query SQL
    $stmt = $conn->prepare("INSERT INTO product (name, brand, ram, rom, camera, battery, price, quantity, fk_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiiiii", $name, $brand, $ram, $rom, $camera, $battery, $price, $quantity, $admin_id);

    // Esegui la query
    if ($stmt->execute()) {
        // Successo - reindirizza alla dashboard con messaggio di successo
        header('Location: ../front-end/admin_dashboard.php?success=product_added');
    } else {
        // Errore - reindirizza con messaggio di errore
        header('Location: ../front-end/admin_dashboard.php?error=product_add_failed');
    }

    $stmt->close();
    $conn->close();
} else {
    // Se non è una richiesta POST, reindirizza
    header('Location: admin_dashboard.php');
}
