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
    <style>
        /* Stili specifici per il carrello */
        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .cart-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
            font-size: 2.2rem;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .cart-table th,
        .cart-table td {
            padding: 1.2rem;
            text-align: center;
            border-bottom: 1px solid var(--light-gray);
        }

        .cart-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .cart-table tr:nth-child(even) {
            background-color: rgba(0, 184, 148, 0.05);
        }

        .cart-table tr:hover {
            background-color: rgba(9, 132, 227, 0.1);
        }

        .product-name {
            font-weight: 600;
            color: var(--primary-color);
        }

        .product-price {
            color: var(--accent-color);
            font-weight: 700;
        }

        .product-quantity {
            font-weight: 500;
        }

        .action-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.6rem 1rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.2rem;
            font-size: 0.9rem;
        }

        .action-btn:hover {
            background-color: #0877c7;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .action-btn i {
            margin-right: 5px;
        }

        .remove-btn {
            background-color: #e74c3c;
        }

        .remove-btn:hover {
            background-color: #c0392b;
        }

        .cart-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .total-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--accent-color);
        }

        .total-label {
            font-weight: 600;
            color: var(--primary-color);
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .continue-btn,
        .checkout-btn {
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .continue-btn {
            background-color: white;
            color: var(--secondary-color);
            border: 2px solid var(--secondary-color);
        }

        .continue-btn:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        .checkout-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
        }

        .checkout-btn:hover {
            background-color: #01a584;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 184, 148, 0.3);
        }

        .empty-cart {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .empty-cart-icon {
            font-size: 3rem;
            color: var(--dark-gray);
            margin-bottom: 1rem;
        }

        .empty-cart-message {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {

            .cart-table th,
            .cart-table td {
                padding: 0.8rem;
                font-size: 0.9rem;
            }

            .action-btn {
                padding: 0.5rem 0.8rem;
                font-size: 0.8rem;
            }

            .cart-summary {
                flex-direction: column;
                align-items: flex-end;
                gap: 1rem;
            }

            .cart-actions {
                flex-direction: column;
            }

            .continue-btn,
            .checkout-btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .cart-container {
                padding: 0 1rem;
            }

            .cart-title {
                font-size: 1.8rem;
            }

            .cart-table {
                font-size: 0.8rem;
            }

            .action-btn {
                padding: 0.4rem 0.6rem;
            }

            .total-price {
                font-size: 1.2rem;
            }
        }
    </style>
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