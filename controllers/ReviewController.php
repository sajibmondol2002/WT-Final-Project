<?php
require_once '../config/database.php';
session_start();

if (isset($_POST['submit_reply'])) {
    $review_id = $_POST['review_id'];
    $reply = $_POST['manager_reply'];

    // Review table e manager_reply column thakle eita kaj korbe
    $sql = "UPDATE reviews SET manager_reply = '$reply' WHERE id = '$review_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ../views/reviews.php?msg=Replied");
    }
}
?>