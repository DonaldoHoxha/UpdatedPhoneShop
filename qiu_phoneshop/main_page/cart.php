<?php
session_start();
include 'db_conn.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrello</title>
    <style>
        table,
        th,
        td {
            border: 1px black solid;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <?php

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
    $stmt = $conn->prepare("SELECT p.name, c.quantity, p.price  FROM product p JOIN cart c on p.id = c.product_id WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    //simple print to be updated
    echo "<table>";
    echo "<tr>";
    echo "<th>Prodotto</th>";
    echo "<th>Quantità</th>";
    echo "<th>Prezzo</th>";
    echo "</tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['quantity'] . "</td>";
        echo "<td>" . $row['price'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    // printing the total price of the cart
    $stmt = $conn->prepare("SELECT SUM(p.price * c.quantity) as total FROM cart c join product p on c.product_id = p.id where c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo "<p>Totale: " . $row['total'] . "</p>";
    }
    ?>
    <a href="logged_Index.php"> Continua ad acquistare</a>
</body>

</html>
