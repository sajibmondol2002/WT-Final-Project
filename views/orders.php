<?php 
require_once '../config/database.php';
session_start();

// Security: Check if manager is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_manager') {
    header("Location: ../login.php");
    exit();
}

$res_id = $_SESSION['restaurant_id'];

// Normal Query: Latest orders shobar upore dekhabe
$sql = "SELECT * FROM orders WHERE restaurant_id = '$res_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders | Manager Portal</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js"></script>
</head>
<body>

    <div class="main-content">
        <h2><i class="fas fa-shopping-cart"></i> All Orders Management</h2>

        <?php if(isset($_GET['msg'])) echo "<p class='msg' style='background:#d4edda; color:#155724; padding:10px; border-radius:5px;'>Status ".$_GET['msg']."</p>"; ?>

        <table border="1">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Total Amount</th>
                    <th>Change Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo $order['created_at']; ?></td>
                    <td>
                        <span class="status-badge" style="padding:5px 10px; border-radius:15px; background:#eee; font-size:12px; font-weight:bold;">
                            <?php echo strtoupper($order['status']); ?>
                        </span>
                    </td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                        <form action="../controllers/OrderController.php" method="POST" style="box-shadow:none; padding:0; background:none;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" style="width:auto; display:inline-block; padding:5px;">
                                <option value="pending" <?php if($order['status']=='pending') echo 'selected'; ?>>Pending</option>
                                <option value="preparing" <?php if($order['status']=='preparing') echo 'selected'; ?>>Preparing</option>
                                <option value="ready" <?php if($order['status']=='ready') echo 'selected'; ?>>Ready</option>
                                <option value="delivered" <?php if($order['status']=='delivered') echo 'selected'; ?>>Delivered</option>
                                <option value="cancelled" <?php if($order['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" style="padding:5px 10px; font-size:12px;">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <br>
        <a href="dashboard.php" style="text-decoration:none; color:#2c3e50; font-weight:bold;">← Back to Dashboard</a>
    </div>

</body>
</html>