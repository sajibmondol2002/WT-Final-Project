<?php
// ============================================================
//  models/AnalyticsModel.php
// ============================================================
require_once __DIR__ . '/../config/database.php';

class AnalyticsModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /** Revenue & order totals by period */
    public function getSalesSummary(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END)               AS orders_today,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN total_amount ELSE 0 END) AS revenue_today,
                COUNT(CASE WHEN YEARWEEK(created_at,1) = YEARWEEK(CURDATE(),1) THEN 1 END) AS orders_week,
                SUM(CASE WHEN YEARWEEK(created_at,1) = YEARWEEK(CURDATE(),1) THEN total_amount ELSE 0 END) AS revenue_week,
                COUNT(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN 1 END) AS orders_month,
                SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN total_amount ELSE 0 END) AS revenue_month,
                COUNT(*) AS orders_all,
                SUM(total_amount) AS revenue_all,
                AVG(total_amount) AS avg_order_value
             FROM orders
             WHERE restaurant_id = ? AND status NOT IN ('pending','cancelled')"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /** Daily orders for last 30 days */
    public function getDailyOrders(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE(created_at) AS day, COUNT(*) AS order_count, SUM(total_amount) AS revenue
             FROM orders
             WHERE restaurant_id = ?
               AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
               AND status NOT IN ('pending','cancelled')
             GROUP BY DATE(created_at)
             ORDER BY day ASC"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Top 5 most ordered items */
    public function getTopItems(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT mi.name, SUM(oi.quantity) AS total_qty, SUM(oi.quantity * oi.unit_price) AS total_revenue
             FROM order_items oi
             JOIN menu_items mi ON oi.menu_item_id = mi.id
             JOIN orders o ON oi.order_id = o.id
             WHERE mi.restaurant_id = ? AND o.status NOT IN ('pending','cancelled')
             GROUP BY mi.id, mi.name
             ORDER BY total_qty DESC
             LIMIT 5"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Orders by status breakdown */
    public function getStatusBreakdown(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT status, COUNT(*) AS cnt
             FROM orders WHERE restaurant_id = ?
             GROUP BY status"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Peak ordering hours */
    public function getPeakHours(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT HOUR(created_at) AS hour, COUNT(*) AS cnt
             FROM orders WHERE restaurant_id = ?
             GROUP BY HOUR(created_at)
             ORDER BY hour ASC"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}