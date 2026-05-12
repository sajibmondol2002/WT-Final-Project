<?php
// ============================================================
//  models/DiscountModel.php
// ============================================================
require_once __DIR__ . '/../config/database.php';

class DiscountModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /** All discounts for a restaurant with item name */
    public function getByRestaurant(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT d.*, mi.name AS item_name, mi.price AS item_price,
                    (SELECT COUNT(*) FROM order_items oi
                     JOIN orders o ON oi.order_id = o.id
                     WHERE oi.menu_item_id = d.menu_item_id
                       AND o.created_at BETWEEN d.valid_from AND DATE_ADD(d.valid_until, INTERVAL 1 DAY)
                    ) AS usage_count
             FROM discounts d
             JOIN menu_items mi ON d.menu_item_id = mi.id
             WHERE d.restaurant_id = ?
             ORDER BY d.id DESC"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(int $id, int $restaurantId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM discounts WHERE id = ? AND restaurant_id = ? LIMIT 1"
        );
        $stmt->bind_param('ii', $id, $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function create(int $restaurantId, array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO discounts (menu_item_id, restaurant_id, discount_pct, valid_from, valid_until, is_active)
             VALUES (?, ?, ?, ?, ?, 1)"
        );
        $stmt->bind_param(
            'iidss',
            $data['menu_item_id'],
            $restaurantId,
            $data['discount_pct'],
            $data['valid_from'],
            $data['valid_until']
        );
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function update(int $id, int $restaurantId, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE discounts SET discount_pct = ?, valid_from = ?, valid_until = ?
             WHERE id = ? AND restaurant_id = ?"
        );
        $stmt->bind_param('dssii', $data['discount_pct'], $data['valid_from'], $data['valid_until'], $id, $restaurantId);
        return $stmt->execute();
    }

    public function toggleActive(int $id, int $restaurantId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE discounts SET is_active = NOT is_active WHERE id = ? AND restaurant_id = ?"
        );
        $stmt->bind_param('ii', $id, $restaurantId);
        return $stmt->execute();
    }

    public function delete(int $id, int $restaurantId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM discounts WHERE id = ? AND restaurant_id = ?"
        );
        $stmt->bind_param('ii', $id, $restaurantId);
        return $stmt->execute();
    }
}