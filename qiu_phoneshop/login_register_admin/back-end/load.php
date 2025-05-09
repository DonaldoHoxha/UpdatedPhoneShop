<?php
session_start();
header('Content-Type: application/json');
include '../../main_page/back-end/db_conn.php';
// get the user id from the session
if (!isset($_SESSION['user'])) {
    echo json_encode(["status" => "error", "message" => "Utente non autenticato"]);
    exit();
}
$action = $_GET['action'] ?? '';
if (empty($action)) {
    echo json_encode(["status" => "error", "message" => "Azione non valida"]);
    exit();
}
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

function loadUsers($conn)
{
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

function loadOrders($conn)
{

    $user = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT id FROM administrator_user WHERE name = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    if (!$admin) {
        echo json_encode(["status" => "error", "message" => "Utente non trovato"]);
        exit();
    }
    $user_id = $admin['id'];
    $query = "SELECT o.id as orderID,u.id as userID,u.username,p.id as productID,p.name,
                    o.order_date,o.quantity,o.total_price,o.shipping_address FROM orders o 
                    join product p on o.product_id = p.id
                    join user u on o.user_id = u.id 
                    where p.fk_admin = ?
                    order by o.order_date desc;";
    $orders = [];
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Errore nel preparare la query"]);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    echo json_encode($orders);
}

function loadProducts($conn)
{
    $user = $_SESSION['user'];
    $stmt = $conn->prepare("SELECT id FROM administrator_user WHERE name = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    if (!$admin) {
        echo json_encode(["status" => "error", "message" => "Utente non trovato"]);
        exit();
    }
    $user_id = $admin['id'];
    $query = "SELECT p.id, p.name,p.brand,p.price,p.quantity FROM product p WHERE p.fk_admin = ?
                    order by p.name asc;";
    $products = [];
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Errore nel preparare la query"]);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    echo json_encode($products);
}

// crud funtions for products
