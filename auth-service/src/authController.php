<?php
require '../config/database.php';
require '../src/JWTHandler.php';

class AuthController {
    private $conn;
    private $jwt;

    public function __construct($db) {
        $this->conn = $db;
        $this->jwt = new JWTHandler();
    }

    public function register($email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (email, password) VALUES (:email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashedPassword);
        
        if ($stmt->execute()) {
            return json_encode(["message" => "User registered successfully"]);
        }
        return json_encode(["error" => "Failed to register user"]);
    }

    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $token = $this->jwt->generateToken($user['id'], $user['email']);
            return json_encode(["token" => $token]);
        }
        return json_encode(["error" => "Invalid credentials"]);
    }

    public function logout($token) {
        $query = "INSERT INTO token_blacklist (token) VALUES (:token)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        if ($stmt->execute()) {
            return json_encode(["message" => "Logged out successfully"]);
        }
        return json_encode(["error" => "Logout failed"]);
    }
}
?>
