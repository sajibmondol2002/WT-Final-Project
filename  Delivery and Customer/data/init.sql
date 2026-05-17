-- ============================================================
--  Online Food Ordering System — Database Initialisation
--  Verified against source code (models/, controllers/, views/)
--  Place in: data/init.sql
--  Import: mysql -u root online_food_ordering < data/init.sql
--  Note: Includes default login accounts for all four roles.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `online_food_ordering`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `online_food_ordering`;

-- ------------------------------------------------------------
-- 1. USERS
-- Columns verified from:
--   User::findByEmail()          → id, name, email, password, phone, role
--   User::findById()             → id, name, email, phone, role, status, vehicle_type, profile_picture, is_available
--   User::all()                  → id, name, email, phone, role, status, created_at
--   User::create()               → name, email, password, phone, role, status, created_at
--   User::updateStatus()         → status
--   User::updateAvailability()   → is_available
--   User::updateDeliveryProfile()→ name, phone, vehicle_type, profile_picture
--   AuthController               → role ENUM: customer, admin, restaurant_manager, delivery_man
--                                → status: customer → 'active', others → 'inactive'
--   Delivery drivers need:       → vehicle_type, profile_picture, is_available
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`              INT           NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(150)  NOT NULL,
    `email`           VARCHAR(200)  NOT NULL,
    `password`        VARCHAR(255)  NOT NULL,
    `password_hash`   VARCHAR(255)      NULL DEFAULT NULL,
    `phone`           VARCHAR(20)       NULL DEFAULT NULL,
    `role`            ENUM('customer','admin','restaurant_manager','delivery_man')
                                  NOT NULL DEFAULT 'customer',
    `status`          ENUM('active','inactive')
                                  NOT NULL DEFAULT 'active',
    `vehicle_type`    VARCHAR(100)      NULL DEFAULT NULL,
    `profile_pic`     VARCHAR(255)      NULL DEFAULT NULL,
    `profile_picture` VARCHAR(255)      NULL DEFAULT NULL,
    `is_active`       TINYINT(1)    NOT NULL DEFAULT 1,
    `is_available`    TINYINT(1)        NULL DEFAULT NULL,
    `created_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    UNIQUE KEY `uq_restaurants_manager` (`manager_id`),
    CONSTRAINT `fk_restaurants_manager`
        FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. CATEGORIES
-- Columns verified from:
--   Category::all()    → id, name
--   Category::find()   → id, name, description
--   Category::create() → name, description, created_at
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `restaurant_id` INT            NULL DEFAULT NULL,
    `name`        VARCHAR(100) NOT NULL,
    `description` TEXT             NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu_categories` (
    `id`            INT          NOT NULL AUTO_INCREMENT,
    `restaurant_id` INT          NOT NULL,
    `category_id`   INT              NULL DEFAULT NULL,
    `name`          VARCHAR(100) NOT NULL,
    `display_order` INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_menu_categories_legacy` (`restaurant_id`, `category_id`),
    KEY `idx_menu_categories_restaurant` (`restaurant_id`),
    CONSTRAINT `fk_menu_categories_restaurant`
        FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. PRODUCTS
