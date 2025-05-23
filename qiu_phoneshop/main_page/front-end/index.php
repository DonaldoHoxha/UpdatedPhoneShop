<?php
//start la session
session_start();
//controllo se nella sessione è stato salvato il nome utente
if (!isset($_SESSION['username'])) {
    // controllo se c'è nel cookie
    if (isset($_COOKIE['user'])) {
        // se c'è nel cookie, la uso per creare la sessione
        $_SESSION['username'] = $_COOKIE['user'];
        // e reindirizzo alla pagina principale
        header("Location: logged_Index.php");
    }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechPhone - Negozio di Smartphone</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="script.js"></script>
</head>

<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-top">
            <a href="index.php">
                <div class="logo-container">
                    <img src="logo.png" alt="TechPhone Logo" class="logo">
                    <h1>TechPhone</h1>
                </div>
            </a>

            <div class="search-container">
                <input type="search" placeholder="Cerca smartphone..." class="search-bar">
                <button class="search-btn"><i class="fas fa-search"></i>
                </button>
            </div>
            <div class="user-actions">
                <div class="login-register">
                    <a href="../../login_register_user/login_register.html"><button class="login">Login</button></a>
                    <a href="../../login_register_user/login_register.html"><button class="register">SignUp</button></a>
                </div>
                <a href="../../login_register_user/login_register.html">
                    <button class="user-btn"><i class="fas fa-user"></i></button>
                </a>
                <a href="../../login_register_user/login_register.html">
                    <button class="cart-btn"><i class="fas fa-shopping-cart"></i><span class="cart-count">0</span></button>
                </a>
            </div>
        </div>
        <nav class="main-nav">
            <ul class="nav-list">
                <li><a href="#novita">Novità</a></li>
                <li><a href="#brand">Brand</a></li>
                <li><a href="#offerte">Offerte</a></li>
                <li><a href="#usato">Usato Certificato</a></li>
                <li><a href="#assistenza">Assistenza</a></li>
                <li><a href="../../login_register_admin/front-end/admin_login.html">Diventa un venditore</a></li>
            </ul>
        </nav>
    </header>
    <!-- Main Content -->
    <main class="content">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h2>Ultimi Modelli 2024</h2>
                <p>Scopri le novità dei principali brand</p>
                <button class="cta-btn">Scopri ora</button>
            </div>
        </section>
        <!-- Products Grid -->
        <section class="products-grid">

            <?php
            include '../back-end/db_conn.php';
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            // Query per ottenere i prodotti
            $stmt = $conn->prepare("SELECT p.name,p.price FROM product p
                                            JOIN administrator_user a ON p.fk_admin = a.id
                                            WHERE a.deleted_at IS NULL");
            $stmt->execute();
            $result = $stmt->get_result();
            // Controllo se ci sono risultati
            while ($row = $result->fetch_assoc()) {
                // Stampo i prodotti
                echo "<article class='product-card'>";
                echo "<div class='product-info'>";
                echo "<h3>" . $row['name'] . "</h3>";
                echo "<p class='product-price'>€" . $row['price'] . "</p>";
                echo "<div class='product-actions'>";
                echo "<a href='../../login_register_user/login_register.html'><button class='quick-view'><i class='fas fa-eye'></i></button></a>";
                echo "<a href='../../login_register_user/login_register.html'><button class='add-to-cart'><i class='fas fa-cart-plus'></i></button></a>";
                echo "</div>";
                echo "</div>";
                echo "</article>";
            }
            ?>


            <!-- Altri prodotti... -->
        </section>
    </main>

    <!-- Footer -->
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