<?php
header("Content-Type: application/json");
// Add CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

// Include Composer's autoloader
require '../../vendor/autoload.php';

 // Database connection setup using MysqliDb
// $db = new MysqliDb('localhost', 'waterman_webapp2', 'o8j1z4P=7ayn', 'waterman_webapp2');
$db = new MysqliDb('localhost', 'root', '', 'bookon');

// Ensure the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get the service_id from the query string (since we're passing it in the URL)
    $service_id = $_GET['service_id'] ?? null;

    if ($service_id) {
        // Fetch the service details including the media (image) filename
        $db->where('service_id', $service_id);
        $service = $db->getOne('services', 'media');  // Fetch only the media field

        if ($service) {
            if ($service['media']) {
                // Construct the image file path
                $imagePath = "uploads/" . $service['media'];
                // Debugging: Log the image path
                error_log("Image path: " . $imagePath);

                // Check if the file exists and delete it
                if (file_exists($imagePath)) {
                    if (!unlink($imagePath)) {
                        error_log("Failed to delete the image: " . $imagePath);
                        echo json_encode(['error' => 'Failed to delete associated image']);
                        exit();
                    }
                } else {
                    error_log("Image file does not exist: " . $imagePath);
                }
            }

            // Now delete the service from the database
            $db->where('service_id', $service_id);
            if ($db->delete('services')) {
                echo json_encode(['success' => true, 'message' => 'Service and associated image deleted successfully']);
            } else {
                error_log("Failed to delete service from database: " . $db->getLastError());
                echo json_encode(['error' => 'Failed to delete service']);
            }
        } else {
            echo json_encode(['error' => 'Service not found']);
        }
    } else {
        echo json_encode(['error' => 'Service ID is missing']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
