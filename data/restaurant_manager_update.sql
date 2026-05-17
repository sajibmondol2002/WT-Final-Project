-- Restaurant Manager feature update for an existing online_food_ordering database.
-- Use this only if you already imported data/init.sql before the Restaurant Manager work was added.

USE `online_food_ordering`;

ALTER TABLE users ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255) NULL DEFAULT NULL AFTER password;
ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_pic VARCHAR(255) NULL DEFAULT NULL AFTER vehicle_type;
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER profile_picture;
UPDATE users SET password_hash = password WHERE password_hash IS NULL;
UPDATE users SET is_active = IF(status = 'active', 1, 0);

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

CREATE TABLE IF NOT EXISTS `restaurants` (
    `id`                 INT            NOT NULL AUTO_INCREMENT,
    `manager_id`         INT            NOT NULL,
    `name`               VARCHAR(200)   NOT NULL,
    `description`        TEXT               NULL,
    `cuisine_type`       VARCHAR(120)       NULL,
    `address`            TEXT               NULL,
    `city`               VARCHAR(100)       NULL,
    `logo_path`          VARCHAR(255)       NULL,
    `opening_hours`      VARCHAR(150)       NULL,
    `delivery_radius_km` DECIMAL(8,2)   NOT NULL DEFAULT 5.00,
    `is_open`            TINYINT(1)     NOT NULL DEFAULT 0,
    `is_approved`        TINYINT(1)     NOT NULL DEFAULT 0,
    `created_at`         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_restaurants_manager` (`manager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE categories ADD COLUMN IF NOT EXISTS restaurant_id INT NULL DEFAULT NULL AFTER id;

CREATE TABLE IF NOT EXISTS `menu_categories` (
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `restaurant_id` INT          NOT NULL,
    `category_id`   INT              NULL DEFAULT NULL,
    `name`          VARCHAR(100) NOT NULL,
    `display_order` INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_menu_categories_legacy` (`restaurant_id`, `category_id`),
    KEY `idx_menu_categories_restaurant` (`restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE products ADD COLUMN IF NOT EXISTS restaurant_id INT NULL DEFAULT NULL AFTER id;
ALTER TABLE products ADD COLUMN IF NOT EXISTS menu_item_id INT NULL DEFAULT NULL AFTER restaurant_id;

CREATE TABLE IF NOT EXISTS `menu_items` (
    `id`            INT            NOT NULL AUTO_INCREMENT,
    `restaurant_id` INT            NOT NULL,
    `category_id`   INT            NOT NULL,
    `product_id`    INT                NULL DEFAULT NULL,
    `name`          VARCHAR(200)   NOT NULL,
    `description`   TEXT               NULL,
    `price`         DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `image_path`    VARCHAR(255)       NULL DEFAULT 'placeholder.png',
    `is_available`  TINYINT(1)     NOT NULL DEFAULT 1,
    `created_at`    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_menu_items_product` (`product_id`),
    KEY `idx_menu_items_restaurant` (`restaurant_id`),
    KEY `idx_menu_items_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `discounts` (
    `id`            INT           NOT NULL AUTO_INCREMENT,
    `menu_item_id`  INT           NOT NULL,
    `restaurant_id` INT           NOT NULL,
    `discount_pct`  DECIMAL(5,2)  NOT NULL DEFAULT 0.00,
    `valid_from`    DATETIME      NOT NULL,
    `valid_until`   DATETIME      NOT NULL,
    `is_active`     TINYINT(1)    NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `idx_discounts_restaurant` (`restaurant_id`),
    KEY `idx_discounts_item` (`menu_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_id INT NULL DEFAULT NULL AFTER user_id;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS restaurant_id INT NULL DEFAULT NULL AFTER customer_id;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS agent_id INT NULL DEFAULT NULL AFTER restaurant_id;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) NULL DEFAULT 'cash_on_delivery' AFTER agent_id;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER payment_method;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER subtotal;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS estimated_delivery_minutes INT NULL DEFAULT 45 AFTER status;
ALTER TABLE orders MODIFY status ENUM('pending','accepted','preparing','ready','picked_up','delivered','cancelled') NOT NULL DEFAULT 'pending';
UPDATE orders SET customer_id = user_id WHERE customer_id IS NULL;
UPDATE orders SET subtotal = total_amount WHERE subtotal = 0.00;

ALTER TABLE order_items ADD COLUMN IF NOT EXISTS menu_item_id INT NULL DEFAULT NULL AFTER product_id;
ALTER TABLE order_items ADD COLUMN IF NOT EXISTS unit_price DECIMAL(10,2) NULL DEFAULT NULL AFTER price;
ALTER TABLE order_items ADD COLUMN IF NOT EXISTS discount_id INT NULL DEFAULT NULL AFTER subtotal;
UPDATE order_items SET unit_price = price WHERE unit_price IS NULL;

ALTER TABLE reviews ADD COLUMN IF NOT EXISTS customer_id INT NULL DEFAULT NULL AFTER user_id;
ALTER TABLE reviews ADD COLUMN IF NOT EXISTS restaurant_id INT NULL DEFAULT NULL AFTER customer_id;
ALTER TABLE reviews ADD COLUMN IF NOT EXISTS manager_reply TEXT NULL DEFAULT NULL AFTER comment;
UPDATE reviews SET customer_id = user_id WHERE customer_id IS NULL;

ALTER TABLE complaints ADD COLUMN IF NOT EXISTS submitter_id INT NULL DEFAULT NULL AFTER user_id;
ALTER TABLE complaints ADD COLUMN IF NOT EXISTS restaurant_id INT NULL DEFAULT NULL AFTER submitter_id;
ALTER TABLE complaints ADD COLUMN IF NOT EXISTS description TEXT NULL DEFAULT NULL AFTER message;
ALTER TABLE complaints MODIFY status ENUM('open','in_progress','resolved') NOT NULL DEFAULT 'open';
UPDATE complaints SET submitter_id = user_id WHERE submitter_id IS NULL;
UPDATE complaints SET description = message WHERE description IS NULL;

CREATE TABLE IF NOT EXISTS `delivery_agents` (
    `id`                    INT         NOT NULL AUTO_INCREMENT,
    `user_id`               INT         NOT NULL,
    `vehicle_type`          VARCHAR(100)     NULL,
    `is_online`             TINYINT(1)  NOT NULL DEFAULT 0,
    `current_location_text` VARCHAR(255)     NULL,
    `total_earnings`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `is_approved`           TINYINT(1)  NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_delivery_agents_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `delivery_assignments` (
    `id`           INT NOT NULL AUTO_INCREMENT,
    `order_id`     INT NOT NULL,
    `agent_id`     INT NOT NULL,
    `assigned_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `picked_up_at` DATETIME NULL DEFAULT NULL,
    `delivered_at` DATETIME NULL DEFAULT NULL,
    `status`       ENUM('assigned','picked_up','delivered','cancelled') NOT NULL DEFAULT 'assigned',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `saved_restaurants` (
    `id`            INT NOT NULL AUTO_INCREMENT,
    `customer_id`   INT NOT NULL,
    `restaurant_id` INT NOT NULL,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_saved_restaurant` (`customer_id`, `restaurant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `delivery_addresses` (
    `id`           INT NOT NULL AUTO_INCREMENT,
    `customer_id`  INT NOT NULL,
    `label`        VARCHAR(100) NULL,
    `address_line` TEXT NOT NULL,
    `city`         VARCHAR(100) NULL,
    `is_default`   TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `platform_settings` (
    `id`            INT NOT NULL AUTO_INCREMENT,
    `setting_key`   VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO restaurants (`manager_id`, `name`, `description`, `cuisine_type`, `address`, `city`, `opening_hours`, `delivery_radius_km`, `is_open`, `is_approved`, `created_at`)
SELECT id, 'Food Hub Demo Restaurant', 'Default approved restaurant for manager demonstrations.', 'Multi Cuisine', 'House 12, Food Street', 'Dhaka', '10:00 AM - 10:00 PM', 8.00, 1, 1, NOW()
FROM users
WHERE email = 'manager@food.local'
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `description` = VALUES(`description`),
    `cuisine_type` = VALUES(`cuisine_type`),
    `address` = VALUES(`address`),
    `city` = VALUES(`city`),
    `opening_hours` = VALUES(`opening_hours`),
    `delivery_radius_km` = VALUES(`delivery_radius_km`),
    `is_open` = VALUES(`is_open`),
    `is_approved` = VALUES(`is_approved`);

SET @default_restaurant_id := (SELECT r.id FROM restaurants r JOIN users u ON u.id = r.manager_id WHERE u.email = 'manager@food.local' LIMIT 1);

UPDATE categories SET restaurant_id = @default_restaurant_id WHERE restaurant_id IS NULL;
UPDATE products SET restaurant_id = @default_restaurant_id WHERE restaurant_id IS NULL;

INSERT IGNORE INTO menu_categories (`restaurant_id`, `category_id`, `name`, `display_order`)
SELECT @default_restaurant_id, c.id, c.name, c.id
FROM categories c
WHERE c.restaurant_id = @default_restaurant_id;

INSERT IGNORE INTO menu_items (`restaurant_id`, `category_id`, `product_id`, `name`, `description`, `price`, `image_path`, `is_available`, `created_at`)
SELECT @default_restaurant_id, mc.id, p.id, p.name, p.description, p.price, p.image, IF(p.status = 'active', 1, 0), p.created_at
FROM products p
JOIN menu_categories mc ON mc.category_id = p.category_id AND mc.restaurant_id = @default_restaurant_id
WHERE p.restaurant_id = @default_restaurant_id
  AND p.menu_item_id IS NULL;

UPDATE products p
JOIN menu_items mi ON mi.product_id = p.id
SET p.menu_item_id = mi.id
WHERE p.restaurant_id = @default_restaurant_id;

UPDATE orders SET restaurant_id = @default_restaurant_id WHERE restaurant_id IS NULL;
UPDATE order_items oi JOIN products p ON p.id = oi.product_id SET oi.menu_item_id = p.menu_item_id WHERE oi.menu_item_id IS NULL;
UPDATE reviews r JOIN orders o ON o.id = r.order_id SET r.restaurant_id = o.restaurant_id WHERE r.restaurant_id IS NULL;
UPDATE complaints c JOIN orders o ON o.id = c.order_id SET c.restaurant_id = o.restaurant_id WHERE c.restaurant_id IS NULL;

INSERT IGNORE INTO delivery_agents (`user_id`, `vehicle_type`, `is_online`, `total_earnings`, `is_approved`)
SELECT id, 'Motorbike', 1, 0.00, 1
FROM users
WHERE email = 'delivery@food.local';

INSERT IGNORE INTO platform_settings (`setting_key`, `setting_value`)
SELECT `key`, `value` FROM settings;
