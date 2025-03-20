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

// Handle different login methods
if (isset($data['email']) && isset($data['password'])) {
    // Email login
    $email = sanitizeInput($data['email']);
    $password = $data['password'];
    
    $result = loginWithEmail($email, $password);
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid login credentials']);
}
?>

