<?php

class OrderModel {
    private $conn;
    private $table = 'orders';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOrder($orderData) {
        $query = "INSERT INTO " . $this->table . "
                  (order_code, user_name, user_email, user_phone, user_address, 
                   total_price, payment_method, order_status, vnpay_transaction_id, created_at)
                  VALUES
                  (:order_code, :user_name, :user_email, :user_phone, :user_address, 
                   :total_price, :payment_method, :order_status, :vnpay_transaction_id, NOW())";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $orderData['order_code'] = htmlspecialchars($orderData['order_code']);
        $orderData['user_name'] = htmlspecialchars($orderData['user_name']);
        $orderData['user_email'] = htmlspecialchars($orderData['user_email']);
        $orderData['user_phone'] = htmlspecialchars($orderData['user_phone']);
        $orderData['user_address'] = htmlspecialchars($orderData['user_address']);
        $orderData['total_price'] = (float)$orderData['total_price'];
        $orderData['payment_method'] = htmlspecialchars($orderData['payment_method']);
        $orderData['order_status'] = $orderData['order_status'] ?? 'pending';
        $orderData['vnpay_transaction_id'] = $orderData['vnpay_transaction_id'] ?? null;

        // Bind values
        $stmt->bindParam(':order_code', $orderData['order_code']);
        $stmt->bindParam(':user_name', $orderData['user_name']);
        $stmt->bindParam(':user_email', $orderData['user_email']);
        $stmt->bindParam(':user_phone', $orderData['user_phone']);
        $stmt->bindParam(':user_address', $orderData['user_address']);
        $stmt->bindParam(':total_price', $orderData['total_price']);
        $stmt->bindParam(':payment_method', $orderData['payment_method']);
        $stmt->bindParam(':order_status', $orderData['order_status']);
        $stmt->bindParam(':vnpay_transaction_id', $orderData['vnpay_transaction_id']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function addOrderItem($orderId, $productId, $productName, $quantity, $price) {
        $query = "INSERT INTO order_items 
                  (order_id, product_id, product_name, quantity, price, subtotal)
                  VALUES
                  (:order_id, :product_id, :product_name, :quantity, :price, :subtotal)";

        $stmt = $this->conn->prepare($query);

        $subtotal = $quantity * $price;

        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':product_name', $productName);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':subtotal', $subtotal);

        return $stmt->execute();
    }

    public function getOrderByCode($orderCode) {
        $query = "SELECT * FROM " . $this->table . " WHERE order_code = :order_code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_code', $orderCode);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getOrderById($orderId) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getOrderItems($orderId) {
        $query = "SELECT * FROM order_items WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateOrderStatus($orderId, $status, $vnpayTransactionId = null) {
        $query = "UPDATE " . $this->table . " 
                  SET order_status = :order_status";
        
        if ($vnpayTransactionId) {
            $query .= ", vnpay_transaction_id = :vnpay_transaction_id";
        }
        
        $query .= " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_status', $status);
        
        if ($vnpayTransactionId) {
            $stmt->bindParam(':vnpay_transaction_id', $vnpayTransactionId);
        }
        
        $stmt->bindParam(':id', $orderId);

        return $stmt->execute();
    }

    public function getOrderByVNPayTransactionId($transactionId) {
        $query = "SELECT * FROM " . $this->table . " WHERE vnpay_transaction_id = :vnpay_transaction_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':vnpay_transaction_id', $transactionId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getAllOrders($limit = 50, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function countOrders() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }
}

?>
