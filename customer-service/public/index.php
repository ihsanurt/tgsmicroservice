/* CUSTOMER SERVICE INDEX.PHP */
<?php
require_once '../config/database.php';
require_once '../middleware/AuthMiddleware.php';
require_once '../src/CustomerService.php';

header("Content-Type: application/json");

$database = new Database();
$db = $database->connect();
$authMiddleware = new AuthMiddleware($db);
$customerService = new CustomerService($db);

$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
$authCheck = json_decode($authMiddleware->validateToken($token), true);

if (isset($authCheck['error'])) {
    echo json_encode($authCheck);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['customers'])) {
    echo $customerService->getCustomers();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['add'])) {
    $data = json_decode(file_get_contents("php://input"), true);
    echo $customerService->addCustomer($data['name'], $data['email']);
} elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['delete'])) {
    $data = json_decode(file_get_contents("php://input"), true);
    echo $customerService->deleteCustomer($data['id']);
} else {
    echo json_encode(["error" => "Invalid request"]);
}