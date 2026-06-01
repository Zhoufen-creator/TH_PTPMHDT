<?php

class OrderModel {
    private $conn;
    private $table = '`order`';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOrder($orderData) {
        $query = "INSERT INTO " . $this->table . " 
                  (name, phone, address)
                  VALUES
                  (:name, :phone, :address)";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $orderData['name'] = htmlspecialchars($orderData['name'] ?? '');
        $orderData['phone'] = htmlspecialchars($orderData['phone'] ?? '');
        $orderData['address'] = htmlspecialchars($orderData['address'] ?? '');

        // Bind values
        $stmt->bindParam(':name', $orderData['name']);
        $stmt->bindParam(':phone', $orderData['phone']);
        $stmt->bindParam(':address', $orderData['address']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function addOrderItem($orderId, $productId, $quantity, $price) {
        $query = "INSERT INTO order_item 
                  (order_id, product_id, quantity, price)
                  VALUES
                  (:order_id, :product_id, :quantity, :price)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);

        return $stmt->execute();
    }

    public function getOrderById($orderId) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getOrderItems($orderId) {
        $query = "SELECT * FROM order_item WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
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
