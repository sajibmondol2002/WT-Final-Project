-- 1. Database creation (Jodi age theke na thake)
CREATE DATABASE IF NOT EXISTS food_ordering_system;
USE food_ordering_system;

-- 2. Users Table (Customer, Admin, and Manager)
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'restaurant_manager', 'admin') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Restaurants Table
CREATE TABLE restaurants (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    manager_id INT(11),
    name VARCHAR(100) NOT NULL,
    cuisine_type VARCHAR(50),
    address TEXT,
    is_open TINYINT(1) DEFAULT 1,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. Menu Categories Table
CREATE TABLE menu_categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT(11),
    category_name VARCHAR(50) NOT NULL,
    display_order INT(11) DEFAULT 0,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
);

-- 5. Menu Items Table
CREATE TABLE menu_items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT(11),
    category_id INT(11),
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_path VARCHAR(255),
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id),
    FOREIGN KEY (category_id) REFERENCES menu_categories(id)
);

-- 6. Orders Table
CREATE TABLE orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    customer_id INT(11),
    restaurant_id INT(11),
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
);

-- 7. Reviews Table
CREATE TABLE reviews (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT(11),
    customer_id INT(11),
    rating INT(1) CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    manager_reply TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
);

-- --------------------------------------------------------
-- 8. Sample Data (Testing er jonno)
-- --------------------------------------------------------

-- Default Password: 'password123' (hash kora)
INSERT INTO users (name, email, password_hash, role) VALUES 
('Mir Md. Kawsur', 'manager@example.com', '$2y$10$7rLSvRl1Z7h.3p.9F86M8u7Jz.7M.A.5Wb.7Jv.7M.A.5Wb.', 'restaurant_manager');

-- Restaurant add kora
INSERT INTO restaurants (manager_id, name, cuisine_type, address) VALUES 
(1, 'Kawsur Kitchen', 'Bengali', 'Dhaka, Bangladesh');

-- Default Category
INSERT INTO menu_categories (restaurant_id, category_name) VALUES (1, 'Main Course');

-- Default Menu Item
INSERT INTO menu_items (restaurant_id, category_id, name, price, description) VALUES 
(1, 1, 'Beef Tehari', 250.00, 'Special Dhaka style tehari');