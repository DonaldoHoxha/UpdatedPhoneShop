<?php
header('Content-Type: application/json');
include '../../main_page/back-end/db_conn.php';

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
    $query = "SELECT o.id as orderID,u.id as userID,u.username,p.id as productID,p.name,
                    o.order_date,o.quantity,o.total_price,o.shipping_address FROM orders o 
                    join product p on o.product_id = p.id
                    join user u on o.user_id = u.id order by o.order_date desc;";
    $orders = [];
    $stmt = $conn->prepare($query);
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
    $query = "SELECT p.id, p.name,p.brand,p.price,p.quantity FROM product p";
    $products = [];
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Errore nel preparare la query"]);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    echo json_encode($products);

    $crud = $_POST['crud'] ?? '';
    switch ($crud) {
        case 'add':
            addProduct($conn);
            break;
        case 'update':
            //updateProduct($conn);
            break;
        case 'delete':
            //deleteProduct($conn);
            break;
        default:
            echo json_encode(["status" => "error", "message" => "Azione non valida"]);
            break;
    }
}

// crud funtions for products

function addProduct($conn){
    $name = $_POST['name'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $ram = $_POST['ram'];
    $rom = $_POST['rom'];
    $battery = $_POST['battery'];
    $camera = $_POST['camera'];

    
}
