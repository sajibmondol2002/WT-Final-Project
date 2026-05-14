<?php
require_once '../config/database.php';
session_start();

// Sudhu logged-in manager er jonno check hobe
if (isset($_SESSION['restaurant_id'])) {
    $res_id = $_SESSION['restaurant_id'];

    // Normal Query style-e pending order count kora
    $sql = "SELECT COUNT(*) as pending_count FROM orders WHERE restaurant_id = '$res_id' AND status = 'pending'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);

    // JSON format-e output deya (JavaScript er jonno)
    echo json_encode(['count' => $data['pending_count']]);
} else {
    echo json_encode(['count' => 0]);
}
?>