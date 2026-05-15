<?php 
require_once '../config/database.php';
session_start();

// Security: Check if user is logged in as Manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_manager') {
    header("Location: ../login.php");
    exit();
}

$res_id = $_SESSION['restaurant_id'];

// --- Dynamic Stats Logic ---
// Fetch Active Menu Count
$count_sql = "SELECT COUNT(*) as total FROM menu_items WHERE restaurant_id = '$res_id'";
$count_result = mysqli_query($conn, $count_sql);
$menu_count = mysqli_fetch_assoc($count_result)['total'];

// Fetch Recent Items
$menu_sql = "SELECT * FROM menu_items 
             WHERE restaurant_id='$res_id'
             ORDER BY id DESC
             LIMIT 5";
$menu_result = mysqli_query($conn, $menu_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manager Dashboard | RM Portal</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; margin: 0; display: flex; background: #f4f7f6; }
        .sidebar { width: 250px; background: #2c3e50; height: 100vh; color: white; padding: 20px; position: fixed; }
        .main-content { margin-left: 270px; padding: 30px; width: calc(100% - 270px); }
        
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 12px; transition: 0.3s; border-radius: 5px; }
        .sidebar a:hover { background: #34495e; color: white; }
        .sidebar i { margin-right: 10px; width: 20px; }

        #ajax-order-alert { 
            background: #e74c3c; color: white; padding: 15px; border-radius: 8px; 
            margin-bottom: 20px; display: none; animation: blink 1s infinite;
        }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.7; } 100% { opacity: 1; } }

        .stat-grid { display: flex; gap: 20px; margin-top: 20px; }
        .card { background: white; padding: 20px; border-radius: 10px; flex: 1; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; }
        .card h3 { color: #7f8c8d; margin: 0; font-size: 14px; }
        .card p { font-size: 28px; margin: 10px 0; color: #2c3e50; font-weight: bold; }
        
        .btn-delete { color: #e74c3c; text-decoration: none; transition: 0.3s; }
        .btn-delete:hover { color: #c0392b; }
    </style>

    <script>
        function fetchNewOrders() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    var alertBox = document.getElementById("ajax-order-alert");
                    var countText = document.getElementById("order-count-text");
                    if (response.count > 0) {
                        alertBox.style.display = "block";
                        countText.innerHTML = response.count;
                    } else {
                        alertBox.style.display = "none";
                    }
                }
            };
            xhttp.open("GET", "../ajax/new_orders.php", true);
            xhttp.send();
        }
        setInterval(fetchNewOrders, 5000);
    </script>
</head>
<body onload="fetchNewOrders()">

    <div class="sidebar">
        <h2 style="text-align: center;">RM Portal</h2>
        <hr style="border: 0.5px solid #444;">
        <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="profile.php"><i class="fas fa-store"></i> Profile</a>
        <a href="menu.php"><i class="fas fa-utensils"></i> Menu</a>
        <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="reviews.php"><i class="fas fa-star"></i> Reviews</a>
        <a href="analytics.php"><i class="fas fa-chart-bar"></i> Analytics</a>
        <br><br>
        <a href="../controllers/AuthController.php?action=logout" style="color: #e74c3c;"><i class="fas fa-power-off"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1>Dashboard Overview</h1>

        <div id="ajax-order-alert">
            <i class="fas fa-exclamation-circle"></i> 
            Attention! You have <span id="order-count-text">0</span> new pending orders.
            <a href="orders.php" style="color: white; font-weight: bold; margin-left: 10px;">Check Now</a>
        </div>

        <div class="stat-grid">
            <div class="card">
                <h3>Total Sales</h3>
                <p>$0.00</p>
            </div>
            <div class="card">
                <h3>Active Menu</h3>
                <p><?php echo $menu_count; ?></p>
            </div>
            <div class="card">
                <h3>Total Reviews</h3>
                <p>0</p>
            </div>
        </div>

        <div style="margin-top: 40px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom:20px;">
                <i class="fas fa-utensils"></i> Recently Added Menu Items
            </h3>

            <?php if(mysqli_num_rows($menu_result) > 0): ?>
                <table style="width:100%; border-collapse: collapse;">
                    <tr style="background:#3498db; color:white;">
                        <th style="padding:12px; text-align:left;">Food Item</th>
                        <th style="padding:12px; text-align:left;">Price</th>
                        <th style="padding:12px; text-align:left;">Description</th>
                        <th style="padding:12px; text-align:center;">Action</th>
                    </tr>

                    <?php while($item = mysqli_fetch_assoc($menu_result)): ?>
                    <tr style="border-bottom:1px solid #ddd;">
                        <td style="padding:12px; font-weight: bold;">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </td>
                        <td style="padding:12px;">
                            $<?php echo number_format($item['price'], 2); ?>
                        </td>
                        <td style="padding:12px; color:#666;">
                            <?php echo htmlspecialchars($item['description']); ?>
                        </td>
                        <td style="padding:12px; text-align:center;">
                            <a href="../controllers/MenuController.php?delete_id=<?php echo $item['id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this item?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p style="color:#777; text-align: center; padding: 20px;">No menu items added yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>