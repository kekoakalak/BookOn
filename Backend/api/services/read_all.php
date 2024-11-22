<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");
// Allow preflight requests (for methods like OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");
    http_response_code(200);
    exit;
}
// require_once '../../vendor/joshcam/mysqli-database-class/MysqliDb.php';
require '../../vendor/autoload.php';


$db = new MysqliDb('localhost', 'root', password: '', db: 'bookon');


$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;
$providerId = isset($_GET['providerId']) ? $_GET['providerId'] : null;

$query = "
    SELECT 
        s.service_name AS name, 
        s.duration AS duration,
        s.price AS price,
        s.media AS media,
        COUNT(a.appointment_id) AS total, 
        SUM(CASE WHEN a.status = 'Completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN a.status = 'Pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN a.status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled
    FROM bookon.services s
    LEFT JOIN bookon.appointments a ON s.service_id = a.service_id
";

$conditions = [];
if ($startDate && $endDate) {
    $conditions[] = "a.appointment_date BETWEEN ? AND ?";
}
if ($providerId) {
    $conditions[] = "a.provider_id = ?";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " GROUP BY s.service_name";

$params = [];
if ($startDate && $endDate) {
    $params[] = $startDate;
    $params[] = $endDate;
}
if ($providerId) {
    $params[] = $providerId;
}

$results = $db->rawQuery($query, $params);

$output = [];
foreach ($results as $row) {
    $output[] = [
        'name' => $row['name'],
        'duration' => $row['duration'],
        'price' => $row['price'],
        'media' => 'http://localhost/BookOn/bookon-backend/api/services/uploads/' . $row['media'], // Construct the full URL for the media
        'total' => $row['total'],
        'Completed' => $row['completed'],
        'Pending' => $row['pending'],
        'Cancelled' => $row['cancelled']
    ];
}

echo json_encode($output);
?>
