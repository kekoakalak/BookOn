<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit();
}

require '../../vendor/autoload.php';

class API {
  private $db;

  public function __construct() {
    $this->db = new MysqliDb('localhost', 'root', '', 'bookon');
    // $this->db = new MysqliDb('localhost', 'root', '', 'waterman_webapp2');
  }

  public function httpGet() {
    $ratings = $this->db->get('ratings');
    
    if ($this->db->count > 0) {
      return ['status' => 'success', 'data' => $ratings];
    } else {
      return ['status' => 'failed', 'message' => 'No ratings found'];
    }
  }

  public function httpPost($payload) {
    if (!is_array($payload)) {
      return ['status' => 'failed', 'message' => 'Invalid input format'];
    }
  
    foreach ($payload as $entry) {
      if (empty($entry['question']) || empty($entry['type'])) {
        return ['status' => 'failed', 'message' => 'Invalid input'];
      }
  
      $data = [
        'question' => $entry['question'],
        'type' => $entry['type'],
        'required' => (int)$entry['required'], 
    ];
  
      $id = $this->db->insert('ratings', $data);
      if (!$id) {
        return ['status' => 'failed', 'message' => 'Failed to add rating', 'error' => $this->db->getLastError()];
      }
    }
  
    return ['status' => 'success', 'message' => 'Ratings added successfully'];
  }
  
}

$api = new API();
$request_method = $_SERVER['REQUEST_METHOD'];

switch($request_method) {
  case 'GET':
    $response = $api->httpGet();
    break;

  case 'POST':
    $received_data = json_decode(file_get_contents('php://input'), true);
    $response = $api->httpPost($received_data);
    break;

  default:
    http_response_code(405);
    $response = ['status' => 'failed', 'message' => 'Method not allowed'];
    break;
}

echo json_encode($response);
?>
