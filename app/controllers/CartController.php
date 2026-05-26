<?php

require_once 'app/config/database.php';
require_once 'app/models/CartService.php';
require_once 'app/models/ProductModel.php';

class CartController {
    private $db;
    private $cartService;
    private $productModel;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->cartService = new CartService($this->db);
        $this->productModel = new ProductModel($this->db);
    }

    public function index() {
        $cartDetails = $this->cartService->getCartDetails();
        include 'app/views/cart_index.php';
    }

    public function addToCart($productId) {
        $quantity = $_POST['quantity'] ?? 1;
        $quantity = (int)filter_var($quantity, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1))) ?: 1;

        $result = $this->cartService->addToCart($productId, $quantity);

        if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        // Redirect if not AJAX
        if ($result['success']) {
            header('Location: /Cart/index');
        } else {
            header('Location: /Product/show/' . $productId);
        }
    }

    public function removeFromCart($productId) {
        $result = $this->cartService->removeFromCart($productId);

        if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        header('Location: /Cart/index');
    }

    public function updateQuantity($productId) {
        $quantity = $_POST['quantity'] ?? 0;
        $quantity = (int)filter_var($quantity, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0))) ?: 0;

        $result = $this->cartService->updateQuantity($productId, $quantity);

        if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        header('Location: /Cart/index');
    }

    public function clearCart() {
        $result = $this->cartService->clearCart();

        if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        header('Location: /Cart/index');
    }

    public function getCart() {
        header('Content-Type: application/json');
        $cartDetails = $this->cartService->getCartDetails();
        echo json_encode($cartDetails);
    }
}

?>
