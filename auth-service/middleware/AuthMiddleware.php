<?php
require '../src/JWTHandler.php';
require '../config/database.php';

class AuthMiddleware {
    private $jwt;
    private $conn;

    public function __construct($db) {
        $this->jwt = new JWTHandler();
        $this->conn = $db;
    }

    public function validateToken($token) {
        if (!$token) {
            return json_encode(["error" => "Token is required"]);
        }
        
        $decoded = $this->jwt->verifyToken($token);
        if (!$decoded) {
            return json_encode(["error" => "Invalid token"]);
        }
        
        // Check if token is blacklisted
        $query = "SELECT * FROM token_blacklist WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return json_encode(["error" => "Token has been revoked"]);
        }
        
        return json_encode(["valid" => true, "user" => $decoded]);
    }
}

// Example usage
header("Content-Type: application/json");
$database = new Database();
$db = $database->connect();
$authMiddleware = new AuthMiddleware($db);

$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

echo $authMiddleware->validateToken($token);
