<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization,ngrok-skip-browser-warning");
// Allow preflight requests (for methods like OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");
    http_response_code(200);
    exit;
}
// Include Composer's autoloader
require '../../vendor/autoload.php';

// Database connection setup using MysqliDb
$db = new MysqliDb('localhost', 'root', '', 'bookon');

try {
    // Count all services in the services table
    $serviceCount = $db->getValue("services", "count(*)");

    echo json_encode(['success' => true, 'service_count' => $serviceCount]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database query failed: ' . $e->getMessage()]);
}
