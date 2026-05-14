<?php
class Menu {
    private $db;

    // Database connection initialize kora
    public function __construct($conn) { 
        $this->db = $conn; 
    }

    // Category list ana (Normal style)
    public function getCategories($restaurant_id) {
        $sql = "SELECT * FROM menu_categories WHERE restaurant_id = '$restaurant_id' ORDER BY display_order";
        $result = mysqli_query($this->db, $sql);
        
        // Data gulo array hishebe return kora
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Notun Menu Item add kora (Normal style)
    public function addMenuItem($res_id, $cat_id, $name, $desc, $price, $img) {
        $sql = "INSERT INTO menu_items (restaurant_id, category_id, name, description, price, image_path) 
                VALUES ('$res_id', '$cat_id', '$name', '$desc', '$price', '$img')";
        
        return mysqli_query($this->db, $sql);
    }
}
?>