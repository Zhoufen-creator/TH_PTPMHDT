<?php

require_once 'app/models/RedisHelper.php';
require_once 'app/models/ProductModel.php';

class CartService {
    private $redis;
    private $productModel;
    private $cartKeyPrefix = "cart:";
    private $cartExpiry = 86400; // 24 hours

    public function __construct($db) {
        $this->redis = new RedisService();
        $this->productModel = new ProductModel($db);
    }

    private function getCartKey() {
        return $this->cartKeyPrefix . session_id();
    }

    public function addToCart($productId, $quantity = 1) {
        $product = $this->productModel->getProductById($productId);
        
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Sản phẩm không tồn tại'
            ];
        }

        $cartKey = $this->getCartKey();
        $cart = $this->redis->get($cartKey) ?? [];

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float)$product->price,
                'quantity' => $quantity,
                'image' => $product->image ?? null
            ];
        }

        $this->redis->set($cartKey, $cart, $this->cartExpiry);

        return [
            'success' => true,
            'message' => 'Sản phẩm đã được thêm vào giỏ hàng',
            'cart' => $cart
        ];
    }

    public function getCart() {
        $cartKey = $this->getCartKey();
        return $this->redis->get($cartKey) ?? [];
    }

    public function removeFromCart($productId) {
        $cartKey = $this->getCartKey();
        $cart = $this->redis->get($cartKey) ?? [];

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->redis->set($cartKey, $cart, $this->cartExpiry);
            return [
                'success' => true,
                'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng',
                'cart' => $cart
            ];
        }

        return [
            'success' => false,
            'message' => 'Sản phẩm không tìm thấy trong giỏ hàng'
        ];
    }

    public function updateQuantity($productId, $quantity) {
        if ($quantity < 0) {
            return [
                'success' => false,
                'message' => 'Số lượng không hợp lệ'
            ];
        }

        $cartKey = $this->getCartKey();
        $cart = $this->redis->get($cartKey) ?? [];

        if (!isset($cart[$productId])) {
            return [
                'success' => false,
                'message' => 'Sản phẩm không tìm thấy trong giỏ hàng'
            ];
        }

        if ($quantity === 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['quantity'] = $quantity;
        }

        $this->redis->set($cartKey, $cart, $this->cartExpiry);

        return [
            'success' => true,
            'message' => 'Cập nhật số lượng thành công',
            'cart' => $cart
        ];
    }

    public function clearCart() {
        $cartKey = $this->getCartKey();
        $this->redis->delete($cartKey);

        return [
            'success' => true,
            'message' => 'Giỏ hàng đã được xóa'
        ];
    }

    public function getCartTotal() {
        $cart = $this->getCart();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return round($total, 2);
    }

    public function getCartCount() {
        $cart = $this->getCart();
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return $count;
    }

    public function getCartDetails() {
        $cart = $this->getCart();
        $total = 0;
        $count = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
            $count += $item['quantity'];
        }

        return [
            'items' => $cart,
            'totalPrice' => round($total, 2),
            'itemCount' => $count,
            'itemsCount' => count($cart)
        ];
    }
}

?>
