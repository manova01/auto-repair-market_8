<?php
require_once 'config.php';
require_once 'db.php';

// Initialize the database connection
$db = new Database();

/**
 * Sanitize user input
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

/**
 * Generate a random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Login with email and password
 */
function loginWithEmail($email, $password) {
    global $db;
    
    // Validate input
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email and password are required.'];
    }
    
    // Check if user exists
    $user = $db->selectOne("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
    
    if (!$user) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid email or password.'];
    }
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_type'] = $user['type']; // customer, provider, admin
    
    // Generate and store auth token
    $token = generateToken();
    
    // Return successful login
    return [
        'success' => true,
        'message' => 'Login successful.',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'type' => $user['type'],
            'token' => $token
        ]
    ];
}

/**
 * Login with phone
 */
function loginWithPhone($phone) {
    global $db;
    
    // Validate input
    if (empty($phone)) {
        return ['success' => false, 'message' => 'Phone number is required.'];
    }
    
    // Check if user exists
    $user = $db->selectOne("SELECT * FROM users WHERE phone = ? AND phone_verified = 1 LIMIT 1", [$phone]);
    
    if (!$user) {
        // Create a new user if doesn't exist
        return ['success' => false, 'message' => 'User not found with this phone number.'];
    }
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_phone'] = $user['phone'];
    $_SESSION['user_type'] = $user['type']; // customer, provider, admin
    
    // Generate and store auth token
    $token = generateToken();
    
    // Return successful login
    return [
        'success' => true,
        'message' => 'Login successful.',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'phone' => $user['phone'],
            'type' => $user['type'],
            'token' => $token
        ]
    ];
}

/**
 * Generate and store verification code for phone login
 */
function generateVerificationCode($phone) {
    global $db;
    
    // Validate phone number
    if (empty($phone)) {
        return ['success' => false, 'message' => 'Phone number is required.'];
    }
    
    // Generate a 6-digit code
    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Set expiration time (10 minutes from now)
    $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    
    // Delete any existing codes for this phone
    $db->delete("verification_codes", "phone = ?", [$phone]);
    
    // Store the code
    $db->insert("verification_codes", [
        'phone' => $phone,
        'code' => $code,
        'expires_at' => $expiresAt
    ]);
    
    // Send the code via SMS
    $result = sendSMS($phone, "Your Rudzz Auto verification code is: $code");
    
    if (!$result['success']) {
        return ['success' => false, 'message' => 'Failed to send verification code. Please try again.'];
    }
    
    return ['success' => true, 'message' => 'Verification code sent.'];
}

/**
 * Verify phone verification code
 */
