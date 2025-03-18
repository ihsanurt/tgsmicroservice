<?php
class ProductService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProducts() {
        $query = "SELECT * FROM products ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($products);
    }

    public function getProductById($id) {
        $query = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product ? json_encode($product) : json_encode(["error" => "Product not found"]);
    }

    public function addProduct($name, $price) {
        $query = "INSERT INTO products (name, price) VALUES (:name, :price)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":price", $price);
        
        if ($stmt->execute()) {
            return json_encode(["message" => "Product added successfully"]);
        }
        return json_encode(["error" => "Failed to add product"]);
    }

    public function updateProduct($id, $name, $price) {
        $query = "UPDATE products SET name = :name, price = :price WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":price", $price);
        
        if ($stmt->execute()) {
            return json_encode(["message" => "Product updated successfully"]);
        }
        return json_encode(["error" => "Failed to update product"]);
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            return json_encode(["message" => "Product deleted successfully"]);
        }
        return json_encode(["error" => "Failed to delete product"]);
    }
}
?>
