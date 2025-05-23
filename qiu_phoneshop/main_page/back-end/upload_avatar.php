<?php
session_start();
include 'db_conn.php';

if (!isset($_SESSION['username'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Accesso non autorizzato");
}

$username = $_SESSION['username'];
$uploadDir = '../user_avatar/';
$maxSize = 2 * 1024 * 1024; // 2MB
$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

// Verifica se è stato inviato un file
if (empty($_FILES['avatar']['tmp_name'])) {
    header("Location: ../front-end/profile.php?error=no_file");
    exit;
}

// Validazione del file

$fileInfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $fileInfo->file($_FILES['avatar']['tmp_name']);

if (!in_array($mimeType, $allowedTypes)) {
    header("Location: ../front-end/profile.php?error=invalid_type");
    exit;
}

if ($_FILES['avatar']['size'] > $maxSize) {
    header("Location: ../front-end/profile.php?error=file_too_big");
    exit;
}

// Genera nome file univoco
$extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
$safeFilename = $_SESSION['username'] . '.' . $extension;
$destination = $uploadDir . $safeFilename;

// Sposta il file caricato
if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
    
    // Elimina il vecchio avatar se non è quello di default
    $stmt = $conn->prepare("SELECT avatar_path FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldAvatar = $result->fetch_assoc()['avatar_path'];
    
    if ($oldAvatar && $oldAvatar !== 'default-avatar.jpg') {
        $oldPath = $uploadDir . $oldAvatar;
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }

    // Aggiorna il database
    $stmt = $conn->prepare("UPDATE user SET avatar_path = ? WHERE username = ?");
    $stmt->bind_param("ss", $safeFilename, $username);
    
    if ($stmt->execute()) {
        header("Location: ../front-end/profile.php?success=avatar_updated");
    } else {
        error_log("Database error: " . $conn->error);
        header("Location: ../front-end/profile.php?error=db_error");
    }
} else {
    header("Location: ../front-end/profile.php?error=upload_failed");
}

exit;