function verifyPhoneCode($phone, $code) {
    global $db;
    
    // Validate input
    if (empty($phone) || empty($code)) {
        return ['success' => false, 'message' => 'Phone number and code are required.'];
    }
    
    // Check if code exists and is valid
    $verification = $db->selectOne(
        "SELECT * FROM verification_codes WHERE phone = ? AND code = ? AND expires_at > NOW() LIMIT 1", 
        [$phone, $code]
    );
    
    if (!$verification) {
        return ['success' => false, 'message' => 'Invalid or expired verification code.'];
    }
    
    // Check if user exists
    $user = $db->selectOne("SELECT * FROM users WHERE phone = ? LIMIT 1", [$phone]);
    
    if ($user) {
        // Update existing user
        $db->update("users", ['phone_verified' => 1], "id = ?", [$user['id']]);
    } else {
        // Create new user with phone
        $userId = $db->insert("users", [
            'name' => 'User', // Default name, can be updated later
            'phone' => $phone,
            'phone_verified' => 1,
            'auth_type' => 'phone',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $user = $db->selectOne("SELECT * FROM users WHERE id = ? LIMIT 1", [$userId]);
    }
    
    // Delete the used verification code
    $db->delete("verification_codes", "phone = ?", [$phone]);
    
    // Return user data and login
    return loginWithPhone($phone);
}

/**
 * Handle Google OAuth Login
 */
function handleGoogleLogin($code) {
    global $db;
    
    // Exchange authorization code for tokens
    $tokenData = getGoogleTokens($code);
    
    if (!$tokenData || isset($tokenData['error'])) {
        return ['success' => false, 'message' => 'Failed to authenticate with Google.'];
    }
    
    // Get user info from Google
    $userInfo = getGoogleUserInfo($tokenData['access_token']);
    
    if (!$userInfo || isset($userInfo['error'])) {
        return ['success' => false, 'message' => 'Failed to get user information from Google.'];
    }
    
    // Check if user exists by Google ID
    $user = $db->selectOne("SELECT * FROM users WHERE social_id = ? AND auth_type = 'google' LIMIT 1", [$userInfo['id']]);
    
    if (!$user) {
        // Check if user exists by email
        $user = $db->selectOne("SELECT * FROM users WHERE email = ? LIMIT 1", [$userInfo['email']]);
        
        if ($user) {
            // Update existing user with Google info
            $db->update("users", [
                'social_id' => $userInfo['id'],
                'auth_type' => 'google',
                'profile_image' => $userInfo['picture'] ?? null
            ], "id = ?", [$user['id']]);
        } else {
            // Create new user
            $userId = $db->insert("users", [
                'name' => $userInfo['name'],
                'email' => $userInfo['email'],
                'auth_type' => 'google',
                'social_id' => $userInfo['id'],
                'profile_image' => $userInfo['picture'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            $user = $db->selectOne("SELECT * FROM users WHERE id = ? LIMIT 1", [$userId]);
        }
    }
    
    // Store OAuth tokens
    storeOAuthTokens($user['id'], 'google', $tokenData);
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_type'] = $user['type'] ?? 'customer'; // Default to customer if not set
    
    // Generate and store auth token
    $token = generateToken();
    
    // Return successful login
    return [
        'success' => true,
        'message' => 'Login successful.',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'type' => $user['type'] ?? 'customer',
            'token' => $token
        ]
    ];
}

/**
 * Handle Facebook OAuth Login
 */
function handleFacebookLogin($code) {
    global $db;
    
    // Exchange authorization code for tokens
    $tokenData = getFacebookTokens($code);
    
    if (!$tokenData || isset($tokenData['error'])) {
        return ['success' => false, 'message' => 'Failed to authenticate with Facebook.'];
    }
    
    // Get user info from Facebook
    $userInfo = getFacebookUserInfo($tokenData['access_token']);
    
    if (!$userInfo || isset($userInfo['error'])) {
        return ['success' => false, 'message' => 'Failed to get user information from Facebook.'];
    }
    
    // Check if user exists by Facebook ID
    $user = $db->selectOne("SELECT * FROM users WHERE social_id = ? AND auth_type = 'facebook' LIMIT 1", [$userInfo['id']]);
    
    if (!$user) {
        // Check if user exists by email (if email is provided by Facebook)
        if (isset($userInfo['email'])) {
            $user = $db->selectOne("SELECT * FROM users WHERE email = ? LIMIT 1", [$userInfo['email']]);
        }
        
        if ($user) {
            // Update existing user with Facebook info
            $db->update("users", [
                'social_id' => $userInfo['id'],
                'auth_type' => 'facebook',
                'profile_image' => $userInfo['picture']['data']['url'] ?? null
            ], "id = ?", [$user['id']]);
        } else {
            // Create new user
            $userData = [
                'name' => $userInfo['name'],
                'auth_type' => 'facebook',
                'social_id' => $userInfo['id'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Add email if provided
            if (isset($userInfo['email'])) {
                $userData['email'] = $userInfo['email'];
            }
            
            // Add profile image if provided
            if (isset($userInfo['picture']['data']['url'])) {
                $userData['profile_image'] = $userInfo['picture']['data']['url'];
            }
            
            $userId = $db->insert("users", $userData);
            
            $user = $db->selectOne("SELECT * FROM users WHERE id = ? LIMIT 1", [$userId]);
        }
    }
    
    // Store OAuth tokens
    storeOAuthTokens($user['id'], 'facebook', $tokenData);
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'] ?? null;
    $_SESSION['user_type'] = $user['type'] ?? 'customer'; // Default to customer if not set
    
    // Generate and store auth token
    $token = generateToken();
    
    // Return successful login
    return [
        'success' => true,
        'message' => 'Login successful.',
        'user' => [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'] ?? null,
            'type' => $user['type'] ?? 'customer',
            'token' => $token
        ]
    ];
}

/**
 * Store OAuth tokens
 */
function storeOAuthTokens($userId, $provider, $tokenData) {
    global $db;
    
    // Delete existing tokens for this user and provider
    $db->delete("oauth_tokens", "user_id = ? AND provider = ?", [$userId, $provider]);
    
    // Calculate expiration time
    $expiresAt = date('Y-m-d H:i:s', time() + $tokenData['expires_in']);
    
    // Store the tokens
    $db->insert("oauth_tokens", [
        'user_id' => $userId,
        'provider' => $provider,
        'access_token' => $tokenData['access_token'],
        'refresh_token' => $tokenData['refresh_token'] ?? null,
        'expires_at' => $expiresAt
    ]);
}

/**
 * Get Google tokens using authorization code
 */
function getGoogleTokens($code) {
    // Prepare the request
    $url = 'https://oauth2.googleapis.com/token';
    $data = [
        'code' => $code,
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code'
    ];
    
    // Send the request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Get Google user info using access token
 */
function getGoogleUserInfo($accessToken) {
    // Prepare the request
    $url = 'https://www.googleapis.com/oauth2/v2/userinfo';
    
    // Send the request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Get Facebook tokens using authorization code
 */
function getFacebookTokens($code) {
    // Prepare the request
    $url = 'https://graph.facebook.com/v12.0/oauth/access_token';
    $data = [
        'code' => $code,
        'client_id' => FACEBOOK_APP_ID,
        'client_secret' => FACEBOOK_APP_SECRET,
        'redirect_uri' => FACEBOOK_REDIRECT_URI
    ];
    
    // Build the URL with query parameters
    $url = $url . '?' . http_build_query($data);
    
    // Send the request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Get Facebook user info using access token
 */
function getFacebookUserInfo($accessToken) {
    // Prepare the request
    $url = 'https://graph.facebook.com/v12.0/me?fields=id,name,email,picture.type(large)';
    $url .= '&access_token=' . urlencode($accessToken);
    
    // Send the request
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

/**
 * Send SMS using Twilio
 */
function sendSMS($to, $message) {
    // If Twilio SDK is available, use it
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php')) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
        
        $client = new Twilio\Rest\Client(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);
        
        try {
            $client->messages->create(
                $to,
                [
                    'from' => TWILIO_PHONE_NUMBER,
                    'body' => $message
                ]
            );
            
            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    } else {
        // Fallback to using cURL if Twilio SDK is not available
        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . TWILIO_ACCOUNT_SID . '/Messages.json';
        
        $data = [
            'From' => TWILIO_PHONE_NUMBER,
            'To' => $to,
            'Body' => $message
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, TWILIO_ACCOUNT_SID . ':' . TWILIO_AUTH_TOKEN);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true];
        } else {
            $responseData = json_decode($response, true);
            return ['success' => false, 'message' => $responseData['message'] ?? 'Failed to send SMS.'];
        }
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user
 */
function getCurrentUser() {
    global $db;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    return $db->selectOne("SELECT * FROM users WHERE id = ? LIMIT 1", [$_SESSION['user_id']]);
}

/**
 * Logout user
 */
function logout() {
    // Clear all session variables
    $_SESSION = [];
    
    // Destroy the session
    session_destroy();
    
    return ['success' => true, 'message' => 'Logout successful.'];
}
?>

