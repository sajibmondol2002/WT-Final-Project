-- Optional default-user seed for an existing database.
-- For a fresh XAMPP/phpMyAdmin setup, import data/init.sql instead.

ALTER TABLE users ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) NULL DEFAULT NULL AFTER password;
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER profile_picture;

DELETE FROM `users`
WHERE `role` = 'admin'
  AND `email` IN ('admin@example.com', 'admin@foodapp.local');

INSERT INTO `users` (`name`, `email`, `password`, `password_hash`, `phone`, `role`, `status`, `vehicle_type`, `is_available`, `is_active`, `created_at`) VALUES
('Default Customer', 'customer@food.local', '$2y$10$Hpz7zbcDDIDmi/kU.GZVuOAhiqF5UmlmUTssCNPGixMMvrZ8rEPji', '$2y$10$Hpz7zbcDDIDmi/kU.GZVuOAhiqF5UmlmUTssCNPGixMMvrZ8rEPji', NULL, 'customer', 'active', NULL, NULL, 1, NOW()),
('Default Delivery Agent', 'delivery@food.local', '$2y$10$PWdkkWOslo/QXHapPmOhXuf.DLLz/VNRGVOxYFcNff7Ti4zCCrtMe', '$2y$10$PWdkkWOslo/QXHapPmOhXuf.DLLz/VNRGVOxYFcNff7Ti4zCCrtMe', '01700000002', 'delivery_man', 'active', 'Motorbike', 1, 1, NOW()),
('Default Restaurant Manager', 'manager@food.local', '$2y$10$oNmX1gtwkAtWFUaH4I6AhOWaZDOfCoZnGQezY0o8I1WcsYaw5KNue', '$2y$10$oNmX1gtwkAtWFUaH4I6AhOWaZDOfCoZnGQezY0o8I1WcsYaw5KNue', '01700000003', 'restaurant_manager', 'active', NULL, NULL, 1, NOW()),
('Default Platform Admin', 'admin@food.local', '$2y$10$FMe0Z56Qa0L7RPsoAAVaneqoqyOmknKcLmRBf4P1AUUOQA.MaoRLq', '$2y$10$FMe0Z56Qa0L7RPsoAAVaneqoqyOmknKcLmRBf4P1AUUOQA.MaoRLq', '01700000004', 'admin', 'active', NULL, NULL, 1, NOW())
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `password` = VALUES(`password`),
    `password_hash` = VALUES(`password_hash`),
    `phone` = VALUES(`phone`),
    `role` = VALUES(`role`),
    `status` = VALUES(`status`),
    `vehicle_type` = VALUES(`vehicle_type`),
    `is_available` = VALUES(`is_available`),
    `is_active` = VALUES(`is_active`);
