<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, ngrok-skip-browser-warning");

session_start();
header("Content-Type: application/json");

// Database connection details
// $servername = "localhost";
// $username = "waterman_webapp2";
// $password = "o8j1z4P=7ayn";
// $dbname = "waterman_webapp2";
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bookon";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get user_id from query parameter
$user_id = $_GET['user_id'] ?? null;

if ($user_id) {
    $stmt = $conn->prepare("SELECT first_name, last_name, email, mobile, created_at FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'User ID not provided']);
}

$conn->close();
?>
