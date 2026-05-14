<?php
session_start();
if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'restaurant_manager') {
    header("Location: views/dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>