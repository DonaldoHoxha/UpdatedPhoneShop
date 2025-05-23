<?php
//inizio la sessione
session_start();
include '../back-end/db_conn.php';
// controllo se la sessione è presente username dell'utente
// se non c'è, reindirizzo alla pagina di login
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
    <title>TechPhone - Profilo Utente</title>
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        function showDeleteModal(e) {
            e.preventDefault();
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        function deleteAccount() {
            //window.location.href = '../back-end/delete_account.php';
            fetch('../back-end/delete_account.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Account eliminato con successo');
                        window.location.href = '../../login_register_user/logout.php';
                    } else {
                        alert('Errore durante l\'eliminazione dell\'account');
                    }
                })
        }
    </script>
</head>

<body>
    <?php
    // ottengo username dell'utente dalla sessione
    $username = $_SESSION['username'];
    // informazioni dell'utente
    $stmt = $conn->prepare("SELECT id, username, email, shipping_address FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // se l'utente non esiste, reindirizzo alla pagina principale
    if (!$user) {
        echo "<script>alert('Utente non trovato'); window.location.href='logged_index.php';</script>";
        exit();
    }

    $user_id = $user['id'];

    // ottengo il numero totale di ordini dell'utente
    $stmt = $conn->prepare("SELECT COUNT(*) as number_of_orders FROM orders WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $orders_result = $stmt->get_result();
    $orders_data = $orders_result->fetch_assoc();
    ?>

    <div class="profile-container">
        <div class="profile-header">
            <h1>Il mio profilo</h1>
            <a href="logged_index.php" class="back-to-shop">Torna allo shopping</a>
        </div>

        <div class="profile-content">
            <div class="profile-sidebar">
                <img src="default-avatar.jpg" class="profile-avatar">
                <ul class="profile-nav">
                    <li><a href="#" class="active"><i class="fas fa-user"></i> Profilo</a></li>
                    <li><a href="orders.php"><i class="fas fa-box"></i> Ordini</a></li>
                    <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Carrello</a></li>
                    <li><a href="../../login_register_user/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <li><a href="#" onclick="showDeleteModal(event)" class="delete"><i class="fas fa-trash"></i>Elimina account</a></li>
                </ul>
            </div>

            <div class="profile-main">
                <div class="profile-section">
                    <h2>Informazioni personali</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nome utente</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Indirizzo di spedizione</span>
                            <span class="info-value">
                                <?php
                                if (!empty($user['shipping_address'])) {
                                    echo htmlspecialchars($user['shipping_address']);
                                } else {
                                    echo "Nessun indirizzo registrato";
                                }
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ordini totali</span>
                            <span class="info-value"><?php echo $orders_data['number_of_orders']; ?></span>
                        </div>
                    </div>
                    <button class="edit-btn" onclick="location.href='edit_profile.php'">Modifica profilo</button>
                </div>

                <div class="profile-section orders-summary">
                    <h2>Ultimi ordini</h2>
                    <?php
                    // ottengo gli ultimi 3 ordini dell'utente
                    $stmt = $conn->prepare("SELECT id, order_date, total_price FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 3");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $recent_orders = $stmt->get_result();

                    if ($recent_orders->num_rows > 0) {
                        while ($order = $recent_orders->fetch_assoc()) {
                            echo '<div class="order-card">';
                            echo '<div class="order-header">';
                            echo '<span>Ordine #' . $order['id'] . '</span>';
                            //Prende una stringa di data/ora (es. "2023-12-25 14:30:00").
                            //La converte in un timestamp numerico (utile per calcoli o formattazioni).
                            echo '<span class="order-date">' . date('d/m/Y', strtotime($order['order_date'])) . '</span>';
                            echo '</div>';
                            echo '<div class="order-details">';
                            echo '<div><span class="info-label">Totale</span><span>€' . number_format($order['total_price'], 2) . '</span></div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>Nessun ordine effettuato</p>';
                    }
                    ?>
                    <?php if ($orders_data['number_of_orders'] > 3) : ?>
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="orders.php" style="color: var(--secondary-color); text-decoration: none;">Visualizza tutti gli ordini →</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="delete-modal-overlay" id="deleteModal" style="display: none;">
        <div class="delete-modal">
            <h3>Conferma eliminazione</h3>
            <p>Sei sicuro di voler eliminare il tuo account? Questa azione è irreversibile.</p>
            <div class="modal-buttons">
                <button class="modal-confirm" onclick="deleteAccount()">Conferma</button>
                <button class="modal-cancel" onclick="closeDeleteModal()">Annulla</button>
            </div>
        </div>
    </div>
</body>

</html>