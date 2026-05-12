<?php
// ============================================================
//  models/UserModel.php
// ============================================================
require_once __DIR__ . '/../config/database.php';

class UserModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    /** Find user by email (for login) */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, password_hash, phone, role, profile_pic, is_active
             FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: null;
    }

    /** Find user by ID */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, name, email, phone, role, profile_pic, is_active, created_at
             FROM users WHERE id = ? LIMIT 1"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /** Register a new manager account */
    public function createManager(string $name, string $email, string $phone, string $password): int|false
    {
        // Check duplicate email
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return false; // email already taken
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, 'manager')"
        );
        $stmt->bind_param('ssss', $name, $email, $hash, $phone);
        $stmt->execute();
        return $this->db->insert_id;
    }

    /** Update basic profile info */
    public function updateProfile(int $id, string $name, string $phone): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE users SET name = ?, phone = ? WHERE id = ? AND role = 'manager'"
        );
        $stmt->bind_param('ssi', $name, $phone, $id);
        return $stmt->execute();
    }

    /** Update password */
    public function updatePassword(int $id, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->bind_param('si', $hash, $id);
        return $stmt->execute();
    }

    /** Update profile picture path */
    public function updateProfilePic(int $id, string $path): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param('si', $path, $id);
        return $stmt->execute();
    }
}