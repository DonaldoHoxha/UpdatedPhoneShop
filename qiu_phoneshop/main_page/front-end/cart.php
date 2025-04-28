<?php
session_start();
include '../back-end/db_conn.php';
if (!isset($_SESSION['username'])) {
    header('Location: ../../login&register/login&register.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechPhone - Carrello</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="cart.css">
    <script src="script.js"></script>
</head>

<body>
    <!-- Header (uguale alla pagina principale) -->
    <header class="main-header">
        <div class="header-top">
            <div class="logo-container">
                <img src="logo.png" alt="TechPhone Logo" class="logo">
                <h1>TechPhone</h1>
            </div>
            <div class="search-container">
                <input type="search" id="search-input" placeholder="Cerca smartphone..." class="search-bar">
                <button id="search-btn" class="search-btn"><i class="fas fa-search"></i></button>
            </div>
            <div class="user-actions">
                <a href="../../login&register/logout.php"><button class="logout">Logout</button></a>
                <button class="user-btn"><i class="fas fa-user">
                        <div class="proflie-img">
                            <div class="profile-options">
                                <a href="profile.php" class="profile-link">Profilo</a>
                                <a href="orders.php" class="profile-link">Ordini</a>
                                <a href="/settings" class="profile-link">Impostazioni</a>
                                <a href="../../login&register/logout.php" class="profile-link">Logout</a>
                            </div>
                        </div>
                    </i></button>
                <a href="cart.php">
                    <button class="cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">
                            <?php
                            $username = $_SESSION['username'];
                            $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
                            $stmt->bind_param("s", $username);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user = $result->fetch_assoc();
                            $user_id = $user['id'];
                            $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?;");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            echo $row['total'] > 0 ? $row['total'] : "0";
                            ?>
                        </span>
                    </button>
                </a>
            </div>
        </div>

        <nav class="main-nav">
            <ul class="nav-list">
                <li><a href="logged_Index.php#novita">Novità</a></li>
                <li><a href="logged_Index.php#brand">Brand</a></li>
                <li><a href="logged_Index.php#offerte">Offerte</a></li>
                <li><a href="logged_Index.php#usato">Usato Certificato</a></li>
                <li><a href="logged_Index.php#assistenza">Assistenza</a></li>
            </ul>
        </nav>
    </header>

    <main class="cart-container">
        <h1 class="cart-title">Il tuo carrello</h1>

        <?php
        // Get the user ID
        $username = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Get cart items
        $stmt = $conn->prepare("SELECT c.product_id, p.name, c.quantity, p.price FROM product p JOIN cart c ON p.id = c.product_id WHERE c.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Prodotto</th>
                        <th>Quantità</th>
                        <th>Prezzo unitario</th>
                        <th>Totale</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grand_total = 0;
                    while ($row = $result->fetch_assoc()):
                        $total = $row['price'] * $row['quantity'];
                        $grand_total += $total;
                    ?>
                        <tr>
                            <td class="product-name"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="product-quantity"><?= $row['quantity'] ?></td>
                            <td class="product-price">€<?= number_format($row['price'], 2) ?></td>
                            <td class="product-price">€<?= number_format($total, 2) ?></td>
                            <td>
                                <button onclick="buyItem(<?= $row['product_id'] ?>)" class="action-btn">
                                    <i class="fas fa-shopping-bag"></i> Acquista
                                </button>
                                <button onclick="removeItem(<?= $row['product_id'] ?>)" class="action-btn remove-btn">
                                    <i class="fas fa-trash-alt"></i> Rimuovi
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <span class="total-label">Totale carrello:</span>
                <span class="total-price">€<?= number_format($grand_total, 2) ?></span>
            </div>

            <div class="cart-actions">
                <a href="logged_Index.php" class="continue-btn">
                    <i class="fas fa-arrow-left"></i> Continua lo shopping
                </a>
                <button onclick="buyCart()" class="checkout-btn">
                    <i class="fas fa-credit-card"></i> Procedi all'acquisto
                </button>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2 class="empty-cart-message">Il tuo carrello è vuoto</h2>
                <a href="logged_Index.php" class="continue-btn">
                    <i class="fas fa-arrow-left"></i> Torna allo shopping
                </a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer (uguale alla pagina principale) -->
    <footer class="main-footer">
        <div class="footer-grid">
            <div class="footer-section">
                <h4>Contatti</h4>
                <ul>
                    <li><i class="fas fa-phone"></i> +39 02 1234567</li>
                    <li><i class="fas fa-envelope"></i> info@techphone.it</li>
                    <li><i class="fas fa-map-marker-alt"></i> Milano, Via Roma 123</li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Servizi</h4>
                <ul>
                    <li>Garanzia Estesa</li>
                    <li>Ritiro Gratuito</li>
                    <li>Finanziamenti</li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Social</h4>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Function to buy a singular item from the cart
        function buyItem(productId) {
            fetch('../back-end/buy_item.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'product_id=' + encodeURIComponent(productId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        location.reload();
                    } else {
                        alert("Errore: " + data.message);
                    }
                })
                .catch(error => console.error('Errore: ', error));
        }

        // Function to remove an item from the cart
        function removeItem(productId) {
            fetch('../back-end/remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'product_id=' + encodeURIComponent(productId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        location.reload();
                    } else {
                        alert("Errore: " + data.message);
                    }
                })
                .catch(error => console.error('Errore:', error));
        }

        // Function to buy all the items in the cart
        function buyCart() {
            fetch('../back-end/buy_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        location.reload();
                    } else {
                        alert("Errore: " + data.message)
                    }
                })
                .catch(error => console.error('Errore:', error));
        }
    </script>
</body>

</html>