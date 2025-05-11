<?php
// Avvia la sessione e include la connessione al database
session_start();
include 'db_conn.php';

// Imposta l'header per risposte JSON
header('Content-Type: application/json');

// Verifica se l'utente è loggato
if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "Utente non autenticato"]);
    exit();
}

// Verifica se è stato passato l'ID del prodotto
if (!isset($_POST['product_id'])) {
    echo json_encode(["status" => "error", "message" => "ID prodotto mancante"]);
    exit();
}

// Recupera i dati dell'utente dal database
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id, shipping_address FROM user WHERE username = ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Errore nel preparare la query"]);
    exit();
}

$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(["status" => "error", "message" => "Utente non trovato"]);
    exit();
}

// Salva i dati utente e prodotto in variabili
$user_id = $user['id'];
$shipping_address = $user['shipping_address'];
$product_id = $_POST['product_id'];

// Inizia una transazione per garantire l'atomicità delle operazioni
$conn->begin_transaction();

try {
    // Verifica disponibilità prodotto con blocco della riga (FOR UPDATE)
    $stmt = $conn->prepare("SELECT quantity FROM product WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        throw new Exception("Prodotto non trovato");
    }

    if ($product['quantity'] < 1) {
        throw new Exception("Prodotto non disponibile in magazzino");
    }

    // Recupera il prodotto dal carrello con quantità e prezzo
    $stmt = $conn->prepare("SELECT c.quantity as cart_quantity, p.price 
                          FROM cart c 
                          JOIN product p ON c.product_id = p.id 
                          WHERE c.user_id = ? AND c.product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        throw new Exception("Prodotto non trovato nel carrello");
    }

    $quantity_in_cart = $row['cart_quantity']; // Quantità nel carrello
    $price = $row['price'];
    $quantity_to_buy = 1; // Acquista 1 unità alla volta

    // Inserisce l'ordine nel database
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total_price, shipping_address) 
                                VALUES (?, ?, ?, ?, ?)");
    $total_price = $quantity_to_buy * $price;
    $stmt->bind_param("iiids", $user_id, $product_id, $quantity_to_buy, $total_price, $shipping_address);
    $stmt->execute();

    // Aggiorna il carrello: elimina se quantità=1, altrimenti decrementa
    if ($quantity_in_cart == 1) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
    } else {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
    }
    $stmt->execute();

    // Aggiorna la quantità disponibile nel magazzino
    $stmt = $conn->prepare("UPDATE product SET quantity = quantity - 1 WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    // Conferma tutte le modifiche nel database
    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Prodotto acquistato con successo"]);
} catch (Exception $e) {
    // In caso di errore, annulla tutte le modifiche
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

// Chiude la connessione al database
$conn->close();