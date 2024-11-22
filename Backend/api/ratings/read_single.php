<?php
header("Content-Type: application/json");
// Add CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

// Include Composer's autoloader
require '../../vendor/autoload.php';

 // Database connection setup using MysqliDb
// $db = new MysqliDb('localhost', 'waterman_webapp2', 'o8j1z4P=7ayn', 'waterman_webapp2');
$db = new MysqliDb('localhost', 'root', '', 'bookon');

 

// Check if the service_id is provided in the query
if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];

    // Fetch service details by ID using MysqliDb
    $db->where('service_id', $service_id);
    $service = $db->getOne('services', [
        'service_id AS id',
        'service_name AS name',
        'description',
        'duration',
        'price',
        'availability',
        'media',
        'start_time',
        'end_time'
    ]);

    if ($service) {
        // Return service details as JSON
        echo json_encode($service);
    } else {
        echo json_encode(['error' => 'Service not found']);
    }
} else {
    echo json_encode(['error' => 'Missing service_id']);
}
