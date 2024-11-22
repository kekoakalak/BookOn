<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

/// Allow preflight requests (for methods like OPTIONS)
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

// Process the incoming request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Receive form data
    $service_name = $_POST['name'] ?? null;
    $description = $_POST['description'] ?? null;
    $duration = $_POST['duration'] ?? null;
    $price = $_POST['price'] ?? null;
    $startTime = $_POST['startTime'] ?? null;
    $endTime = $_POST['endTime'] ?? null;
    $availability = $_POST['availability'] ?? '';
    $provider_id = $_POST['provider_id'] ?? null; // Extract provider_id from POST data

    // Handle file upload (unchanged)
    $mediaFileName = null;
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $media = $_FILES['media'];
        $mediaFileName = uniqid() . '-' . $media['name'];
        $uploadsDir = 'uploads/';

        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }

        move_uploaded_file($media['tmp_name'], $uploadsDir . $mediaFileName);
    }

    // Prepare data for insertion
    $data = [
        'service_name' => $service_name,
        'description'  => $description,
        'duration'     => $duration,
        'price'        => $price,
        'start_time'   => $startTime,
        'end_time'     => $endTime,
        'availability' => $availability,
        'media'        => $mediaFileName, // Store the file name only
        'provider_id'  => $provider_id,  // Add provider_id to the data
    ];

    // Insert data into the database using MysqliDb
    if ($db->insert('services', $data)) {
        echo json_encode(['success' => true, 'message' => 'Service added successfully']);
    } else {
        echo json_encode(['error' => 'Failed to add service: ' . $db->getLastError()]);
    }
}
?>