-- Columns verified from:
--   Product::featured() → id, name, description, price, image, category_id (JOIN)
--   Product::all()      → id, name, description, price, image, category_id (JOIN)
--   Product::find()     → * (all columns)
--   Product::create()   → category_id, name, description, price, image, status, created_at
--   inc/functions.php   → id, price / id, name, price, image
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
    `id`          INT            NOT NULL AUTO_INCREMENT,
    `restaurant_id` INT              NULL DEFAULT NULL,
    `menu_item_id`  INT              NULL DEFAULT NULL,
    `category_id` INT            NOT NULL,
    `name`        VARCHAR(200)   NOT NULL,
    `description` TEXT               NULL,
    `price`       DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `image`       VARCHAR(255)       NULL DEFAULT 'placeholder.png',
    `status`      ENUM('active','inactive')
                               NOT NULL DEFAULT 'active',
    `created_at`  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_products_category` (`category_id`),
    CONSTRAINT `fk_products_category`
        FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    KEY `idx_menu_items_category` (`category_id`),
    CONSTRAINT `fk_menu_items_restaurant`
        FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_menu_items_category`
        FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
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
    KEY `idx_discounts_item` (`menu_item_id`),
    CONSTRAINT `fk_discounts_restaurant`
        FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_discounts_item`
        FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. ORDERS
-- Columns verified from:
--   Order::create()              → user_id, total_amount, delivery_address, status, created_at
--   Order::allByUser()           → id, total_amount, status, delivery_address, created_at
--   Order::all()                 → id, total_amount, status, created_at + JOIN users.name
--   Order::availableAssignments()→ id, total_amount, delivery_address, delivery_status, created_at
--   Order::assignToAgent()       → delivery_agent_id, delivery_status
--   Order::allAssignedToAgent()  → * (all columns)
--   Order::updateDeliveryStatus()→ delivery_status, status
--   Order::calculateEarnings()   → delivery_agent_id, delivery_status, total_amount
--
-- status ENUM verified from views/restaurant/orders.php & views/admin/orders.php:
--   pending | preparing | delivered | cancelled
--
-- delivery_status ENUM verified from views/delivery/assignments.php:
--   pending | picked_up | on_the_way | delivered
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
    `id`                INT            NOT NULL AUTO_INCREMENT,
    `user_id`           INT            NOT NULL,
    `customer_id`       INT                NULL DEFAULT NULL,
    `restaurant_id`     INT                NULL DEFAULT NULL,
    `agent_id`          INT                NULL DEFAULT NULL,
    `payment_method`    VARCHAR(50)        NULL DEFAULT 'cash_on_delivery',
    `subtotal`          DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `delivery_fee`      DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `total_amount`      DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `delivery_address`  TEXT           NOT NULL,
    `status`            ENUM('pending','accepted','preparing','ready','picked_up','delivered','cancelled')
                                       NOT NULL DEFAULT 'pending',
    `estimated_delivery_minutes` INT       NULL DEFAULT 45,
    `delivery_agent_id` INT                NULL DEFAULT NULL,
    `delivery_status`   ENUM('pending','picked_up','on_the_way','delivered')
                                           NULL DEFAULT NULL,
    `delivered_at`      DATETIME       NULL DEFAULT NULL,
    `created_at`        DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_orders_user`           (`user_id`),
    KEY `idx_orders_delivery_agent` (`delivery_agent_id`),
    CONSTRAINT `fk_orders_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_orders_delivery_agent`
        FOREIGN KEY (`delivery_agent_id`) REFERENCES `users` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. ORDER ITEMS
