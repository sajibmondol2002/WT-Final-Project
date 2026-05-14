<?php 
require_once '../config/database.php';
require_once '../models/Restaurant.php';
session_start();

$resModel = new Restaurant($conn);
$profile = $resModel->getProfile($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head><title>Profile</title></head>
<body>
    <h2>Restaurant Profile</h2>
    <form action="../controllers/RestaurantController.php" method="POST">
        Name: <input type="text" name="name" value="<?php echo $profile['name']; ?>"><br><br>
        Cuisine: <input type="text" name="cuisine" value="<?php echo $profile['cuisine_type']; ?>"><br><br>
        Address: <textarea name="address"><?php echo $profile['address']; ?></textarea><br><br>
        Status: 
        <select name="is_open">
            <option value="1" <?php if($profile['is_open']) echo 'selected'; ?>>Open</option>
            <option value="0" <?php if(!$profile['is_open']) echo 'selected'; ?>>Closed</option>
        </select><br><br>
        <button type="submit" name="update_profile">Update Profile</button>
    </form>
    <br><a href="dashboard.php">Back to Dashboard</a>
</body>
</html>