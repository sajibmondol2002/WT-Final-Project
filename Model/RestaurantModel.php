<?php
// ============================================================
//  models/RestaurantModel.php
// ============================================================
require_once __DIR__ . '/../config/database.php';

class RestaurantModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /** Get restaurant by manager ID */
    public function getByManagerId(int $managerId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM restaurants WHERE manager_id = ? LIMIT 1"
        );
        $stmt->bind_param('i', $managerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /** Get restaurant by its own ID */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM restaurants WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /** Create a new restaurant (pending approval) */
    public function create(int $managerId, array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO restaurants
             (manager_id, name, description, cuisine_type, address, city, opening_hours, delivery_radius_km)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'issssssd',
            $managerId,
            $data['name'],
            $data['description'],
            $data['cuisine_type'],
            $data['address'],
            $data['city'],
            $data['opening_hours'],
            $data['delivery_radius_km']
        );
        $stmt->execute();
        return $this->db->insert_id;
    }

    /** Update restaurant profile */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE restaurants SET
                name = ?, description = ?, cuisine_type = ?, address = ?,
                city = ?, opening_hours = ?, delivery_radius_km = ?
             WHERE id = ?"
        );
        $stmt->bind_param(
            'ssssssd i',
            $data['name'],
            $data['description'],
            $data['cuisine_type'],
            $data['address'],
            $data['city'],
            $data['opening_hours'],
            $data['delivery_radius_km'],
            $id
        );
        return $stmt->execute();
    }

    /** Update logo path */
    public function updateLogo(int $id, string $logoPath): bool
    {
        $stmt = $this->db->prepare("UPDATE restaurants SET logo_path = ? WHERE id = ?");
        $stmt->bind_param('si', $logoPath, $id);
        return $stmt->execute();
    }

    /** Toggle open/closed */
    public function toggleOpen(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE restaurants SET is_open = NOT is_open WHERE id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    /** Get average rating for a restaurant */
    public function getAverageRating(int $restaurantId): float
    {
        $stmt = $this->db->prepare(
            "SELECT AVG(rating) AS avg_rating FROM reviews WHERE restaurant_id = ?"
        );
        $stmt->bind_param('i', $restaurantId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return round((float)($row['avg_rating'] ?? 0), 1);
    }
}