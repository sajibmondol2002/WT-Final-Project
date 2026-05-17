<section class="hero">
    <h1>Fresh meals delivered fast</h1>
    <p>Browse delicious dishes, add to cart, and place your order in minutes. Your favorite restaurant menu is ready to explore.</p>
</section>

<section class="section-title">
    <h2>Explore categories</h2>
    <a class="button button-primary" href="../public/index.php?route=menu">View full menu</a>
</section>

<div class="grid grid-3">
    <?php foreach ($categories as $category): ?>
        <div class="card">
            <div class="card-body">
                <h3><?php echo sanitize($category['name']); ?></h3>
                <p>Discover great dishes from <?php echo sanitize($category['name']); ?>.</p>
                <a class="button button-primary" href="../public/index.php?route=menu&category=<?php echo sanitize($category['id']); ?>">Browse</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<section class="section-title" style="margin-top: 40px;">
    <h2>Featured dishes</h2>
</section>
<div class="grid grid-3">
    <?php foreach ($featured as $product): ?>
        <div class="card">
            <img src="../assets/images/<?php echo sanitize($product['image']); ?>" alt="<?php echo sanitize($product['name']); ?>" onerror="this.onerror=null;this.src='https://via.placeholder.com/400x300?text=Food';">
            <div class="card-body">
                <h3><?php echo sanitize($product['name']); ?></h3>
                <p><?php echo sanitize($product['category_name']); ?> &mdash; <?php echo formatCurrency((float)$product['price']); ?></p>
                <p><?php echo sanitize($product['description']); ?></p>
                <form action="../public/index.php?route=cart" method="post" style="margin-top:auto;">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo sanitize($product['id']); ?>">
                    <button type="submit" class="button button-primary">Add to Cart</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<section class="section-title" style="margin-top: 40px;">
    <h2>Featured Restaurants</h2>
</section>
<div class="grid grid-3">
    <?php foreach ($featuredRestaurants as $r): ?>
        <div class="card">
            <div class="card-body">
                <h3><?php echo sanitize($r['name']); ?></h3>
                <?php if (!empty($r['profile_picture'])): ?>
                    <img src="../assets/images/profiles/<?php echo sanitize($r['profile_picture']); ?>" alt="<?php echo sanitize($r['name']); ?>" style="max-width:100%;height:150px;object-fit:cover;">
                <?php endif; ?>
                <p><a class="button button-primary" href="../public/index.php?route=restaurant&action=menu&restaurant=<?php echo sanitize($r['id']); ?>">View menu</a></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
