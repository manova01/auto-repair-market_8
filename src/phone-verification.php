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

// Validate phone number
if (!isset($data['phone']) || empty($data['phone'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phone number is required']);
    exit;
}

// Sanitize and validate phone number
$phone = sanitizeInput($data['phone']);

// Basic phone number validation (you may want to use a library for better validation)
if (!preg_match('/^\+?[1-9]\d{1,14}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
    exit;
}

// Generate and send verification code
$result = generateVerificationCode($phone);

// Return the result
echo json_encode($result);
?>

