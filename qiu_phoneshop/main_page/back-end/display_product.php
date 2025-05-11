<?php
// Includi il file di connessione al database
include 'db_conn.php';

// Verifica se la connessione al database è attiva
if ($conn->connect_error) {
    // In caso di errore, restituisci un messaggio JSON invece di die()
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Connessione al database fallita"]);
    exit();
}

// Imposta l'header per risposte JSON
header('Content-Type: application/json');

// Verifica che l'ID prodotto sia stato passato
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    echo json_encode(["status" => "error", "message" => "ID prodotto mancante o non valido"]);
    exit();
}

// Sanitizza l'input (protezione base contro SQL injection)
$product_id = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);

// Prepara la query SQL
$stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Errore nella preparazione della query"]);
    exit();
}

// Lega il parametro e esegui la query
$stmt->bind_param("i", $product_id);
if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Errore nell'esecuzione della query"]);
    exit();
}

// Ottieni il risultato
$result = $stmt->get_result();

// Prepara l'array per i risultati
$products = [];

// Se non ci sono risultati, restituisci un messaggio appropriato
if ($result->num_rows === 0) {
    echo json_encode(["status" => "success", "message" => "Nessun prodotto trovato", "data" => []]);
    exit();
}

// Estrai i dati
while ($row = $result->fetch_assoc()) {
    // Puoi filtrare/trasformare i dati qui se necessario
    $products[] = $row;
}

// Restituisci i prodotti in formato JSON
echo json_encode([
    "status" => "success",
    "data" => $products
]);

// Chiudi le risorse
$stmt->close();
$conn->close();