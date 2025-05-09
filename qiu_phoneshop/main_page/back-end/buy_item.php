<?php
session_start();
include 'db_conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "Utente non autenticato"]);
    exit();
}

if (!isset($_POST['product_id'])) {
    echo json_encode(["status" => "error", "message" => "ID prodotto mancante"]);
    exit();
}

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

$user_id = $user['id'];
$shipping_address = $user['shipping_address'];
$product_id = $_POST['product_id'];

$conn->begin_transaction();

try {
    // Verifica disponibilità magazzino (usiamo FOR UPDATE per bloccare la riga)
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

    // Recupera prodotto dal carrello (specificando esplicitamente c.quantity per il carrello)
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

    $quantity_in_cart = $row['cart_quantity']; // Ora usiamo l'alias cart_quantity
    $price = $row['price'];
    $quantity_to_buy = 1;

    // Inserisci ordine
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total_price, shipping_address) 
                                VALUES (?, ?, ?, ?, ?)");
    $total_price = $quantity_to_buy * $price;
    $stmt->bind_param("iiids", $user_id, $product_id, $quantity_to_buy, $total_price, $shipping_address);
    $stmt->execute();

    // Aggiorna carrello
    if ($quantity_in_cart == 1) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
    } else {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
    }
    $stmt->execute();

    // Aggiorna magazzino
    $stmt = $conn->prepare("UPDATE product SET quantity = quantity - 1 WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Prodotto acquistato con successo"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
