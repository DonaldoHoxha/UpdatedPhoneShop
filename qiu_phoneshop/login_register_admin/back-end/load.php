<?php
session_start();
header('Content-Type: application/json');
include '../../main_page/back-end/db_conn.php';
// get the user id from the session
if (!isset($_SESSION['admin_user'])) {
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
    case 'analytics':
        analytics($conn);
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
    $user = $_SESSION['admin_user'];
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

    // Configurazione paginazione
    $orders_per_page = 10;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) $current_page = 1;

    // Conta ordini totali
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders o 
                                 JOIN product p ON o.product_id = p.id 
                                 WHERE p.fk_admin = ?");
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_orders = $count_result->fetch_assoc()['total'];

    $total_pages = ceil($total_orders / $orders_per_page);
    if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;

    $offset = ($current_page - 1) * $orders_per_page;

    $query = "SELECT o.id as orderID, u.id as userID, u.username, p.id as productID, p.name,
                     o.order_date, o.quantity, o.total_price, o.shipping_address 
              FROM orders o 
              JOIN product p ON o.product_id = p.id
              JOIN user u ON o.user_id = u.id 
              WHERE p.fk_admin = ?
              ORDER BY o.order_date DESC
              LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $orders_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'orders' => $orders,
        'pagination' => [
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'total_orders' => $total_orders
        ]
    ]);
}

function loadProducts($conn)
{
    $user = $_SESSION['admin_user'];
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

    // Configurazione paginazione
    $products_per_page = 10;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) $current_page = 1;

    // Conta il numero totale di prodotti
    $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM product WHERE fk_admin = ?");
    $count_stmt->bind_param("i", $user_id);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_products = $count_result->fetch_assoc()['total'];

    // Calcola il numero totale di pagine
    $total_pages = ceil($total_products / $products_per_page);
    if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;

    $offset = ($current_page - 1) * $products_per_page;

    $query = "SELECT p.id, p.name, p.brand, p.price, p.quantity, p.ram, p.rom, p.camera, p.battery 
              FROM product p 
              WHERE p.fk_admin = ?
              ORDER BY p.id ASC
              LIMIT ? OFFSET ?";

    $products = [];
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $products_per_page, $offset);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Errore nel preparare la query"]);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);

    $response = [
        'products' => $products,
        'pagination' => [
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'total_products' => $total_products
        ]
    ];

    echo json_encode($response);
}
function analytics($conn) {
    // Get admin ID from session
    $user = $_SESSION['admin_user']; 
    
    // First get the admin ID from the name
    $adminStmt = $conn->prepare("SELECT id FROM administrator_user WHERE name = ?");
    $adminStmt->bind_param("s", $user);
    $adminStmt->execute();
    $result = $adminStmt->get_result();
    $admin = $result->fetch_assoc();
    
    if (!$admin) {
        echo json_encode(["status" => "error", "message" => "Admin not found"]);
        exit();
    }
    
    $admin_id = $admin['id'];
    
    // Get total sales
    $stmtSales = $conn->prepare("SELECT SUM(o.total_price) as total
                           FROM orders o 
                           JOIN product p ON o.product_id = p.id 
                           WHERE p.fk_admin = ?");
    $stmtSales->bind_param("i", $admin_id);
    $stmtSales->execute();
    $result = $stmtSales->get_result();
    
    // Fetch the result properly
    $row = $result->fetch_assoc();
    $totalSales = $row['total'] ?? 0;

    // Get total searches
    $stmtSearches = $conn->prepare("SELECT COUNT(*) as total
                           FROM searches s
                           JOIN product p ON s.product_id = p.id 
                           WHERE p.fk_admin = ?");
    $stmtSearches->bind_param("i", $admin_id);
    $stmtSearches->execute();
    $result = $stmtSearches->get_result();
    
    // Fetch the result properly
    $row = $result->fetch_assoc();
    $totalSearches = $row['total'] ?? 0;
    
    echo json_encode([
        'totalSales' => (float)$totalSales,
        'totalSearches' => (int)$totalSearches,
        'status' => 'success'
    ]);
}

        