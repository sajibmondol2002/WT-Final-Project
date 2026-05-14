<?php
require_once '../config/database.php';
session_start();

if (isset($_POST['update_profile'])) {
    $res_id = $_SESSION['restaurant_id'];
    $name = $_POST['name'];
    $cuisine = $_POST['cuisine'];
    $address = $_POST['address'];
    $is_open = isset($_POST['is_open']) ? 1 : 0;

    $sql = "UPDATE restaurants SET name='$name', cuisine_type='$cuisine', address='$address', is_open='$is_open' WHERE id='$res_id'";
    mysqli_query($conn, $sql);
    
    header("Location: ../views/profile.php?msg=Profile Updated");
}
?>