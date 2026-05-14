<?php
// Database configuration variables
$host = "localhost";
$user = "root";
$pass = "";
$db   = "food_ordering_system";

// 1. Database connection create kora
$conn = mysqli_connect($host, $user, $pass, $db);

// 2. Connection check kora
if (!$conn) {
    // Defense-er jonno error message-ta sundor vabe deya
    die("Database Connection Failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

 ?> 