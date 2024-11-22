<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

// Include Composer's autoloader
require '../../vendor/autoload.php';

// Database connection setup using MysqliDb
// $db = new MysqliDb('localhost', 'waterman_webapp2', 'o8j1z4P=7ayn', 'waterman_webapp2');
$db = new MysqliDb('localhost', 'root', '', 'bookon');


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $service_id = $_GET['service_id'] ?? null;

    // Check if service_id is provided
    if (!$service_id) {
        echo json_encode(['error' => 'Missing required parameter: service_id']);
        exit;
    }

    // Fetch reviews for the specified service_id with user details
    try {
        $db->where('service_id', $service_id);
        $db->join("users u", "u.user_id = ratings.user_id", "LEFT");
        
        $reviews = $db->get('ratings', null, [
            'ratings.rating_id',
            'ratings.appointment_id',
            'ratings.user_id',
            'u.first_name',
            'u.last_name',
            'ratings.feedback',
            'ratings.star_rating',
            'ratings.created_at'
        ]);

        echo json_encode($reviews);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error fetching reviews: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
