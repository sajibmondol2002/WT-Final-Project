<?php 
require_once '../config/database.php';
session_start();
$res_id = $_SESSION['restaurant_id'];

$sql = "SELECT SUM(total_amount) as revenue, COUNT(id) as total_orders FROM orders WHERE restaurant_id = '$res_id' AND status = 'delivered'";
$res = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);
?>
<h2>Restaurant Analytics</h2>
<p>Total Revenue: <strong>$<?php echo number_format($data['revenue'], 2); ?></strong></p>
<p>Orders Completed: <strong><?php echo $data['total_orders']; ?></strong></p>
<br><a href="dashboard.php">Back to Dashboard</a>