<?php
session_start();
include 'db_conn.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user ID
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    echo json_encode(["status" => "error", "message" => "Utente non trovato"]);
    exit();
}
$user_id = $user['id'];

// We get the name and the quantity of the items in the cart
$stmt = $conn->prepare("SELECT w.name, c.quantity FROM product w JOIN cart c on w.id = c.product_id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

//simple print to be updated
echo "<table>";
echo "<tr>";
echo "<th>Prodotto</th>";
echo "<th>Quantità</th>";
echo "</tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['quantity'] . "</td>";
    echo "</tr>";
}
