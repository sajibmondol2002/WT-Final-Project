<?php
require_once '../config/database.php';
session_start();

if (isset($_POST['update_profile'])) {
    $res_id = $_SESSION['restaurant_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cuisine = mysqli_real_escape_string($conn, $_POST['cuisine']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $is_open = (int)$_POST['is_open']; // Cast to integer for SQL

    $sql = "UPDATE restaurants SET name='$name', cuisine_type='$cuisine', address='$address', is_open='$is_open' WHERE id='$res_id'";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: ../views/profile.php?msg=Profile Updated Successfully");
    } else {
        header("Location: ../views/profile.php?msg=Error Updating Profile");
    }
    exit();
}
?>