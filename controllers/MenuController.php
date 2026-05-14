<?php
require_once '../config/database.php';
session_start();

if (isset($_POST['add_item'])) {
    $res_id = $_SESSION['restaurant_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    
    // Check kora hocche description asholei form theke ashche kina
    $desc = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : "";

    // Query theke category_id muche deya hoyeche jate error na ashe
    $sql = "INSERT INTO menu_items (restaurant_id, name, price, description) 
            VALUES ('$res_id', '$name', '$price', '$desc')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ../View/menu.php?msg=Item Added Successfully");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>