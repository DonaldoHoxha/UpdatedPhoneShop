<?php
// start la session
session_start();
include '../back-end/db_conn.php';
// controllo se nella sessione è stato salvato il nome utente,se non c'è, reindirizzo alla pagina di login

if (!isset($_SESSION['username'])) {
    header('Location: ../../login_register_user/login_register.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechPhone - I tuoi ordini</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="orders.css">
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
                <a href="../../login_register_user/logout.php"><button class="logout">Logout</button></a>
                <button class="user-btn">
                    <i class="fas fa-user">
                        <div class="proflie-img">
                            <div class="profile-options">
                                <a href="profile.php" class="profile-link">Profile</a>
                                <a href="orders.php" class="profile-link">Orders</a>
                                <a href="/settings" class="profile-link">Setting</a>
                                <a href="../../login_register_user/logout.php" class="profile-link">Logout</a>
                            </div>
                        </div>
                    </i>
                </button>
                <a href="cart.php">
                    <button class="cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">
                            <?php
                            // Otteniamo l'id dell'utente  connesso
                            $username = $_SESSION['username'];
                            $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
                            $stmt->bind_param("s", $username);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user = $result->fetch_assoc();
                            $user_id = $user['id'];
                            // Otteniamo il numero totale di prodotti nel carrello
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

    <main class="orders-container">
        <h1 class="orders-title">I tuoi ordini</h1>

        <?php
        // Otteniamo l'id dell'utente connesso
        $username = $_SESSION['username'];
        $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        // Configurazione paginazione
        $orders_per_page = 7; // Numero di ordini per pagina
        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($current_page < 1) $current_page = 1;

        // Conta il numero totale di ordini
        $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
        $count_stmt->bind_param("i", $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $total_orders = $count_result->fetch_assoc()['total'];

        // Calcola il numero totale di pagine
        $total_pages = ceil($total_orders / $orders_per_page);
        if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;

        // Modifica la query per includere LIMIT e OFFSET
        // LIMIT è il numero di ordini per pagina
        // OFFSET è il numero di ordini da saltare
        $stmt = $conn->prepare("SELECT 
                        o.id as order_id,
                        DATE_FORMAT(o.order_date, '%d/%m/%Y %H:%i') as formatted_date,
                        o.quantity, 
                        o.total_price, 
                        p.name
                    FROM orders o
                    JOIN product p ON p.id = o.product_id
                    WHERE user_id = ? 
                    ORDER BY o.order_date DESC
                    LIMIT ? OFFSET ?");
        $offset = ($current_page - 1) * $orders_per_page;
        $stmt->bind_param("iii", $user_id, $orders_per_page, $offset);
        $stmt->execute();
        $result = $stmt->get_result();


        if ($result->num_rows > 0): ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Data ordine</th>
                        <th>Prodotto</th>
                        <th>Quantità</th>
                        <th>Prezzo totale</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Ciclo per stampare gli ordini -->
                    <!-- Questo è una funzionalità del php, che permette di usare direttamente i tag del html,senza echo/print -->
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="order-date"><?= htmlspecialchars($row['formatted_date']) ?></td>
                            <td class="product-name"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="order-quantity"><?= $row['quantity'] ?></td>
                            <td class="order-price">€<?= number_format($row['total_price'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php if ($result->num_rows > 0 && $total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?= $current_page - 1 ?>" class="page-link page-nav">
                            <i class="fas fa-chevron-left"></i> Precedente
                        </a>
                    <?php else: ?>
                        <span class="page-link page-nav" disabled>
                            <i class="fas fa-chevron-left"></i> Precedente
                        </span>
                    <?php endif; ?>

                    <?php
                    // Mostra i numeri di pagina
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);

                    if ($start_page > 1): ?>
                        <a href="?page=1" class="page-link">1</a>
                        <?php if ($start_page > 2): ?>
                            <span class="page-dots">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <a href="?page=<?= $i ?>" class="page-link <?= $i == $current_page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <span class="page-dots">...</span>
                        <?php endif; ?>
                        <a href="?page=<?= $total_pages ?>" class="page-link"><?= $total_pages ?></a>
                    <?php endif; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?= $current_page + 1 ?>" class="page-link page-nav">
                            Successiva <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="page-link page-nav" disabled>
                            Successiva <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-orders">
                <div class="no-orders-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h2 class="no-orders-message">Non hai ancora effettuato ordini</h2>
            </div>
        <?php endif; ?>

        <a href="logged_Index.php" class="continue-shopping">
            <i class="fas fa-arrow-left"></i> Continua lo shopping
        </a>
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
</body>

</html>