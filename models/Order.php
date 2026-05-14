<?php
class Order {
    private $db;
    public function __construct($conn) { $this->db = $conn; }

    public function getOrdersByRestaurant($res_id) {
        $sql = "SELECT * FROM orders WHERE restaurant_id = '$res_id' ORDER BY created_at DESC";
        return mysqli_query($this->db, $sql);
    }
}
?>