-- Columns verified from:
--   OrderItem::create()       → order_id, product_id, quantity, price, subtotal
--   OrderItem::allByOrderId() → * + JOIN products.name
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `order_items` (
    `id`         INT            NOT NULL AUTO_INCREMENT,
    `order_id`   INT            NOT NULL,
    `product_id` INT            NOT NULL,
    `menu_item_id` INT              NULL DEFAULT NULL,
    `quantity`   INT            NOT NULL DEFAULT 1,
    `price`      DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `unit_price` DECIMAL(10,2)      NULL DEFAULT NULL,
    `subtotal`   DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `discount_id` INT              NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_order_items_order`   (`order_id`),
    KEY `idx_order_items_product` (`product_id`),
    CONSTRAINT `fk_order_items_order`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_order_items_product`
        FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. REVIEWS
-- Columns verified from:
--   Review::all()              → id, order_id, rating, comment, created_at
--                                + JOIN users.name, orders.total_amount, orders.status
--   Review::create()           → order_id, user_id, rating, comment, created_at
--   Review::existsByOrderId()  → id (WHERE order_id)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `reviews` (
    `id`         INT      NOT NULL AUTO_INCREMENT,
    `order_id`   INT      NOT NULL,
    `user_id`    INT      NOT NULL,
    `customer_id` INT         NULL DEFAULT NULL,
    `restaurant_id` INT       NULL DEFAULT NULL,
    `rating`     TINYINT  NOT NULL DEFAULT 5,
    `comment`    TEXT         NULL,
    `manager_reply` TEXT      NULL DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_reviews_order` (`order_id`),
    KEY `idx_reviews_user` (`user_id`),
    CONSTRAINT `fk_reviews_order`
        FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_reviews_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. COMPLAINTS
-- Customer complaints for orders or general issues
-- Fields: order_id (optional), user_id (customer), subject, message, status (open,resolved), admin_note, resolved_at
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `complaints` (
    `id`           INT         NOT NULL AUTO_INCREMENT,
    `order_id`     INT         NULL DEFAULT NULL,
    `user_id`      INT         NOT NULL,
    `submitter_id` INT         NULL DEFAULT NULL,
    `restaurant_id` INT        NULL DEFAULT NULL,
    `subject`      VARCHAR(255) NOT NULL,
    `message`      TEXT        NOT NULL,
    `description`  TEXT        NULL DEFAULT NULL,
    `status`       ENUM('open','in_progress','resolved') NOT NULL DEFAULT 'open',
    `admin_note`   TEXT        NULL DEFAULT NULL,
    `resolved_by`  INT         NULL DEFAULT NULL,
    `resolved_at`  DATETIME    NULL DEFAULT NULL,
    `created_at`   DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_complaints_user` (`user_id`),
    KEY `idx_complaints_order` (`order_id`),
    CONSTRAINT `fk_complaints_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_complaints_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. FEATURED RESTAURANTS
-- Maps restaurant user_id to a featured list with ordering
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `featured_restaurants` (
    `id`         INT NOT NULL AUTO_INCREMENT,
    `user_id`    INT NOT NULL,
    `position`   INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_featured_user` (`user_id`),
    CONSTRAINT `fk_featured_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. SETTINGS
-- Key-value settings for platform configuration
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT NULL DEFAULT NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `delivery_agents` (
    `id`                    INT         NOT NULL AUTO_INCREMENT,
    `user_id`               INT         NOT NULL,
    `vehicle_type`          VARCHAR(100)     NULL,
    `is_online`             TINYINT(1)  NOT NULL DEFAULT 0,
    `current_location_text` VARCHAR(255)     NULL,
    `total_earnings`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `is_approved`           TINYINT(1)  NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_delivery_agents_user` (`user_id`),
    CONSTRAINT `fk_delivery_agents_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `delivery_assignments` (
    `id`           INT NOT NULL AUTO_INCREMENT,
    `order_id`     INT NOT NULL,
    `agent_id`     INT NOT NULL,
    `assigned_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `picked_up_at` DATETIME NULL DEFAULT NULL,
    `delivered_at` DATETIME NULL DEFAULT NULL,
    `status`       ENUM('assigned','picked_up','delivered','cancelled') NOT NULL DEFAULT 'assigned',
    PRIMARY KEY (`id`),
    KEY `idx_delivery_assignments_order` (`order_id`),
    KEY `idx_delivery_assignments_agent` (`agent_id`),
    CONSTRAINT `fk_delivery_assignments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_delivery_assignments_agent` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `saved_restaurants` (
    `id`            INT NOT NULL AUTO_INCREMENT,
    `customer_id`   INT NOT NULL,
    `restaurant_id` INT NOT NULL,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_saved_restaurant` (`customer_id`, `restaurant_id`),
    CONSTRAINT `fk_saved_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_saved_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `delivery_addresses` (
    `id`           INT NOT NULL AUTO_INCREMENT,
    `customer_id`  INT NOT NULL,
    `label`        VARCHAR(100) NULL,
    `address_line` TEXT NOT NULL,
    `city`         VARCHAR(100) NULL,
    `is_default`   TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_delivery_addresses_customer` (`customer_id`),
    CONSTRAINT `fk_delivery_addresses_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `platform_settings` (
    `id`            INT NOT NULL AUTO_INCREMENT,
    `setting_key`   VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Compatibility: existing old schema may still use k/v columns
SET @rename_k = (
    SELECT IF(COUNT(*) > 0,
        'ALTER TABLE settings CHANGE COLUMN `k` `key` VARCHAR(100) NOT NULL UNIQUE',
        'SELECT 1')
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings' AND COLUMN_NAME = 'k'
);
PREPARE stmt FROM @rename_k;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @rename_v = (
    SELECT IF(COUNT(*) > 0,
        'ALTER TABLE settings CHANGE COLUMN `v` `value` TEXT NULL DEFAULT NULL',
        'SELECT 1')
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'settings' AND COLUMN_NAME = 'v'
);
PREPARE stmt FROM @rename_v;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Default platform settings
INSERT IGNORE INTO `settings` (`key`, `value`) VALUES
('commission_rate', '10'),
('base_delivery_fee', '20.00'),
('per_km_fee', '5.00'),
('estimated_time_formula', 'base + distance * per_km');
-- on-time threshold (minutes) used for delivery performance reporting
INSERT IGNORE INTO `settings` (`key`, `value`) VALUES ('on_time_threshold_minutes', '30');


-- ============================================================
--  SEED DATA - Default Users, Categories & Products
-- ============================================================

-- Default login accounts.
-- The app logs in with email address. Passwords are listed in README.md and AUTHENTICATION.md.
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

INSERT INTO `restaurants` (`manager_id`, `name`, `description`, `cuisine_type`, `address`, `city`, `opening_hours`, `delivery_radius_km`, `is_open`, `is_approved`, `created_at`)
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

INSERT INTO `categories` (`name`, `description`) VALUES
('Burgers',       'Juicy flame-grilled burgers and sliders'),
('Pizzas',        'Stone-baked pizzas with fresh toppings'),
('Pasta',         'Classic Italian pasta dishes'),
('Fried Chicken', 'Crispy fried chicken pieces and meals'),
('Rice & Biryani','Aromatic rice dishes and biryanis'),
('Sandwiches',    'Freshly made sandwiches and wraps'),
('Desserts',      'Cakes, brownies, ice cream and more'),
('Drinks',        'Cold drinks, juices and shakes');

INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `image`, `status`) VALUES
-- Burgers
(1, 'Classic Beef Burger',     'Quarter-pound beef patty with lettuce, tomato and cheese',        5.99,  'placeholder.png', 'active'),
(1, 'Double Smash Burger',     'Two smashed beef patties, caramelised onion and special sauce',   8.49,  'placeholder.png', 'active'),
(1, 'Chicken Burger',          'Crispy fried chicken fillet with coleslaw and mayo',              6.49,  'placeholder.png', 'active'),
(1, 'Veggie Burger',           'Plant-based patty with avocado, tomato and lettuce',              5.49,  'placeholder.png', 'active'),
-- Pizzas
(2, 'Margherita Pizza',        'Classic tomato base, fresh mozzarella and basil',                 8.99,  'placeholder.png', 'active'),
(2, 'Pepperoni Pizza',         'Loaded pepperoni on a rich tomato sauce',                        10.49,  'placeholder.png', 'active'),
(2, 'BBQ Chicken Pizza',       'BBQ base with grilled chicken, onion and peppers',               11.49,  'placeholder.png', 'active'),
(2, 'Veggie Supreme Pizza',    'Mixed vegetables on a tomato and pesto base',                     9.99,  'placeholder.png', 'active'),
-- Pasta
(3, 'Spaghetti Bolognese',     'Slow-cooked beef ragu with spaghetti and parmesan',               8.99,  'placeholder.png', 'active'),
(3, 'Penne Arrabbiata',        'Penne in a spicy tomato sauce with garlic and chilli',            7.49,  'placeholder.png', 'active'),
(3, 'Chicken Alfredo',         'Creamy Alfredo sauce with grilled chicken and fettuccine',        9.99,  'placeholder.png', 'active'),
-- Fried Chicken
(4, 'Fried Chicken (4 pcs)',   '4 pieces of crispy golden fried chicken',                        7.99,  'placeholder.png', 'active'),
(4, 'Chicken Strips (6 pcs)',  '6 tender chicken strips with dipping sauce',                     6.99,  'placeholder.png', 'active'),
(4, 'Spicy Chicken Meal',      'Spicy fried chicken with fries and a drink',                    10.49,  'placeholder.png', 'active'),
-- Rice & Biryani
(5, 'Chicken Biryani',         'Aromatic basmati rice slow-cooked with spiced chicken',           9.49,  'placeholder.png', 'active'),
(5, 'Beef Kacchi Biryani',     'Traditional kacchi style beef biryani with raita',               11.99,  'placeholder.png', 'active'),
(5, 'Vegetable Fried Rice',    'Wok-fried rice with fresh vegetables and egg',                    6.99,  'placeholder.png', 'active'),
-- Sandwiches
(6, 'Club Sandwich',           'Triple-decker chicken, bacon, lettuce and tomato',                6.49,  'placeholder.png', 'active'),
(6, 'Grilled Cheese Sandwich', 'Melted cheddar and mozzarella on toasted sourdough',              4.99,  'placeholder.png', 'active'),
(6, 'Tuna Mayo Wrap',          'Tuna, mayo and sweetcorn in a flour tortilla wrap',               5.49,  'placeholder.png', 'active'),
-- Desserts
(7, 'Chocolate Brownie',       'Warm gooey chocolate brownie with vanilla ice cream',             4.49,  'placeholder.png', 'active'),
(7, 'Cheesecake Slice',        'New York style cheesecake with berry coulis',                     4.99,  'placeholder.png', 'active'),
(7, 'Ice Cream Sundae',        'Three scoops of ice cream with toppings of your choice',          3.99,  'placeholder.png', 'active'),
-- Drinks
(8, 'Coca-Cola (500ml)',       'Ice-cold Coca-Cola',                                              1.49,  'placeholder.png', 'active'),
(8, 'Fresh Orange Juice',      'Freshly squeezed orange juice',                                   2.49,  'placeholder.png', 'active'),
(8, 'Mango Lassi',             'Chilled yoghurt and mango blended drink',                         2.99,  'placeholder.png', 'active'),
(8, 'Mineral Water (500ml)',   'Still mineral water',                                             0.99,  'placeholder.png', 'active');

-- Link the starter menu to the default restaurant manager so Restaurant Manager
-- dashboards, orders, reviews, and analytics have owned data to work with.
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

INSERT IGNORE INTO delivery_agents (`user_id`, `vehicle_type`, `is_online`, `total_earnings`, `is_approved`)
SELECT id, 'Motorbike', 1, 0.00, 1
FROM users
WHERE email = 'delivery@food.local';

INSERT IGNORE INTO platform_settings (`setting_key`, `setting_value`)
SELECT `key`, `value` FROM settings;
