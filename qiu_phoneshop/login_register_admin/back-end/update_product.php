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
$admin_username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM administrator_user WHERE name = ?");
$stmt->bind_param("s", $admin_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    $admin_id = $admin['id'];
} else {
    header('Location: admin_login.html');
    exit();
}

// Controlla se il metodo è POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prendi i dati dal form
    $product_id = $_POST['id']; 
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $ram = $_POST['ram'];
    $rom = $_POST['rom'];
    $camera = $_POST['camera'];
    $battery = $_POST['battery'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Prepara la query SQL per l'UPDATE
    $stmt = $conn->prepare("UPDATE product SET name = ?, brand = ?, ram = ?, rom = ?, camera = ?, battery = ?, price = ?, quantity = ?, fk_admin = ? WHERE id = ?");
    $stmt->bind_param("ssiiiiiiii", $name, $brand, $ram, $rom, $camera, $battery, $price, $quantity, $admin_id, $product_id);

    // Esegui la query
    if ($stmt->execute()) {
        header('Location: ../front-end/admin_dashboard.php?success=product_updated');
    } else {
        header('Location: ../front-end/admin_dashboard.php?error=product_update_failed');
    }

    $stmt->close();
    $conn->close();
} else {
    // Reindirizza se non è POST
    header('Location: admin_dashboard.php');
}
