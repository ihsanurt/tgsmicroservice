<?php
class CustomerService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCustomers() {
        $query = "SELECT * FROM customers ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function addCustomer($name, $email) {
        $query = "INSERT INTO customers (name, email) VALUES (:name, :email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        return $stmt->execute() ? json_encode(["message" => "Customer added successfully"])
                                : json_encode(["error" => "Failed to add customer"]);
    }

    public function deleteCustomer($id) {
        $query = "DELETE FROM customers WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute() ? json_encode(["message" => "Customer deleted successfully"])
                                : json_encode(["error" => "Failed to delete customer"]);
    }
}