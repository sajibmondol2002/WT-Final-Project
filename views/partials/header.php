<header>
    <div class="container">
        <div class="site-title"><a href="../public/index.php?route=home" style="color:#fff;">Online Food Ordering</a></div>
        <nav>
            <a href="../public/index.php?route=home">Home</a>
            <a href="../public/index.php?route=menu">Menu</a>
            <a href="../public/index.php?route=cart">Cart (<?php echo cartCount(); ?>)</a>
            <?php if (!empty($currentUser)): ?>
                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                    <a href="../public/index.php?route=admin">Admin</a>
                <?php elseif (($currentUser['role'] ?? '') === 'restaurant_manager'): ?>
                    <a href="../public/index.php?route=restaurant">Restaurant</a>
                <?php elseif (($currentUser['role'] ?? '') === 'delivery_man'): ?>
                    <a href="../public/index.php?route=delivery">Delivery</a>
                <?php else: ?>
                    <a href="../public/index.php?route=order">My Orders</a>
                <?php endif; ?>
                <a href="../public/index.php?route=auth&action=logout">Logout</a>
            <?php else: ?>
                <a href="../public/index.php?route=auth&action=unified">Login</a>
                <a href="../public/index.php?route=auth&action=unified">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
