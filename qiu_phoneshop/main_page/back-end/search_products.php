<?php
// Start the session at the beginning of the file
session_start();

include 'db_conn.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set content type to JSON
header('Content-Type: application/json');

// We check if there is a query in the search
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search_query = $_GET['query'];
    $stmt = $conn->prepare("SELECT * FROM product WHERE name LIKE ?");
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("s", $search_param);

    // Add the search to the searches table if user is logged in
    if (isset($_SESSION['username'])) {
        // First get the user's ID from their username
        $username = $_SESSION['username'];
        $user_id_stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
        $user_id_stmt->bind_param("s", $username);
        $user_id_stmt->execute();
        $user_result = $user_id_stmt->get_result();
        
        if ($user_row = $user_result->fetch_assoc()) {
            $user_id = $user_row['id']; // Get the actual user ID
            
            // Get all matching product IDs
            $id_stmt = $conn->prepare("SELECT id FROM product WHERE name LIKE ?");
            $id_stmt->bind_param("s", $search_param);
            $id_stmt->execute();
            $product_result = $id_stmt->get_result();
            
            // Insert search records
            $insert_stmt = $conn->prepare("INSERT INTO searches (user_id, product_id) VALUES (?, ?)");
            while ($product = $product_result->fetch_assoc()) {
                $insert_stmt->bind_param("ii", $user_id, $product['id']);
                if (!$insert_stmt->execute()) {
                    error_log("Failed to track search: " . $conn->error);
                }
            }
            
            // Close statements
            $insert_stmt->close();
            $id_stmt->close();
        }
        $user_id_stmt->close();
    }
} else {
    // If there isn't a query, we return all products
    $stmt = $conn->prepare("SELECT * FROM product");
}

$stmt->execute();
$result = $stmt->get_result();

// We prepare the query to return in JSON 
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Return the products
echo json_encode($products);

// Close the connection
$stmt->close();
$conn->close();