<section class="section-title">
    <h2>Menu</h2>
    <form method="get" action="../public/index.php?route=menu">
        <select name="category" onchange="this.form.submit()">
            <option value="0">All categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo sanitize($category['id']); ?>" <?php echo $category['id'] === $categoryId ? 'selected' : ''; ?>><?php echo sanitize($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</section>

<div class="grid grid-3">
    <?php if (empty($products)): ?>
        <div class="card"><div class="card-body"><p>No products found.</p></div></div>
    <?php endif; ?>

    <?php foreach ($products as $product): ?>
        <div class="card">
            <img src="../assets/images/<?php echo sanitize($product['image']); ?>" alt="<?php echo sanitize($product['name']); ?>" onerror="this.onerror=null;this.src='https://via.placeholder.com/400x300?text=Food';">
            <div class="card-body">
                <h3><?php echo sanitize($product['name']); ?></h3>
                <p><?php echo sanitize($product['category_name']); ?><?php echo !empty($product['restaurant_name']) ? ' - ' . sanitize($product['restaurant_name']) : ''; ?></p>
                <p><?php echo sanitize($product['description']); ?></p>
                <?php $discountPct = (float) ($product['discount_pct'] ?? 0); ?>
                <?php if ($discountPct > 0): ?>
                    <p><strong><?php echo formatCurrency((float)$product['price'] * (1 - ($discountPct / 100))); ?></strong> <small><del><?php echo formatCurrency((float)$product['price']); ?></del> <?php echo sanitize($discountPct); ?>% off</small></p>
                <?php else: ?>
                    <p><strong><?php echo formatCurrency((float)$product['price']); ?></strong></p>
                <?php endif; ?>
                <form action="../public/index.php?route=cart" method="post">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo sanitize($product['id']); ?>">
                    <button type="submit" class="button button-primary">Add to Cart</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
