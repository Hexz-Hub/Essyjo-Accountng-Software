<?php
session_start();
header("Access-Control-Allow-Origin: *"); // Allow all origins (replace * with frontend URL in production)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Define allowed request methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Allow specific headers
header('Content-Type: application/json');

// Handle preflight requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (isset($_SESSION['email'])) {
    echo json_encode(['email' => $_SESSION['email']]);
} else {
    echo json_encode(['email' => null]);
}
?>
