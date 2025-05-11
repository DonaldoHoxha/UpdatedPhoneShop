<?php
// avvio la sessione
session_start();
include 'db_conn.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "Utente non autenticato"]);
    exit();
}

// ottengo l'ID dell'utente dalla sessione
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

//se l'utente non esiste, restituisco un errore
if (!$user) {
    echo json_encode(["status" => "error", "message" => "Utente non trovato"]);
    exit();
}

$user_id = $user['id'];

// ottengo l'ID del prodotto dalla richiesta POST
if (!isset($_POST['product_id'])) {
    echo json_encode(["status" => "error", "message" => "ID prodotto non valido"]);
    exit();
}

$product_id = intval($_POST['product_id']);

// controllo se il prodotto esiste nel carello
$check_stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
$check_stmt->bind_param("ii", $user_id, $product_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // se il prodotto esiste, aggiorno la quantità
    $update_stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
    $update_stmt->bind_param("ii", $user_id, $product_id);
    if ($update_stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Prodotto aggiornato nel carrello"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Errore nell'aggiornamento del carrello"]);
    }
} else {
    // se il prodotto non esiste, lo inserisco nel carrello
    $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
    $insert_stmt->bind_param("ii", $user_id, $product_id);
    if ($insert_stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Prodotto aggiunto al carrello"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Errore nell'aggiunta al carrello"]);
    }
}
$conn->close();
