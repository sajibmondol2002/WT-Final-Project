<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Product.php';

class HomeController extends Controller
{
    public function index(): void
    {
        $categoryModel = new Category();
        $productModel = new Product();

        $categories = $categoryModel->all();
        $featured = $productModel->featured();

        // Featured restaurants
        $featuredRestaurants = db_fetch_all(
            "SELECT u.id, u.name, u.profile_picture FROM featured_restaurants fr JOIN users u ON u.id = fr.user_id WHERE u.status = ? ORDER BY fr.position LIMIT 6",
            's',
            ['active']
        );

        $this->render('home/index.php', [
            'categories' => $categories,
            'featured' => $featured,
            'featuredRestaurants' => $featuredRestaurants,
            'currentUser' => getCurrentUser(),
        ]);
    }
}
