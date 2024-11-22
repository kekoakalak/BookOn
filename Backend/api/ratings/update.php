<?php
header("Content-Type: application/json");
// Add CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

// Include Composer's autoloader
require '../../vendor/autoload.php';

 // Database connection setup using MysqliDb
// $db = new MysqliDb('localhost', 'waterman_webapp2', 'o8j1z4P=7ayn', 'waterman_webapp2');
$db = new MysqliDb('localhost', 'root', '', 'bookon');

 

// Check if the service_id is provided in the query
if (isset($_GET['service_id'])) {
    $service_id = $_GET['service_id'];

    // Fetch the current service details
    $db->where('service_id', $service_id);
    $currentService = $db->getOne('services');

    // Check if the service exists
    if (!$currentService) {
        echo json_encode(['error' => 'Service not found']);
        exit();
    }

    // Create an array to hold the fields to update
    $fieldsToUpdate = [];

    // Update only fields that are provided, otherwise retain current values
    $fieldsToUpdate['service_name'] = $_POST['name'] ?? $currentService['service_name'];
    $fieldsToUpdate['description'] = $_POST['description'] ?? $currentService['description'];
    $fieldsToUpdate['duration'] = $_POST['duration'] ?? $currentService['duration'];
    $fieldsToUpdate['price'] = $_POST['price'] ?? $currentService['price'];
    $fieldsToUpdate['start_time'] = $_POST['startTime'] ?? $currentService['start_time'];
    $fieldsToUpdate['end_time'] = $_POST['endTime'] ?? $currentService['end_time'];
    $fieldsToUpdate['availability'] = $_POST['availability'] ?? $currentService['availability'];

    // Handle file upload only if a new file is uploaded
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        // Move the uploaded file to the desired location
        $targetDir = "uploads/";
        $mediaFileName = uniqid() . '-' . basename($_FILES["media"]["name"]);
        $targetFile = $targetDir . $mediaFileName;

        if (move_uploaded_file($_FILES["media"]["tmp_name"], $targetFile)) {
            $fieldsToUpdate['media'] = $mediaFileName;
        } else {
            echo json_encode(['error' => 'File upload failed']);
            exit();
        }
    } else {
        // Retain the current media if no new file is uploaded
        $fieldsToUpdate['media'] = $currentService['media'];
    }

    // Update the service record in the database
    $db->where('service_id', $service_id);
    if ($db->update('services', $fieldsToUpdate)) {
        echo json_encode(['success' => true, 'message' => 'Service updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update service: ' . $db->getLastError()]);
    }
} else {
    echo json_encode(['error' => 'Missing service_id']);
}
