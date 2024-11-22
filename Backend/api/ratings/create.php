<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

// Include Composer's autoloader
require '../../vendor/autoload.php';

// Database connection setup using MysqliDb
$db = new MysqliDb('localhost', 'root', '', 'bookon');


// Process the incoming request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the content type is JSON
    $input = json_decode(file_get_contents("php://input"), true);

    // Receive form data from JSON input
    $appointment_id = $input['appointment_id'] ?? null;
    $service_id = $input['service_id'] ?? null; // Add service_id
    $provider_id = $input['provider_id'] ?? null; // Add provider_id
    $user_id = $input['user_id'] ?? null;
    $feedback = $input['feedback'] ?? null;
    $star_rating = $input['star_rating'] ?? null;

    // Validate required fields (make provider_id optional)
if (!$appointment_id || !$service_id || !$user_id || !$star_rating) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}


    // Prepare data for insertion
$data = [
    'appointment_id' => $appointment_id,
    'service_id'     => $service_id, // Include service_id in the data array
    'user_id'        => $user_id,
    'feedback'       => $feedback,
    'star_rating'    => $star_rating,
    'created_at'     => date('Y-m-d H:i:s'),
    'updated_at'     => date('Y-m-d H:i:s')
];

// Only add provider_id if it's not null
if ($provider_id) {
    $data['provider_id'] = $provider_id; // Include provider_id in the data array only if it exists
}

// Insert data into the database using MysqliDb
if ($db->insert('ratings', $data)) {
    echo json_encode(['success' => true, 'message' => 'Rating added successfully']);
} else {
    echo json_encode(['error' => 'Failed to add rating: ' . $db->getLastError()]);
}

} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
