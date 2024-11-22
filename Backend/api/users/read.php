<?php
// Set headers to allow cross-origin requests
header("Access-Control-Allow-Origin: *"); // Allow all origins for development
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, ngrok-skip-browser-warning");

// Handle preflight requests (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Content-Type header for JSON response
header("Content-Type: application/json");

// Include Composer's autoloader for database connection via MysqliDb
require '../../vendor/autoload.php';

// Database connection setup using MysqliDb
// $db = new MysqliDb('localhost', 'waterman_webapp2', 'o8j1z4P=7ayn', 'waterman_webapp2');
$db = new MysqliDb('localhost', 'root', '', 'bookon');

// Process the incoming request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all data from the users table
    $users = $db->get('users');

    if ($users) {
        // Return the users data as JSON
        echo json_encode([
            'success' => true,
            'users' => $users
        ]);
    } else {
        // Return an error if no users are found or there was an issue with the query
        echo json_encode(['error' => 'No users found or database query failed']);
    }
} else {
    // Handle invalid request method
    echo json_encode(['error' => 'Invalid request method']);
}
