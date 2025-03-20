<?php
require_once '../includes/config.php';
require_once '../includes/auth_functions.php';

// Check if there's an error
if (isset($_GET['error'])) {
    // Redirect to login page with error
    header('Location: ' . SITE_URL . '/login.html?error=' . urlencode($_GET['error']));
    exit;
}

// Check if the authorization code is present
if (!isset($_GET['code'])) {
    // Redirect to login page with error
    header('Location: ' . SITE_URL . '/login.html?error=missing_code');
    exit;
}

// Get the authorization code
$code = $_GET['code'];

// Process the Facebook login
$result = handleFacebookLogin($code);

if ($result['success']) {
    // Set a cookie with user info (optional, for client-side access)
    setcookie('user_data', json_encode([
        'id' => $result['user']['id'],
        'name' => $result['user']['name'],
        'email' => $result['user']['email'] ?? '',
        'type' => $result['user']['type']
    ]), time() + 3600, '/', '', true, true);
    
    // Redirect to dashboard
    header('Location: ' . SITE_URL . '/dashboard.html');
} else {
    // Redirect to login page with error
    header('Location: ' . SITE_URL . '/login.html?error=' . urlencode($result['message']));
}
?>

