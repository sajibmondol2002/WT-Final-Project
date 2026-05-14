<?php 
require_once '../config/database.php';
session_start();
$res_id = $_SESSION['restaurant_id'];

$sql = "SELECT r.*, u.name FROM reviews r JOIN users u ON r.customer_id = u.id WHERE r.restaurant_id = '$res_id'";
$result = mysqli_query($conn, $sql);
?>
<h2>Customer Reviews</h2>
<?php while($rev = mysqli_fetch_assoc($result)): ?>
    <div style="border:1px solid #ccc; margin:10px; padding:10px;">
        <p><strong><?php echo $rev['name']; ?>:</strong> <?php echo $rev['rating']; ?>/5</p>
        <p><?php echo $rev['comment']; ?></p>
    </div>
<?php endwhile; ?>
<br><a href="dashboard.php">Back to Dashboard</a>