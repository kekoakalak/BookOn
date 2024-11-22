<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

// Handle preflight requests (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require '../../vendor/autoload.php';

// $db = new MysqliDb('localhost', 'waterman_webapp2', 'o8j1z4P=7ayn', 'waterman_webapp2');
$db = new MysqliDb('localhost', 'root', '', 'bookon');


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Fetch all providers
        $providers = $db->get('providers');

        // Check if providers exist
        if ($providers) {
            // Send the list of providers as a JSON response
            echo json_encode($providers);
        } else {
            echo json_encode(['error' => 'No providers found']);
        }
    } catch (Exception $e) {
        // Handle any errors that occur during the query
        echo json_encode(['error' => 'Error fetching providers: ' . $e->getMessage()]);
        http_response_code(500);
    }
} else {
    // If request method is not GET
    echo json_encode(['error' => 'Invalid request method']);
    http_response_code(405);
}
?>
