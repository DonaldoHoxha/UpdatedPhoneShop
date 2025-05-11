<?php
// Avvia la sessione all'inizio del file
session_start();

// Includi il file di connessione al database
include 'db_conn.php';

// Verifica se la connessione al database ha avuto successo
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Imposta il tipo di contenuto come JSON
header('Content-Type: application/json');

// Verifica se è presente una query di ricerca
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search_query = $_GET['query'];
    
    // Prepara la query SQL per cercare prodotti con nome simile
    $stmt = $conn->prepare("SELECT * FROM product WHERE name LIKE ?");
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("s", $search_param);

    // Se l'utente è loggato, registra la ricerca
    if (isset($_SESSION['username'])) {
        // Ottieni l'ID utente dal nome utente
        $username = $_SESSION['username'];
        $user_id_stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
        $user_id_stmt->bind_param("s", $username);
        $user_id_stmt->execute();
        $user_result = $user_id_stmt->get_result();
        
        if ($user_row = $user_result->fetch_assoc()) {
            $user_id = $user_row['id'];
            
            // Ottieni tutti gli ID dei prodotti corrispondenti
            $id_stmt = $conn->prepare("SELECT id FROM product WHERE name LIKE ?");
            $id_stmt->bind_param("s", $search_param);
            $id_stmt->execute();
            $product_result = $id_stmt->get_result();
            
            // Inserisci i record di ricerca nella tabella searches
            $insert_stmt = $conn->prepare("INSERT INTO searches (user_id, product_id) VALUES (?, ?)");
            while ($product = $product_result->fetch_assoc()) {
                $insert_stmt->bind_param("ii", $user_id, $product['id']);
                if (!$insert_stmt->execute()) {
                    // Registra l'errore nel log
                    error_log("Failed to track search: " . $conn->error);
                }
            }
            
            // Chiudi le statement
            $insert_stmt->close();
            $id_stmt->close();
        }
        $user_id_stmt->close();
    }
} else {
    // Se non c'è query, restituisci tutti i prodotti
    $stmt = $conn->prepare("SELECT * FROM product");
}

// Esegui la query principale
$stmt->execute();
$result = $stmt->get_result();

// Prepara l'array per i risultati JSON
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Restituisci i prodotti in formato JSON
echo json_encode($products);

// Chiudi la connessione al database
$stmt->close();
$conn->close();