<?php 
require_once '../config/database.php';
session_start();

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_manager') {
    header("Location: ../login.php");
    exit();
}

$res_id = $_SESSION['restaurant_id'];

// 1. Fetch Summary Stats
$sql = "SELECT SUM(total_amount) as revenue, COUNT(id) as total_orders FROM orders WHERE restaurant_id = '$res_id' AND status = 'delivered'";
$res = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);
$revenue = $data['revenue'] ?? 0;
$orders = $data['total_orders'] ?? 0;

// 2. Fetch Data for Chart (Last 7 Days Revenue)
$chart_sql = "SELECT DATE(created_at) as date, SUM(total_amount) as daily_revenue 
              FROM orders 
              WHERE restaurant_id = '$res_id' AND status = 'delivered' 
              GROUP BY DATE(created_at) 
              ORDER BY date ASC LIMIT 7";
$chart_res = mysqli_query($conn, $chart_sql);

$labels = [];
$amounts = [];
while($row = mysqli_fetch_assoc($chart_res)) {
    $labels[] = $row['date'];
    $amounts[] = $row['daily_revenue'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics | Manager Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <style>
        body { font-family: 'Poppins', sans-serif; margin: 0; display: flex; background: #f4f7f6; }
        .sidebar { width: 250px; background: #2c3e50; height: 100vh; color: white; padding: 20px; position: fixed; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 12px; transition: 0.3s; border-radius: 5px; }
        .sidebar a:hover, .active { background: #34495e; color: white; }
        .sidebar i { margin-right: 10px; }

        .main-content { margin-left: 270px; padding: 30px; width: calc(100% - 270px); }
        
        .stat-grid { display: flex; gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; flex: 1; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; border-top: 4px solid #3498db; }
        .card h3 { color: #7f8c8d; margin: 0; font-size: 15px; text-transform: uppercase; }
        .card p { font-size: 32px; margin: 10px 0; color: #2c3e50; font-weight: bold; }

        .chart-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2 style="text-align: center;">RM Portal</h2>
        <hr style="border: 0.5px solid #444;">
        <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="profile.php"><i class="fas fa-store"></i> Profile</a>
        <a href="menu.php"><i class="fas fa-utensils"></i> Menu</a>
        <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="reviews.php"><i class="fas fa-star"></i> Reviews</a>
        <a href="analytics.php" class="active"><i class="fas fa-chart-bar"></i> Analytics</a>
        <br><br>
        <a href="../controllers/AuthController.php?action=logout" style="color: #e74c3c;"><i class="fas fa-power-off"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1><i class="fas fa-chart-line"></i> Restaurant Analytics</h1>

        <div class="stat-grid">
            <div class="card">
                <h3>Total Revenue</h3>
                <p>$<?php echo number_format($revenue, 2); ?></p>
            </div>
            <div class="card" style="border-top-color: #2ecc71;">
                <h3>Orders Completed</h3>
                <p><?php echo $orders; ?></p>
            </div>
            <div class="card" style="border-top-color: #f1c40f;">
                <h3>Avg. Order Value</h3>
                <p>$<?php echo ($orders > 0) ? number_format($revenue / $orders, 2) : '0.00'; ?></p>
            </div>
        </div>

        <div class="chart-container">
            <h3>Revenue Trend (Last 7 Days)</h3>
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Daily Revenue ($)',
                    data: <?php echo json_encode($amounts); ?>,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>