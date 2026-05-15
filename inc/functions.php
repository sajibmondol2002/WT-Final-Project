<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function getCurrentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user']);
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function isRestaurantManager(): bool
{
    return isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'restaurant_manager';
}

function isDeliveryMan(): bool
{
    return isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'delivery_man';
}

function isCustomer(): bool
{
    return isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'customer';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('public/index.php?route=auth&action=unified');
    }
}

function requireAdmin(): void
{
    $current = $_SERVER['PHP_SELF'] ?? '';
    $loginPath = strpos($current, '/admin/') !== false ? 'login.php' : 'admin/login.php';
    if (!isAdmin()) {
        redirect($loginPath);
    }
}

function requireRestaurantManager(): void
{
    if (!isRestaurantManager()) {
        redirect('public/index.php?route=auth&action=unified');
    }
}

function requireDeliveryMan(): void
{
    if (!isDeliveryMan()) {
        redirect('public/index.php?route=auth&action=unified');
    }
}

function cartItems(): array
{
    return $_SESSION['cart'] ?? [];
}

function cartCount(): int
{
    return array_sum(cartItems());
}

function cartTotal(): float
{
    $total = 0.0;
    $items = cartItems();
    if (empty($items)) {
        return 0.0;
    }

    $ids = array_keys($items);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT id, price FROM products WHERE id IN ($placeholders) AND status = 'active'";
    $types = str_repeat('i', count($ids));
    $params = $ids;
    $rows = db_fetch_all($sql, $types, $params);

    foreach ($rows as $row) {
        $total += $row['price'] * ($items[$row['id']] ?? 0);
    }

    return $total;
}

function addToCart(int $productId, int $quantity = 1): void
{
    $items = cartItems();
    $items[$productId] = max(1, ($items[$productId] ?? 0) + $quantity);
    $_SESSION['cart'] = $items;
}

function updateCart(array $quantities): void
{
    $items = cartItems();
    foreach ($quantities as $productId => $quantity) {
        $quantity = max(0, (int) $quantity);
        if ($quantity > 0) {
            $items[$productId] = $quantity;
        } else {
            unset($items[$productId]);
        }
    }
    $_SESSION['cart'] = $items;
}

function clearCart(): void
{
    unset($_SESSION['cart']);
}

function formatCurrency(float $amount): string
{
    return '₱' . number_format($amount, 2);
}

function getCartProducts(): array
{
    $items = cartItems();
    if (empty($items)) {
        return [];
    }

    $ids = array_keys($items);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT id, name, price, image FROM products WHERE id IN ($placeholders) AND status = 'active'";
    $types = str_repeat('i', count($ids));
    $params = $ids;
    $products = db_fetch_all($sql, $types, $params);

    foreach ($products as &$product) {
        $product['quantity'] = $items[$product['id']];
        $product['subtotal'] = $product['price'] * $product['quantity'];
    }

    return $products;
}
