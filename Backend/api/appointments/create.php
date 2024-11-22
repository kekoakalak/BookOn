<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require '../../vendor/autoload.php';

$db = new MysqliDb('localhost', 'root', '', 'bookon');

// Function to generate random alphanumeric code
function generateAppointmentCode($db) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $codeLength = 6;
    $code = '';

    for ($i = 0; $i < $codeLength; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    $db->where('code', $code);
    $exists = $db->getOne('appointments');

    if ($exists) {
        return generateAppointmentCode($db);
    }

    return $code;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the incoming JSON data
    $inputData = json_decode(file_get_contents('php://input'), true);

    if ($inputData) {
        $service_id = $inputData['service_id'];
        $dateTime = $inputData['appointment_date']; 
        $first_name = $inputData['first_name'];
        $last_name = $inputData['last_name'];
        $mobile = $inputData['mobile'];
        $notes = $inputData['notes'];
        $payment_method = $inputData['payment_method'];
        $user_id = $inputData['user_id'];

        // Retrieve the provider_id and duration from the services table
        $serviceQuery = $db->rawQuery("SELECT provider_id, duration FROM services WHERE service_id = ?", [$service_id]);
        
        if (empty($serviceQuery)) {
            echo json_encode(['error' => 'Invalid service_id']);
            http_response_code(400);
            exit;
        }

        $provider_id = $serviceQuery[0]['provider_id'];
        $duration = isset($serviceQuery[0]['duration']) ? $serviceQuery[0]['duration'] : '60';

        // Use the custom function to generate a unique appointment code
        $appointmentCode = generateAppointmentCode($db);

        // Insert appointment data
        $appointmentData = [
            'service_id' => $service_id,
            'provider_id' => $provider_id,
            'appointment_date' => $dateTime,
            'status' => 'Pending',
            'user_id' => $user_id,
            'code' => $appointmentCode,
        ];

        $appointmentId = $db->insert('appointments', $appointmentData);

        if (!$appointmentId) {
            echo json_encode(['error' => 'Failed to create appointment']);
            http_response_code(500);
            exit;
        }

        // Insert appointment details
        $appointmentDetailsData = [
            'appointment_id' => $appointmentId,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'mobile' => $mobile,
            'notes' => $notes,
            'payment_method' => $payment_method,
            'appointment_date' => $dateTime,
            'duration' => $duration,
        ];

        $db->insert('appointment_details', $appointmentDetailsData);

        echo json_encode(['message' => 'Appointment created successfully']);
    } else {
        echo json_encode(['error' => 'Invalid JSON data']);
        http_response_code(400);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
    http_response_code(405);
}
?>
