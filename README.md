# Online Food Ordering System

A simple PHP/MySQL online food ordering application designed for XAMPP.

## Features
- Customer registration and login
- Product browsing by category
- Shopping cart and checkout flow
- Order history for customers
- Admin dashboard for product, category, and order management
- Responsive user interface with a clean style
- Restaurant Manager section for menu, orders, reviews, and analytics
- Restaurant profile, menu categories/items, discounts, live AJAX order dashboard, review replies, and restaurant complaints
- Delivery Agent workflow for accepting deliveries, updating status, and tracking earnings
- Platform Admin tools for user management, platform oversight, and reports

## Setup
1. Place the project in your XAMPP `htdocs` folder.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Create a database named `online_food_ordering`.
5. Select that database, open the **Import** tab, and import `data/init.sql`.
   - `data/init.sql` is the complete database file: tables, settings, sample menu data, and default users.
   - `data/insert_admin.sql` is optional and only reseeds the four default users in an existing database.
   - If you already imported an older copy of `data/init.sql`, import `data/restaurant_manager_update.sql` once instead of recreating the database.
6. Update database settings in `config/database.php` if required.
7. Open the app in your browser: `http://localhost/Food/public/index.php?route=home`

## Default Credentials
Use the email address to log in.

| Role | Email | Password |
|------|-------|----------|
| Customer | `customer@food.local` | `Customer@123` |
| Delivery Agent | `delivery@food.local` | `Delivery@123` |
| Restaurant Manager | `manager@food.local` | `Manager@123` |
| Platform Admin | `admin@food.local` | `Admin@123` |

## Registration Approval
- Customer registrations are approved automatically and can log in immediately.
- Restaurant Manager and Delivery Agent registrations are inactive until a Platform Admin approves them.
- Platform Admin registrations are created inactive, but the new admin is allowed into User Management so they can approve their own account.

## Restaurant Manager Module
- Managers register with restaurant details and wait for admin approval.
- Approved managers can manage profile, open/closed status, categories, menu items, images, item availability, discounts, live orders, reviews, analytics, and restaurant complaints.
- The live order list refreshes by AJAX every 10 seconds on the Restaurant Orders page.

## Notes
- This project uses plain PHP and procedural MySQL access.
- For production, enable secure password storage, HTTPS, and validation.
