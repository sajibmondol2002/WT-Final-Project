<?php 
require_once '../config/database.php';
require_once '../models/restaurant.php'; // Corrected filename to lowercase if needed
session_start();

// Security: Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_manager') {
    header("Location: ../login.php");
    exit();
}

$resModel = new Restaurant($conn);
$profile = $resModel->getProfile($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Profile | RM Portal</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="sidebar">
        <h2>RM Portal</h2>
        <hr style="border: 0.5px solid #444; margin-bottom: 20px;">
        <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="profile.php" class="active"><i class="fas fa-store"></i> Profile</a>
        <a href="menu.php"><i class="fas fa-utensils"></i> Menu</a>
        <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="reviews.php"><i class="fas fa-star"></i> Reviews</a>
        <a href="analytics.php"><i class="fas fa-chart-bar"></i> Analytics</a>
        <br><br>
        <a href="../controllers/AuthController.php?action=logout" style="color: #e74c3c;"><i class="fas fa-power-off"></i> Logout</a>
    </div>

    <div class="main-content">
        <h1><i class="fas fa-edit"></i> Manage Restaurant Profile</h1>

        <div class="form-container" style="max-width: 600px;">
            <?php if(isset($_GET['msg'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>

            <form action="../controllers/RestaurantController.php" method="POST">
                <label>Restaurant Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($profile['name']); ?>" required>

                <label>Cuisine Type</label>
                <input type="text" name="cuisine" value="<?php echo htmlspecialchars($profile['cuisine_type']); ?>" placeholder="e.g. Italian, Bengali" required>

                <label>Store Address</label>
                <textarea name="address" rows="4" required><?php echo htmlspecialchars($profile['address']); ?></textarea>

                <label>Availability Status</label>
                <select name="is_open">
                    <option value="1" <?php if($profile['is_open'] == 1) echo 'selected'; ?>>Open for Business</option>
                    <option value="0" <?php if($profile['is_open'] == 0) echo 'selected'; ?>>Temporarily Closed</option>
                </select>

                <button type="submit" name="update_profile" style="margin-top: 15px;">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

</body>
</html>