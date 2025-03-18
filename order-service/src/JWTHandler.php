<?php
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class JWTHandler {
    private $secret;

    public function __construct() {
        $this->secret = $_ENV['JWT_SECRET'];
    }

    public function generateToken($user_id, $email) {
        $payload = [
            'iss' => 'order-service',
            'iat' => time(),
            'exp' => time() + 3600,
            'sub' => $user_id,
            'email' => $email
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function verifyToken($token) {
        try {
            return (array) JWT::decode($token, new Key($this->secret, 'HS256'));
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
