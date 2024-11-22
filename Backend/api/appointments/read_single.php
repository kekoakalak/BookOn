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


// Check if the appointment_id is provided in the query
if (isset($_GET['appointment_id'])) {
    $appointment_id = $_GET['appointment_id'];

    // Fetch appointment details from the appointments table
    $db->where('appointment_id', $appointment_id);
    $appointment = $db->getOne('appointments', [
        'appointment_id',
        'code',
        'appointment_date',
        'service_id',
        'provider_id',
        'user_id',
        'status'
    ]);

    if ($appointment) {
        // Fetch additional details from appointment_details table based on appointment_id
        $db->where('appointment_id', $appointment_id);
        $appointment_details = $db->getOne('appointment_details', [
            'first_name',
            'last_name',
            'mobile',
            'notes',
            'payment_method'

        ]);

                    // Fetch service details from services table using service_id from the appointment
            if ($appointment['service_id']) {
                $db->where('service_id', $appointment['service_id']);
                $service_details = $db->getOne('services', [
                    'service_name',
                    'price',
                    'duration'
                ]);
            } else {
                $service_details = [];
            }


        // Combine the appointment, appointment details, and service details
        $response = [
            'appointment' => $appointment,
            'details' => $appointment_details ? $appointment_details : [],
            'service' => $service_details ? $service_details : []
        ];

        // Return the combined details as JSON
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Appointment not found']);
    }
} else {
    echo json_encode(['error' => 'Missing appointment_id']);
}
?>
