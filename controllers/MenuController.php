<?php
require_once '../config/database.php';
session_start();

// Security: Ensure the user is actually a manager before doing anything
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant_manager') {
    header("Location: ../login.php");
    exit();
}

// --- START: ADD LOGIC ---
if (isset($_POST['add_item'])) {
    $res_id = $_SESSION['restaurant_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $desc = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : "";

    $sql = "INSERT INTO menu_items (restaurant_id, name, price, description) 
            VALUES ('$res_id', '$name', '$price', '$desc')";
    
    if (mysqli_query($conn, $sql)) {
        // Updated path: pointing back to the views folder
        header("Location: ../views/menu.php?msg=Item Added Successfully");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// --- START: DELETE LOGIC ---
if (isset($_GET['delete_id'])) {
    // Convert to integer for extra security against SQL injection
    $id = intval($_GET['delete_id']); 
    $res_id = $_SESSION['restaurant_id']; 

    // Delete query
    $sql = "DELETE FROM menu_items WHERE id = '$id' AND restaurant_id = '$res_id'";

    if (mysqli_query($conn, $sql)) {
        /* Using HTTP_REFERER sends the user back to the page they clicked 'Delete' from.
           This is better because it works for both dashboard.php AND menu.php.
        */
        if(isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: ../views/menu.php?msg=Item Deleted Successfully");
        }
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>