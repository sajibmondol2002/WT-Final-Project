<?php
// ============================================================
//  models/MenuModel.php  (Categories + Items)
// ============================================================
require_once __DIR__ . '/../config/database.php';

class MenuModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    // ==================== CATEGORIES ====================

    public function getCategoriesByRestaurant(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM menu_categories WHERE restaurant_id = ? ORDER BY display_order ASC"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategoryById(int $id, int $restaurantId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM menu_categories WHERE id = ? AND restaurant_id = ? LIMIT 1"
        );
        $stmt->bind_param('ii', $id, $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function createCategory(int $restaurantId, string $name, int $displayOrder): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO menu_categories (restaurant_id, name, display_order) VALUES (?, ?, ?)"
        );
        $stmt->bind_param('isi', $restaurantId, $name, $displayOrder);
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function updateCategory(int $id, int $restaurantId, string $name, int $displayOrder): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE menu_categories SET name = ?, display_order = ?
             WHERE id = ? AND restaurant_id = ?"
        );
        $stmt->bind_param('siii', $name, $displayOrder, $id, $restaurantId);
        return $stmt->execute();
    }

    public function deleteCategory(int $id, int $restaurantId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM menu_categories WHERE id = ? AND restaurant_id = ?"
        );
        $stmt->bind_param('ii', $id, $restaurantId);
        return $stmt->execute();
    }

    // ==================== MENU ITEMS ====================

    /** Get all items for a restaurant, joined with category name */
    public function getItemsByRestaurant(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT mi.*, mc.name AS category_name
             FROM menu_items mi
             LEFT JOIN menu_categories mc ON mi.category_id = mc.id
             WHERE mi.restaurant_id = ?
             ORDER BY mc.display_order ASC, mi.name ASC"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getItemById(int $id, int $restaurantId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM menu_items WHERE id = ? AND restaurant_id = ? LIMIT 1"
        );
        $stmt->bind_param('ii', $id, $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function createItem(int $restaurantId, array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO menu_items (restaurant_id, category_id, name, description, price, image_path, is_available)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'iissdsi',
            $restaurantId,
            $data['category_id'],
            $data['name'],
            $data['description'],
            $data['price'],
            $data['image_path'],
            $data['is_available']
        );
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function updateItem(int $id, int $restaurantId, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE menu_items SET category_id = ?, name = ?, description = ?,
             price = ?, is_available = ?
             WHERE id = ? AND restaurant_id = ?"
        );
        $stmt->bind_param(
            'issdiiii',
            $data['category_id'],
            $data['name'],
            $data['description'],
            $data['price'],
            $data['is_available'],
            $id,
            $restaurantId
        );
        return $stmt->execute();
    }

    public function updateItemImage(int $id, string $imagePath): bool
    {
        $stmt = $this->db->prepare("UPDATE menu_items SET image_path = ? WHERE id = ?");
        $stmt->bind_param('si', $imagePath, $id);
        return $stmt->execute();
    }

    public function toggleAvailability(int $id, int $restaurantId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE menu_items SET is_available = NOT is_available
             WHERE id = ? AND restaurant_id = ?"
        );
        $stmt->bind_param('ii', $id, $restaurantId);
        return $stmt->execute();
    }

    public function deleteItem(int $id, int $restaurantId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM menu_items WHERE id = ? AND restaurant_id = ?"
        );
        $stmt->bind_param('ii', $id, $restaurantId);
        return $stmt->execute();
    }
}