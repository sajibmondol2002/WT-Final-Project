<?php
class Restaurant {
    private $db;
    public function __construct($conn) { $this->db = $conn; }

    public function getProfile($manager_id) {
        $sql = "SELECT * FROM restaurants WHERE manager_id = '$manager_id'";
        $result = mysqli_query($this->db, $sql);
        return mysqli_fetch_assoc($result);
    }
}
?>