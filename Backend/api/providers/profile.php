<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, ngrok-skip-browser-warning");

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

// Get provider_id from query parameter
$provider_id = $_GET['provider_id'] ?? null;

if ($provider_id) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT first_name, last_name, email, mobile, created_at FROM providers WHERE provider_id = ?");
    $stmt->bind_param("i", $provider_id); // Bind provider_id as an integer
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Provider not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Provider ID not provided']);
}

// Close the connection
$conn->close();
?>
