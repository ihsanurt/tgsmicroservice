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
        // Implement token blacklist (store invalidated tokens in DB or cache)
        $query = "INSERT INTO token_blacklist (token) VALUES (:token)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        if ($stmt->execute()) {
            return json_encode(["message" => "Logged out successfully"]);
        }
        return json_encode(["error" => "Logout failed"]);
    }

    public function validateToken($token) {
        if ($this->jwt->verifyToken($token)) {
            // Check if token is blacklisted
            $query = "SELECT * FROM token_blacklist WHERE token = :token";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":token", $token);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return json_encode(["error" => "Invalid token"]);
            }
            return json_encode(["valid" => true]);
        }
        return json_encode(["error" => "Invalid token"]);
    }
}

// Handle API Requests
header("Content-Type: application/json");
$database = new Database();
$db = $database->connect();
$auth = new AuthController($db);

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents("php://input"), true);

if ($method == 'POST' && isset($_GET['register'])) {
    echo $auth->register($data['email'], $data['password']);
} elseif ($method == 'POST' && isset($_GET['login'])) {
    echo $auth->login($data['email'], $data['password']);
} elseif ($method == 'POST' && isset($_GET['logout'])) {
    echo $auth->logout($data['token']);
} elseif ($method == 'GET' && isset($_GET['validateToken'])) {
    echo $auth->validateToken($_GET['token']);
} else {
    echo json_encode(["error" => "Invalid request"]);
}
