<?php
// Avvia la sessione e include la connessione al database
session_start();
include 'db_conn.php';

// Imposta il content type a JSON per tutte le risposte
header('Content-Type: application/json');

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

// Validazione dell'ID prodotto
if (!isset($_POST['product_id']) || !is_numeric($_POST['product_id'])) {
    echo json_encode([
        "status" => "error", 
        "message" => "ID prodotto non valido"
    ]);
    exit();
}

$product_id = intval($_POST['product_id']);

// Inizia una transazione per garantire l'atomicità delle operazioni
$conn->begin_transaction();

try {
    // Controlla la quantità del prodotto nel carrello
    $check_stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ? FOR UPDATE");
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $row = $check_result->fetch_assoc();

    if (!$row) {
        throw new Exception("Prodotto non presente nel carrello");
    }

    // Se c'è più di un prodotto, decrementa la quantità
    if ($row['quantity'] > 1) {
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?");
        $update_stmt->bind_param("ii", $user_id, $product_id);
        $update_stmt->execute();
        
        if ($update_stmt->affected_rows === 0) {
            throw new Exception("Nessuna riga aggiornata");
        }
    } 
    // Altrimenti rimuovi completamente il prodotto
    else {
        $delete_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $delete_stmt->bind_param("ii", $user_id, $product_id);
        $delete_stmt->execute();
        
        if ($delete_stmt->affected_rows === 0) {
            throw new Exception("Nessuna riga eliminata");
        }
    }

    // Se tutto è andato bene, conferma le modifiche
    $conn->commit();
    echo json_encode([
        "status" => "success", 
        "message" => "Prodotto rimosso dal carrello"
    ]);
    
} catch (Exception $e) {
    // In caso di errore, annulla tutte le modifiche
    $conn->rollback();
    echo json_encode([
        "status" => "error", 
        "message" => $e->getMessage()
    ]);
}

// Chiudi la connessione al database
$conn->close();