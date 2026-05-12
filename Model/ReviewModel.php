<?php
// ============================================================
//  models/ReviewModel.php
// ============================================================
require_once __DIR__ . '/../config/database.php';

class ReviewModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getByRestaurant(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, u.name AS customer_name, u.profile_pic AS customer_pic
             FROM reviews r
             JOIN users u ON r.customer_id = u.id
             WHERE r.restaurant_id = ?
             ORDER BY r.created_at DESC"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Post or update manager reply */
    public function addReply(int $reviewId, int $restaurantId, string $reply): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE reviews SET manager_reply = ?
             WHERE id = ? AND restaurant_id = ?"
        );
        $stmt->bind_param('sii', $reply, $reviewId, $restaurantId);
        return $stmt->execute();
    }

    public function getAverageSummary(int $restaurantId): array
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS total, AVG(rating) AS avg_rating,
                    SUM(rating = 5) AS five_star,
                    SUM(rating = 4) AS four_star,
                    SUM(rating = 3) AS three_star,
                    SUM(rating = 2) AS two_star,
                    SUM(rating = 1) AS one_star
             FROM reviews WHERE restaurant_id = ?"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}