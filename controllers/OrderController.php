<?php
require_once '../config/database.php';
session_start();

if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE orders SET status = '$status' WHERE id = '$order_id'";
    mysqli_query($conn, $sql);
    
    header("Location: ../views/orders.php?msg=Updated");
}
?>