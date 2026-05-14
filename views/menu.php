<?php 
require_once '../config/database.php';
session_start();

// Security: Check if manager is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_manager') {
    header("Location: ../login.php");
    exit();
}

$res_id = $_SESSION['restaurant_id'];

// Data fetch kora
$sql = "SELECT * FROM menu_items WHERE restaurant_id = '$res_id' ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu Management | Manager Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; display: flex; margin: 0; background: #f4f7f6; }
        .sidebar { width: 250px; background: #2c3e50; height: 100vh; color: white; padding: 20px; position: fixed; }
        .sidebar h2 { text-align: center; font-size: 20px; }
        .sidebar a { display: block; color: #bdc3c7; text-decoration: none; padding: 12px; border-radius: 5px; }
        .sidebar a:hover, .active { background: #34495e; color: white; }
        
        .main-content { margin-left: 250px; padding: 40px; width: 100%; }
        .msg { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; background: white; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #3498db; color: white; }

        .add-form { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 500px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #27ae60; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #219150; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>RM Portal</h2>
        <hr>
        <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="menu.php" class="active"><i class="fas fa-utensils"></i> Menu</a>
        <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        <a href="../logout.php" style="color: #e74c3c; margin-top: 20px;"><i class="fas fa-power-off"></i> Logout</a>
    </div>

    <div class="main-content">
        <h2><i class="fas fa-utensils"></i> Menu Management</h2>
        
        <?php if(isset($_GET['msg'])): ?>
            <div class="msg"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($item = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><strong><?php echo $item['name']; ?></strong></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td style="color: #666; font-size: 14px;"><?php echo $item['description']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center;">No items found. Add your first item below!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="add-form">
            <h3><i class="fas fa-plus-circle"></i> Add New Menu Item</h3>
            <form action="../controllers/MenuController.php" method="POST">
                <div class="form-group">
                    <label>Item Name:</label>
                    <input type="text" name="name" placeholder="e.g. Chicken Burger" required>
                </div>
                
                <div class="form-group">
                    <label>Price ($):</label>
                    <input type="number" step="0.01" name="price" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label>Description:</label>
                    <textarea name="description" rows="3" placeholder="Brief details about the food..." required></textarea>
                </div>

                <button type="submit" name="add_item">Add Item to Menu</button>
            </form>
        </div>
    </div>

</body>
</html>