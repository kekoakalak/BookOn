<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

// Include Composer's autoloader
require '../../vendor/autoload.php';

// Database connection setup using MysqliDb
// $db = new MysqliDb('localhost', 'waterman_webapp2', 'o8j1z4P=7ayn', 'waterman_webapp2');
$db = new MysqliDb('localhost', 'root', '', 'bookon');


// Get the provider_id from the request
$provider_id = $_GET['provider_id'] ?? null;

// Check if provider_id is provided
if (!$provider_id) {
    echo json_encode(['error' => 'Provider ID is required']);
    exit;
}

try {
    $db->where('provider_id', $provider_id);
    $appointmentCount = $db->getValue("appointments", "count(*)");

    echo json_encode(['success' => true, 'appointment_count' => $appointmentCount]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database query failed: ' . $e->getMessage()]);
}
