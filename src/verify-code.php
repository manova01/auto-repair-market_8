<?php
require_once '../includes/config.php';
require_once '../includes/auth_functions.php';

header('Content-Type: application/json');

// Protect against CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get the request data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['phone']) || empty($data['phone']) || !isset($data['code']) || empty($data['code'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phone number and verification code are required']);
    exit;
}

// Sanitize input
$phone = sanitizeInput($data['phone']);
$code = sanitizeInput($data['code']);

// Verify the code
$result = verifyPhoneCode($phone, $code);

// Return the result
echo json_encode($result);
?>

