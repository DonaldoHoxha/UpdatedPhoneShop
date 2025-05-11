<?php
// Avvia la sessione (se non è già stata avviata)
session_start();

// Rimuove tutte le variabili di sessione
session_unset();

// Distrugge completamente la sessione
// - Elimina i dati dal server
// - Invalida il cookie di sessione lato client
session_destroy();

// Itera su tutti i cookie presenti
foreach ($_COOKIE as $key => $value) {
    // Imposta ogni cookie con:
    // - Valore vuoto
    // - Tempo di scadenza nel passato (per rimuoverlo)
    // - Percorso root (per assicurarsi di cancellarlo da tutto il sito)
    setcookie($key, '', time() - 3600, '/');
    
    // Rimuove il cookie dall'array $_COOKIE
    unset($_COOKIE[$key]);
}

// Reindirizza l'utente alla pagina principale
header("Location: ../main_page/front-end/index.php");

// Termina l'esecuzione dello script
exit();