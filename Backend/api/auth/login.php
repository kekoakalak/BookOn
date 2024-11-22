<?php
// Allow CORS for development purposes
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, ngrok-skip-browser-warning");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);  // Respond with 200 OK to the preflight request
    exit;
}


// Start the session
session_start();

// Set content type for JSON responses
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

// Check the database connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Handle POST request for login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data from the request
    $inputData = json_decode(file_get_contents("php://input"), true);
    $email = $inputData['email'] ?? null;
    $password = $inputData['password'] ?? null;

    // Validate input
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Email and password are required']);
        exit;
    }

    // First check in the users table
    $stmt = $conn->prepare("SELECT user_id, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password for the user
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'user_type' => 'user' // Specify the user type as 'user'
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Incorrect password']);
        }
    } else {
        // If not found in users, check in the providers table
        $stmt = $conn->prepare("SELECT provider_id, email, password FROM providers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $provider = $result->fetch_assoc();

            // Check if the password in providers is hashed
            // Assuming providers passwords might not be hashed; adjust according to actual situation
            if (password_verify($password, $provider['password']) || $provider['password'] === $password) {
                $_SESSION['provider_id'] = $provider['provider_id'];

                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'user_id' => $provider['provider_id'],
                    'email' => $provider['email'],
                    'user_type' => 'provider' // Specify the user type as 'provider'
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Incorrect password']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'User or provider not found']);
        }
    }

    // Close statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

// Close the database connection
$conn->close();
?>
