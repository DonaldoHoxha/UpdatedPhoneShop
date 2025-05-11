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

$user_id = $user['id'];
$shipping_address = $user['shipping_address'];

// Inizia una transazione per garantire l'atomicità delle operazioni
$conn->begin_transaction();

try {
    // Recupera i prodotti nel carrello con i relativi prezzi e quantità disponibili
    $stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.price, p.quantity as stock_quantity FROM cart c JOIN product p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $all_success = true;  // Flag per verificare il successo di tutte le operazioni
    $total_price = 0;     // Totale dell'ordine
    $products = [];       // Array per memorizzare i prodotti

    // Prima verifica la disponibilità di tutti i prodotti
    while ($row = $result->fetch_assoc()) {
        if ($row['stock_quantity'] <= 0) {
            throw new Exception("Prodotto ID {$row['product_id']} non disponibile in magazzino");
        }
        if ($row['stock_quantity'] < $row['quantity']) {
            throw new Exception("Quantità richiesta non disponibile per il prodotto ID {$row['product_id']}");
        }
        $products[] = $row;  // Aggiunge il prodotto all'array se disponibile
    }

    // Se tutti i prodotti sono disponibili, procedi con l'ordine
    foreach ($products as $row) {
        $product_total = $row['quantity'] * $row['price'];
        $total_price += $product_total;

        // Inserisce l'ordine nel database
        $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total_price, shipping_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiids", $user_id, $row['product_id'], $row['quantity'], $product_total, $shipping_address);

        if (!$stmt->execute()) {
            $all_success = false;
            throw new Exception("Errore nell'inserimento dell'ordine");
        }

        // Aggiorna la quantità disponibile nel magazzino
        $stmt = $conn->prepare("UPDATE product SET quantity = quantity - ? WHERE id = ?");
        $stmt->bind_param("ii", $row['quantity'], $row['product_id']);
        if (!$stmt->execute()) {
            $all_success = false;
            throw new Exception("Errore nell'aggiornamento del magazzino");
        }
    }

    if ($all_success) {
        // Svuota il carrello dopo l'acquisto riuscito
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Conferma tutte le modifiche nel database
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Prodotti acquistati con successo", "total_price" => $total_price]);
    } else {
        // Annulla tutte le modifiche in caso di errore
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Errore nell'acquisto del carrello"]);
    }
} catch (Exception $e) {
    // Gestisce eventuali eccezioni e annulla la transazione
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

// Chiude la connessione al database
$conn->close();