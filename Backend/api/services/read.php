<?php
header("Content-Type: application/json");
// Add CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
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
 

// Fetch services from the database
try {
    $services = $db->get('services', null, [
        'service_id AS id',
        'service_name AS name',
        'description',
        'duration',
        'price',
        'media'
    ]);

    // Send the result as JSON
    echo json_encode($services);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error fetching services: ' . $e->getMessage()]);
}
