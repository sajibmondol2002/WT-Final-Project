<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Product.php';

class MenuController extends Controller
{
    public function index(): void
    {
        requireLogin();
        
        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : 0;
        $categoryModel = new Category();
        $productModel = new Product();

        $categories = $categoryModel->all();
        $products = $productModel->all($categoryId);

        $this->render('menu/index.php', [
            'categories' => $categories,
            'products' => $products,
            'currentUser' => getCurrentUser(),
            'categoryId' => $categoryId,
        ]);
    }
}
