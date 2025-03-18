<?php
require_once '../src/JWTHandler.php';
require_once '../config/database.php';

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
        
        return json_encode(["valid" => true, "user" => $decoded]);
    }
}
?>