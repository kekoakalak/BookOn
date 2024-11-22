<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

// Include Composer's autoloader
require '../../vendor/autoload.php';

// Database connection setup using MysqliDb
$db = new MysqliDb('localhost', 'root', '', 'bookon');

// Get input parameters
$user_id = $_GET['user_id'] ?? null;
$provider_id = $_GET['provider_id'] ?? null;
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;

// Validate input
if (!$user_id && !$provider_id) {
    echo json_encode(['error' => 'User or Provider ID is required.']);
    exit;
}

try {
    // Set conditions based on input
    if ($user_id) {
        $db->where('a.user_id', $user_id);
    } elseif ($provider_id) {
        $db->where('a.provider_id', $provider_id);
    }

    // Apply date range filter if provided
    if ($start_date && $end_date) {
        $db->where('a.appointment_date', [$start_date, $end_date], 'BETWEEN');
    }

    // Fetch appointments data
    $appointments = $db->get("appointments a", null, [
        'a.appointment_id',
        'a.code',
        'a.appointment_date',
        'a.status',
        'a.service_id',
        'a.provider_id',
        'a.user_id'
    ]);

    if ($appointments) {
        $response = [];

        // Loop through each appointment to fetch additional details
        foreach ($appointments as $appointment) {
            $appointment_id = $appointment['appointment_id'];
            $service_id = $appointment['service_id'];
            $provider_id = $appointment['provider_id'];

            // Fetch service details (handle NULL service_id)
            $service_details = [];
            if ($service_id) {
                $db->where('service_id', $service_id);
                $service_details = $db->getOne('services', [
                    'service_name',
                    'description',
                    'duration',
                    'price',
                    'start_time',
                    'end_time',
                    'availability',
                    'media'
                ]);
            }

            // Fetch appointment details from appointment_details table
            $db->where('appointment_id', $appointment_id);
            $appointment_details = $db->getOne('appointment_details', [
                'first_name',
                'last_name',
                'mobile',
                'notes',
                'payment_method'
            ]);

            // Fetch provider details
            $db->where('provider_id', $provider_id);
            $provider_details = $db->getOne('providers', [
                'first_name AS provider_first_name',
                'last_name AS provider_last_name'
            ]);

            // Fetch appointment from appointments table (new part - merged)
            $db->where('appointment_id', $appointment_id);
            $appointment_data = $db->getOne('appointments', [
                'appointment_date',
                'status',
            ]);

            // Combine the data (merged update)
            $response[] = [
                'appointment' => [
                    'appointment_id' => $appointment['appointment_id'],
                    'code' => $appointment['code'],
                    'appointment_date' => $appointment_data ? $appointment_data['appointment_date'] : [], // From the new fetch
                    'status' => $appointment_data ? $appointment_data['status'] : [], // From the new fetch
                    'service_id' => $service_id,
                    'provider_id' => $provider_id,
                    'user_id' => $appointment['user_id']
                ],
                'details' => $appointment_details ?: [],
                'service' => $service_details ?: [],
                'provider' => $provider_details ?: [],
                'date' => $appointment_data ? $appointment_data : [] // Added the date field here
            ];
        }

        // Return the structured response
        echo json_encode(['success' => true, 'appointments' => $response]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No appointments found.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database query failed: ' . $e->getMessage()]);
}
