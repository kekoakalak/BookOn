<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

// Include Composer's autoloader
require '../../vendor/autoload.php';

// Database connection setup using MysqliDb
// $db = new MysqliDb('localhost', 'waterman_webapp2', 'o8j1z4P=7ayn', 'waterman_webapp2');
$db = new MysqliDb('localhost', 'root', '', 'bookon');

try {
    // Fetch all reviews from the ratings table
    $reviews = $db->get("ratings", null, [
        'rating_id',
        'appointment_id',
        'service_id',
        'user_id',
        'provider_id',
        'feedback',
        'star_rating',
        'created_at'
    ]);

    if ($reviews) {
        $response = [];

        // Loop through each review to fetch related details
        foreach ($reviews as $review) {
            $user_id = $review['user_id'];
            $provider_id = $review['provider_id'];
            $service_id = $review['service_id'];

            // Fetch user details
            $db->where('user_id', $user_id);
            $user_details = $db->getOne('users', [
                'first_name AS user_first_name',
                'last_name AS user_last_name'
            ]);

            // Fetch provider details if available
            if ($provider_id) {
                $db->where('provider_id', $provider_id);
                $provider_details = $db->getOne('providers', [
                    'first_name AS provider_first_name',
                    'last_name AS provider_last_name'
                ]);
            } else {
                $provider_details = null;
            }

            // Fetch service details if available
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
            } else {
                $service_details = null;
            }

            // Combine the data
            $response[] = [
                'review' => $review,
                'user' => $user_details ? $user_details : [],
                'provider' => $provider_details ? $provider_details : [],
                'service' => $service_details ? $service_details : []
            ];
        }

        // Return the structured response
        echo json_encode(['success' => true, 'reviews' => $response]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No reviews found.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database query failed: ' . $e->getMessage()]);
}
