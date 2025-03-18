<?php
require_once '../config/database.php';
require_once '../middleware/AuthMiddleware.php';
require_once '../src/ProductService.php';

use ProductService\Config\Database;

$database = new Database();
$db = $database->connect();
$authMiddleware = new AuthMiddleware($db);
$productService = new ProductService($db);

// Handle API Requests
header("Content-Type: application/json");

$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$authCheck = json_decode($authMiddleware->validateToken($token), true);

if (isset($authCheck['error'])) {
    echo json_encode($authCheck);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['products'])) {
    echo $productService->getProducts();
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['product'])) {
    echo $productService->getProductById($_GET['product']);
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT' && isset($_GET['update'])) {
    $data = json_decode(file_get_contents("php://input"), true);
    echo $productService->updateProduct($data['id'], $data['name'], $data['price']);
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['delete'])) {
    $data = json_decode(file_get_contents("php://input"), true);
    echo $productService->deleteProduct($data['id']);
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
