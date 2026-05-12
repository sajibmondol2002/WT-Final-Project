<?php
// ============================================================
//  models/OrderModel.php
// ============================================================
require_once __DIR__ . '/../config/database.php';

class OrderModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /** Incoming pending orders for a restaurant */
    public function getPendingOrders(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT o.*, u.name AS customer_name, u.phone AS customer_phone
             FROM orders o
             JOIN users u ON o.customer_id = u.id
             WHERE o.restaurant_id = ? AND o.status = 'pending'
             ORDER BY o.created_at ASC"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Active orders (accepted / preparing / ready) */
    public function getActiveOrders(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT o.*, u.name AS customer_name, u.phone AS customer_phone
             FROM orders o
             JOIN users u ON o.customer_id = u.id
             WHERE o.restaurant_id = ?
               AND o.status IN ('accepted','preparing','ready')
             ORDER BY o.created_at ASC"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Full order history */
    public function getOrderHistory(int $restaurantId, string $search = ''): array
    {
        $like = "%$search%";
        $stmt = $this->db->prepare(
            "SELECT o.*, u.name AS customer_name
             FROM orders o
             JOIN users u ON o.customer_id = u.id
             WHERE o.restaurant_id = ?
               AND (u.name LIKE ? OR CAST(o.id AS CHAR) LIKE ?)
             ORDER BY o.created_at DESC"
        );
        $stmt->bind_param('iss', $restaurantId, $like, $like);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Get a single order with its items */
    public function getOrderWithItems(int $orderId, int $restaurantId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT o.*, u.name AS customer_name, u.phone AS customer_phone
             FROM orders o
             JOIN users u ON o.customer_id = u.id
             WHERE o.id = ? AND o.restaurant_id = ? LIMIT 1"
        );
        $stmt->bind_param('ii', $orderId, $restaurantId);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        if (!$order) return null;

        $stmt2 = $this->db->prepare(
            "SELECT oi.*, mi.name AS item_name
             FROM order_items oi
             JOIN menu_items mi ON oi.menu_item_id = mi.id
             WHERE oi.order_id = ?"
        );
        $stmt2->bind_param('i', $orderId);
        $stmt2->execute();
        $order['items'] = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

        return $order;
    }

    /** Update order status */
    public function updateStatus(int $orderId, int $restaurantId, string $status): bool
    {
        $allowed = ['accepted', 'preparing', 'ready', 'cancelled'];
        if (!in_array($status, $allowed)) return false;

        $stmt = $this->db->prepare(
            "UPDATE orders SET status = ? WHERE id = ? AND restaurant_id = ?"
        );
        $stmt->bind_param('sii', $status, $orderId, $restaurantId);
        return $stmt->execute();
    }

    /** Count pending orders (used by AJAX polling) */
    public function countPending(int $restaurantId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS cnt FROM orders WHERE restaurant_id = ? AND status = 'pending'"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['cnt'];
    }

    /** Latest timestamp of pending orders (for change detection) */
    public function latestPendingTimestamp(int $restaurantId): string
    {
        $stmt = $this->db->prepare(
            "SELECT MAX(created_at) AS ts FROM orders WHERE restaurant_id = ? AND status = 'pending'"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['ts'] ?? '';
    }
}