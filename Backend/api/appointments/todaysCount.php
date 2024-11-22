<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization,ngrok-skip-browser-warning");

// Include Composer's autoloader
require '../../vendor/autoload.php';

// Database connection setup using MysqliDb
$db = new MysqliDb('localhost', 'root', '', 'bookon');

// Get the provider_id from the request
$provider_id = $_GET['provider_id'] ?? null;

// Check if provider_id is provided
if (!$provider_id) {
    echo json_encode(['error' => 'Provider ID is required']);
    exit;
}

try {
    // Count for today's appointments
    $db->where('provider_id', $provider_id);
    $db->where('DATE(appointment_date)', date('Y-m-d')); // Replace 'appointment_date' with the actual date field name in your table
    $todayAppointmentCount = $db->getValue("appointments", "count(*)");

    echo json_encode([
        'success' => true,
        'today_appointment_count' => $todayAppointmentCount
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database query failed: ' . $e->getMessage()]);
}