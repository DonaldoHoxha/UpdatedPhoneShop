<?php
// We check if the user has logged in 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const profileContainer = document.querySelector('.user-btn');
            const profileOptions = document.querySelector('.profile-options');
            let hoverTimeout;
            // Show on hover
            profileContainer.addEventListener('mouseenter', () => {
                clearTimeout(hoverTimeout);
                profileOptions.classList.add('show');
            });
            // Hide with delay
            profileContainer.addEventListener('mouseleave', () => {
                hoverTimeout = setTimeout(() => {
                    profileOptions.classList.remove('show');
                }, 300);
            });
            // Keep open if hovering over options
            profileOptions.addEventListener('mouseenter', () => {
                clearTimeout(hoverTimeout);
                profileOptions.classList.add('show');
            })
            profileOptions.addEventListener('mouseleave', () => {
                hoverTimeout = setTimeout(() => {
                    profileOptions.classList.remove('show');
                }, 200);
            });
            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!profileContainer.contains(e.target)) {
                    profileOptions.classList.remove('show');
                }
            });
        });
        // Add an item to the cart 
        function addItem(productId) {
            fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'product_id=' + encodeURIComponent(productId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        // update the cart items count
                        let cartCount = document.querySelector('.cart-count');
                        cartCount.textContent = parseInt(cartCount.textContent) + 1;
                    } else {
                        alert("Errore: " + data.message);
                    }
                })
                .catch(error => console.error('Errore:', error));
        }
        //change the add-to-cart's icon status to checked after the click, and return to before when mouse left
        const add_to_cart = document.querySelector('.add-to-cart');
        const icon = add_to_cart.querySelector('i');
        const originalIconClass = 'fa-cart-plus';
        const clickedIconClass = 'fa-check-circle';

        add_to_cart.addEventListener('click', () => {

            icon.classList.remove(originalIconClass);
            icon.classList.add(clickedIconClass);

        });

        add_to_cart.addEventListener('mouseleave', () => {

            icon.classList.remove(clickedIconClass);
            icon.classList.add(originalIconClass);

        });
    </script>
</head>

<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-top">
            <div class="logo-container">
                <img src="logo.png" alt="TechPhone Logo" class="logo">
                <h1>TechPhone</h1>
            </div>
            <div class="search-container">
                <input type="search" placeholder="Cerca smartphone..." class="search-bar">
                <button class="search-btn"><i class="fas fa-search"></i></button>
            </div>
            <div class="user-actions">
                <a href="../login&register/logout.php"><button class="logout">LogOut</button></a>
                <button class="user-btn"><i class="fas fa-user">
                        <div class="proflie-img">
                            <div class="profile-options">
                                <a href="/profile" class="profile-link">Profile</a>
                                <a href="/orders" class="profile-link">Orders</a>
                                <a href="/settings" class="profile-link">Settings</a>
                                <a href="../login&register/logout.php" class="profile-link">Logout</a>
                            </div>
                        </div>
                    </i></button>
                <a href="cart.php">
                    <button class="cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">
                            <?php
                            include 'db_conn.php';
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }
                            // Ottieni l'ID dell'utente
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
                            $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?;");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            if ($row['total'] > 0) {
                                echo $row['total'];
                            } else {
                                echo "0";
                            }
                            ?>
                        </span></button></a>
            </div>
        </div>

        <nav class="main-nav">
            <ul class="nav-list">
                <li><a href="#novita">Novità</a></li>
                <li><a href="#brand">Brand</a></li>
                <li><a href="#offerte">Offerte</a></li>
                <li><a href="#usato">Usato Certificato</a></li>
                <li><a href="#assistenza">Assistenza</a></li>
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
            include 'db_conn.php';
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $stmt = $conn->prepare("SELECT * FROM product");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {

                echo "<article class='product-card'>";
                echo "<div class='product-info'>";
                echo "<h3>" . $row['name'] . "</h3>";
                echo "<p class='product-price'>€" . $row['price'] . "</p>";
                echo "<div class='product-actions'>";
                echo "<button class='quick-view'><i class='fas fa-eye'></i></button>";

                echo "<button class='add-to-cart' onclick='addItem(" . $row['id'] . ")'><i class='fas fa-cart-plus'></i></button>";

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
