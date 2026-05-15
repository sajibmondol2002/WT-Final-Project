<?php
require_once '../config/database.php';
session_start();

if (isset($_POST['submit_reply'])) {
    $review_id = mysqli_real_escape_string($conn, $_POST['review_id']);
    $reply = mysqli_real_escape_string($conn, $_POST['manager_reply']);

    $sql = "UPDATE reviews SET manager_reply = '$reply' WHERE id = '$review_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ../views/reviews.php?msg=RepliedSuccessfully");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>