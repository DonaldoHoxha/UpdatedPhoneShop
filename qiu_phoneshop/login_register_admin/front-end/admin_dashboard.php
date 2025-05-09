<?php
session_start();
// Check if the admin user is logged in via session
if (!isset($_SESSION['admin_user'])) {
    // Session doesn't exist, check for admin cookies
    if (isset($_COOKIE['admin_user'])) {
        // Validate the cookie against the database for security
        include '../../main_page/back-end/db_conn.php';
        $stmt = $conn->prepare("SELECT name FROM administrator_user WHERE name = ?");
        if ($stmt) {
            $stmt->bind_param("s", $_COOKIE['admin_user']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                // Cookie is valid, recreate the session
                $_SESSION['admin_user'] = $_COOKIE['admin_user'];
                // Regenerate session ID for security
                session_regenerate_id(true);
                $stmt->close();
            } else {
                // Invalid cookie, clear it and redirect
                setcookie("admin_user", "", time() - 3600, "/");
                header('Location: admin_login.html?error=invalid_cookie');
                exit();
            }
            $conn->close();
        } else {
            // Database error
            header('Location: admin_login.html?error=db_error');
            exit();
        }
    } else {
        // No session or cookies, redirect to login
        header('Location: admin_login.html');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="admin_dashboard.css">
    <title>Administrator Dashboard</title>
</head>

<body>
    <div class="container">

        <!-- Sidebar Section-->
        <aside>
            <div class="toggle">
                <div class="logo">
                    <img src="logo.png" alt="logo">
                    <h2>Phone<span class="danger">Shop</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <span class="material-icons-sharp">
                        close
                    </span>
                </div>
            </div>

            <div class="sidebar">
                <a href="#" class="active" id="dashboard">
                    <span class="material-icons-sharp">
                        dashboard
                    </span>
                    <h3>Dashboard</h3>
                </a>
                <a href="#" id="products">
                    <span class="material-icons-sharp">
                        add
                    </span>
                    <h3>Products</h3>
                </a>

                <a href="#" id="analytics">
                    <span class="material-icons-sharp">
                        insights
                    </span>
                    <h3>Analytics</h3>
                </a>

                <a href="#" id="orders">
                    <span class="material-icons-sharp">
                        inventory
                    </span>
                    <h3>Sale List</h3>
                </a>

                <a href="#" id="users">
                    <span class="material-icons-sharp">
                        person_outline
                    </span>
                    <h3>Users</h3>
                </a>

                <a href="#" id="tickets">
                    <span class="material-icons-sharp">
                        mail_outline
                    </span>
                    <h3>Tickets</h3>
                    <span class="message-count">1</span>
                </a>

                <a href="#" id="reports">
                    <span class="material-icons-sharp">
                        report_gmailerrorred
                    </span>
                    <h3>Reports</h3>
                </a>
                <a href="#" id="settings">
                    <span class="material-icons-sharp">
                        settings
                    </span>
                    <h3>Settings</h3>
                </a>
                <a href="../back-end/logout.php" id="logout">
                    <span class="material-icons-sharp">
                        logout
                    </span>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
        <!--End Sidebar Section-->

        <!-- Main Section-->
        <main>
            <h1>Analytics</h1>
            <!-- Anylises -->
            <div class="analyse">
                <!-- Total Sales-->
                <div class="sales">
                    <div class="status">
                        <div class="info">
                            <h3>Total Sales</h3>
                            <h1>$100000</h1>
                        </div>
                        <div class="progress">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                +81%
                            </div>
                        </div>
                    </div>
                </div>
                <!--Site visits-->
                <div class="visits">
                    <div class="status">
                        <div class="info">
                            <h3>Site visits</h3>
                            <h1>12,345</h1>
                        </div>
                        <div class="progress">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                +12%
                            </div>
                        </div>
                    </div>
                </div>
                <!--Searches-->
                <div class="searches">
                    <div class="status">
                        <div class="info">
                            <h3>Searches</h3>
                            <h1>14,143</h1>
                        </div>
                        <div class="progress">
                            <svg>
                                <circle cx="38" cy="38" r="36"></circle>
                            </svg>
                            <div class="percentage">
                                -2%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--End of Anylises Section-->

            <!--New User Section-->
            <div class="new-users">
                <h2>New Users</h2>
                <div class="user-list">
                    <div class="user">
                        <img src="" alt="">
                        <h2>Tommy</h2>
                        <p>1h ago</p>
                    </div>
                    <div class="user">
                        <img src="" alt="">
                        <h2>Jack</h2>
                        <p>1h 30m ago</p>
                    </div>
                    <div class="user">
                        <img src="" alt="">
                        <h2>John</h2>
                        <p>2h ago</p>
                    </div>
                    <div class="user">
                        <img src="../login_register_admin/img/add_24dp_666666.png">
                        <h2>See more</h2>

                    </div>
                </div>
            </div>
            <!--End of New User Section-->

            <!--Recent Orders Section-->
            <div class="recent-orders">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>OrderID</th>
                            <th>CustomerName</th>
                            <th>ProductName</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include '../../main_page/back-end/db_conn.php';
                        $stmt = $conn->prepare("SELECT id FROM administrator_user WHERE name = ?");
                        $stmt->bind_param("s", $_SESSION['admin_user']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        $admin_id = $row['id'];
                        // Fetch the latest 5 orders from the database
                        $query = "SELECT o.id,u.username,p.name,
                        o.order_date,o.quantity,o.total_price FROM orders o 
                        join product p on o.product_id = p.id
                        join user u on o.user_id = u.id 
                        where p.fk_admin = ?
                        order by o.order_date desc limit 3;";

                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("i", $admin_id);
                        if ($stmt) {
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // Check if there are any results
                            if ($result->num_rows > 0) {
                                // Loop through the results and display them in the table
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                    echo "<td>$" . htmlspecialchars($row['total_price']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['order_date']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No recent orders found.</td></tr>";
                            }
                            $stmt->close();
                        } else {
                            // Handle error in preparing statement
                            echo "<tr><td colspan='6'>Error fetching orders.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <a href="#">Show All</a>
            </div>
        </main>
        <!--End of Main Section-->
        <div id="tempBox" class="tempBox_inactive"></div>
        <!-- Right Section-->
        <div class="right-section">
            <!--Nav Section-->
            <div class="nav">
                <button id="menu-btn">
                    <span class="material-icons-sharp">
                        menu
                    </span>
                </button>
                <div class="dark-mode">
                    <span class="material-icons-sharp active">
                        light_mode
                    </span>
                    <span class="material-icons-sharp">
                        dark_mode
                    </span>
                </div>

                <div class="profile">
                    <div class="info">
                        <p>Hey,
                            <b>
                                <?php
                                echo $_SESSION['admin_user']; // Changed from username to admin_user
                                ?>
                            </b>
                        </p>
                        <small class="text-muted">Admin</small>
                    </div>
                    <div class="profile-photo">
                        <img src="">
                    </div>
                </div>

            </div>
            <!-- End of Nav section-->

            <div class="user-profile">
                <div class="logo">
                    <img src="" alt="">
                    <h2>PhoneShop</h2>
                    <p>We sell phones :)!!</p>
                </div>
            </div>

            <div class="reminders">
                <div class="header">
                    <h2>Reminders</h2>
                    <span class="material-icons-sharp">
                        notifications_none
                    </span>
                </div>

                <div class="notification">
                    <div class="icon">
                        <span class="material-icons-sharp">
                            volume_up
                        </span>
                    </div>
                    <div class="content">
                        <div class="info">
                            <h3>Workshop</h3>
                            <small class="text_muted">
                                08:00 AM - 12:00 PM
                            </small>
                        </div>
                        <span class="material-icons-sharp">
                            more_vert
                        </span>
                    </div>
                </div>
                <div class="notification deactive">
                    <div class="icon">
                        <span class="material-icons-sharp">
                            edit
                        </span>
                    </div>
                    <div class="content">
                        <div class="info">
                            <h3>Workshop</h3>
                            <small class="text_muted">
                                08:00 AM - 12:00 PM
                            </small>
                        </div>
                        <span class="material-icons-sharp">
                            more_vert
                        </span>
                    </div>
                </div>
                <div class="notification add-reminder">
                    <div>
                        <span class="material-icons-sharp">
                            add
                        </span>
                        <h3>Add Reminder</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="admin_dashboard.js"></script>
</body>

</html>