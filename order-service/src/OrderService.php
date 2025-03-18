<?php
class OrderService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getOrders() {
        $query = "SELECT * FROM orders ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function createOrder($customer_id, $product_id, $quantity) {
        $query = "INSERT INTO orders (customer_id, product_id, quantity) VALUES (:customer_id, :product_id, :quantity)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":customer_id", $customer_id);
        $stmt->bindParam(":product_id", $product_id);
        $stmt->bindParam(":quantity", $quantity);

        return $stmt->execute() ? json_encode(["message" => "Order placed successfully"]) 
                                : json_encode(["error" => "Failed to place order"]);
    }

    public function deleteOrder($id) {
        $query = "DELETE FROM orders WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        return $stmt->execute() ? json_encode(["message" => "Order deleted successfully"]) 
                                : json_encode(["error" => "Failed to delete order"]);
    }
}
?>
