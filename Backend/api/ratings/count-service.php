<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

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

$service_id = isset($_GET['service_id']) ? $_GET['service_id'] : null;

try {
    if ($service_id) {
        // Count all reviews for the given service_id in the ratings table
        $db->where("service_id", $service_id);
        $reviewCount = $db->getValue("ratings", "count(*)");
    } else {
        $reviewCount = 0;
    }

    echo json_encode(['success' => true, 'review_count' => $reviewCount]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database query failed: ' . $e->getMessage()]);
}