<?php
// Avvia la sessione
session_start();

// 1. VALIDAZIONE DEL METODO DI INVIO
// ----------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Form not submitted.");
    // Blocca l'esecuzione se il form non è stato inviato via POST
}

// 2. CONTROLLO DEI CAMPI OBBLIGATORI
// ----------------------------------------------------------------
if (empty($_POST['username']) || empty($_POST['password'])) {
    // Reindirizza alla pagina di login con messaggio di errore
    header("Location: login_register.html?error=missing_fields");
    exit();
}

// 3. CONNESSIONE AL DATABASE
// ----------------------------------------------------------------
include '../main_page/back-end/db_conn.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 4. PREPARAZIONE QUERY SQL (con prepared statement)
// ----------------------------------------------------------------
$stmt = $conn->prepare("SELECT username, `password` FROM user WHERE username = ? AND deleted_at IS NULL");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// 5. BIND PARAMETRI ED ESECUZIONE
// ----------------------------------------------------------------
$stmt->bind_param("s", $_POST['username']);
$stmt->execute();
$result = $stmt->get_result();

// 6. GESTIONE RISULTATI
// ----------------------------------------------------------------
if ($result->num_rows === 1) {
    // Utente trovato nel database
    $user = $result->fetch_assoc();

    // Verifica la password con password_verify (hash sicuro)
    if (password_verify($_POST['password'], $user['password'])) {

        // Rigenera l'ID di sessione per prevenire fixation attacks
        session_regenerate_id(true);

        // 7. GESTIONE DEL "RICORDAMI"
        // --------------------------------------------------------
        if (isset($_POST['remember'])) {
            // Imposta un cookie sicuro
            $cookie_options = [
                'expires'  => time() + 86400 * 30, // 30 giorni
                'path'     => '/',                  // Valido per tutto il sito
                'secure'   => true,                 // Solo su HTTPS
                'httponly' => true,                 // Non accessibile via JS
                'samesite' => 'Strict'              // Protezione CSRF
            ];
            setcookie("user", $user['username'], $cookie_options);
        }

        // Imposta la variabile di sessione
        $_SESSION['username'] = $user['username'];

        // Reindirizza alla pagina protetta
        header("Location: ../main_page/front-end/logged_Index.php");
    } else {
        // Password non valida
        header("Location: login_register.html?error=invalid_password");
    }
} else {
    // Utente non trovato
    header("Location: login_register.html?error=user_not_found");
}

// 8. CHIUSURA RISORSE
// ----------------------------------------------------------------
$stmt->close();
$conn->close();
