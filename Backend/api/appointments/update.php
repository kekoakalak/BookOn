<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

// Include Composer's autoloader
require '../../vendor/autoload.php';

// Database connection setup using MysqliDb
// $db = new MysqliDb('localhost', 'waterman_webapp2', 'o8j1z4P=7ayn', 'waterman_webapp2');
$db = new MysqliDb('localhost', 'root', '', 'bookon');

// Get the JSON input
$inputData = json_decode(file_get_contents("php://input"), true);
$appointment_id = $inputData['appointment_id'] ?? null;
$status = $inputData['status'] ?? null;

// Validate input
if (!$appointment_id || !$status) {
    echo json_encode(['success' => false, 'error' => 'Appointment ID and status are required.']);
    exit;
}

try {
    // Update the appointment status in the database
    $db->where('appointment_id', $appointment_id);
    if ($db->update('appointments', ['status' => $status])) {
        echo json_encode(['success' => true, 'message' => 'Appointment status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update appointment status.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database query failed: ' . $e->getMessage()]);
}
