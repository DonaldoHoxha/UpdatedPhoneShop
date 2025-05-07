<?php
include '../../main_page/back-end/db_conn.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'users':
        loadUsers($conn);
        break;
    case 'products':
        loadProducts($conn);
        break;
    case 'orders':
        loadOrders($conn);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Azione non valida"]);
        break;
}

function loadUsers($conn) {
    $query = "SELECT id, username, email, 
    shipping_address,registration_date FROM user";
    $users = [];
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Errore nel preparare la query"]);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    echo json_encode($users);
}

function loadOrders($conn) {
    $query = "SELECT id, username, email, 
    shipping_address,registration_date FROM user";
    $users = [];
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Errore nel preparare la query"]);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all();

    $stmt->close();
    echo json_encode($users);
}

function loadProducts($conn) {
    $query = "SELECT id, name, price, description, image FROM product";
    $products = [];
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Errore nel preparare la query"]);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all();

    $stmt->close();
    echo json_encode($products);
}