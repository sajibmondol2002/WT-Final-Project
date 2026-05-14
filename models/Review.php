<?php
class Review {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    // Get all reviews for a specific restaurant (Normal Style)
    public function getRestaurantReviews($restaurant_id) {
        $sql = "SELECT r.*, u.name as customer_name 
                FROM reviews r 
                JOIN users u ON r.customer_id = u.id 
                WHERE r.restaurant_id = '$restaurant_id' 
                ORDER BY r.created_at DESC";
        
        $result = mysqli_query($this->db, $sql);
        
        // Shob review array hishebe return korbe
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Update the manager's reply to a specific review (Normal Style)
    public function submitReply($review_id, $reply_text) {
        // Variable-ke query-r bhetore sorasori boshiye deya
        $sql = "UPDATE reviews SET manager_reply = '$reply_text' WHERE id = '$review_id'";
        
        return mysqli_query($this->db, $sql);
    }
}
?>