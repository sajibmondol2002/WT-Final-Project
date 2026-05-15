<?php 
require_once '../config/database.php';
require_once '../models/review.php'; 
session_start();

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_manager') {
    header("Location: ../login.php");
    exit();
}

$res_id = $_SESSION['restaurant_id'];
$reviewModel = new Review($conn);
$all_reviews = $reviewModel->getRestaurantReviews($res_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Reviews | RM Portal</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=1.1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <h2>RM Portal</h2>
        <hr style="border: 0.5px solid #444; margin-bottom: 20px;">
        <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="profile.php"><i class="fas fa-store"></i> Profile</a>
        <a href="menu.php"><i class="fas fa-utensils"></i> Menu</a>
        <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="reviews.php" class="active"><i class="fas fa-star"></i> Reviews</a>
        <a href="analytics.php"><i class="fas fa-chart-bar"></i> Analytics</a>
        <br><br>
        <a href="../controllers/AuthController.php?action=logout" style="color: #e74c3c;"><i class="fas fa-power-off"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1><i class="fas fa-star"></i> Customer Reviews</h1>

        <?php if(empty($all_reviews)): ?>
            <div class="review-card"><p>No reviews yet for your restaurant.</p></div>
        <?php else: ?>
            <?php foreach($all_reviews as $rev): ?>
                <div class="review-card">
                    <div class="review-header">
                        <span class="customer-name"><?php echo htmlspecialchars($rev['customer_name']); ?></span>
                        <span class="rating">
                            <?php for($i=1; $i<=5; $i++) echo ($i <= $rev['rating']) ? '★' : '☆'; ?>
                        </span>
                    </div>
                    
                    <p class="comment-text">"<?php echo htmlspecialchars($rev['comment']); ?>"</p>
                    <small style="color: #999;"><?php echo $rev['created_at']; ?></small>

                    <div class="reply-box">
                        <?php if(!empty($rev['manager_reply'])): ?>
                            <span class="manager-label"><i class="fas fa-reply"></i> Your Reply:</span>
                            <p style="margin:0; color: #333;"><?php echo htmlspecialchars($rev['manager_reply']); ?></p>
                        <?php else: ?>
                            <form action="../controllers/reviewcontroller.php" method="POST">
                                <input type="hidden" name="review_id" value="<?php echo $rev['id']; ?>">
                                <textarea name="manager_reply" placeholder="Write your reply here..." required style="height: 80px;"></textarea>
                                <button type="submit" name="submit_reply" style="width: auto; margin-top: 10px;">Submit Reply</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>