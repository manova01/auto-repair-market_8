<?php
require_once 'db.php';

// User authentication functions
function registerUser($firstName, $lastName, $email, $password, $phone = null, $userType = 'customer') {
    global $db;
    
    // Check if email already exists
    $existingUser = $db->selectOne("SELECT id FROM users WHERE email = ?", [$email]);
    if ($existingUser) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $userId = $db->insert('users', [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password' => $hashedPassword,
        'phone' => $phone,
        'user_type' => $userType
    ]);
    
    if ($userId) {
        return ['success' => true, 'user_id' => $userId];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

function loginUser($email, $password) {
    global $db;
    
    $user = $db->selectOne("SELECT * FROM users WHERE email = ?", [$email]);
    
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        
        return ['success' => true, 'user' => $user];
    } else {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function isProvider() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'provider';
}

function logout() {
    session_unset();
    session_destroy();
    
    // Redirect to home page
    header('Location: ' . SITE_URL);
    exit;
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $db;
    return $db->selectOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

function getCurrentProvider() {
    if (!isProvider()) {
        return null;
    }
    
    global $db;
    return $db->selectOne("SELECT * FROM providers WHERE user_id = ?", [$_SESSION['user_id']]);
}

// Provider functions
function getProviders($limit = 10, $offset = 0, $filters = []) {
    global $db;
    
    $sql = "SELECT p.*, u.email, u.phone 
            FROM providers p 
            JOIN users u ON p.user_id = u.id";
    
    $whereConditions = [];
    $params = [];
    
    // Apply filters
    if (!empty($filters['service_category'])) {
        $sql .= " JOIN services s ON p.id = s.provider_id";
        $whereConditions[] = "s.category_id = ?";
        $params[] = $filters['service_category'];
    }
    
    if (!empty($filters['city'])) {
        $whereConditions[] = "p.city = ?";
        $params[] = $filters['city'];
    }
    
    if (!empty($filters['min_rating'])) {
        $whereConditions[] = "p.avg_rating >= ?";
        $params[] = $filters['min_rating'];
    }
    
    if (!empty($whereConditions)) {
        $sql .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    // Add sorting
    if (!empty($filters['sort_by'])) {
        switch ($filters['sort_by']) {
            case 'rating':
                $sql .= " ORDER BY p.avg_rating DESC";
                break;
            case 'reviews':
                $sql .= " ORDER BY p.review_count DESC";
                break;
            default:
                $sql .= " ORDER BY p.business_name ASC";
        }
    } else {
        $sql .= " ORDER BY p.business_name ASC";
    }
    
    // Add pagination
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return $db->select($sql, $params);
}

function getProviderById($id) {
    global $db;
    
    $provider = $db->selectOne("
        SELECT p.*, u.email, u.phone 
        FROM providers p 
        JOIN users u ON p.user_id = u.id 
        WHERE p.id = ?
    ", [$id]);
    
    if ($provider) {
        // Get provider images
        $provider['images'] = $db->select("
            SELECT * FROM provider_images WHERE provider_id = ?
        ", [$id]);
        
        // Get provider services
        $provider['services'] = $db->select("
            SELECT s.*, c.name as category_name 
            FROM services s 
            JOIN service_categories c ON s.category_id = c.id 
            WHERE s.provider_id = ?
        ", [$id]);
        
        // Get business hours
        $provider['hours'] = $db->select("
            SELECT * FROM business_hours WHERE provider_id = ? ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
        ", [$id]);
        
        // Get reviews
        $provider['reviews'] = $db->select("
            SELECT r.*, u.first_name, u.last_name 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.provider_id = ? 
            ORDER BY r.created_at DESC
        ", [$id]);
    }
    
    return $provider;
}

function createProvider($userId, $data) {
    global $db;
    
    // Insert provider
    $providerId = $db->insert('providers', [
        'user_id' => $userId,
        'business_name' => $data['business_name'],
        'description' => $data['description'],
        'address' => $data['address'],
        'city' => $data['city'],
        'state' => $data['state'],
        'zip_code' => $data['zip_code'],
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'website' => $data['website'] ?? null
    ]);
    
    if ($providerId) {
        // Update user type
        $db->update('users', ['user_type' => 'provider'], 'id = ?', [$userId]);
        
        // Insert business hours
        foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
            $db->insert('business_hours', [
                'provider_id' => $providerId,
                'day_of_week' => $day,
                'open_time' => $day != 'Sunday' ? '09:00:00' : null,
                'close_time' => $day != 'Sunday' ? '17:00:00' : null,
                'is_closed' => $day == 'Sunday'
            ]);
        }
        
        return ['success' => true, 'provider_id' => $providerId];
    } else {
        return ['success' => false, 'message' => 'Failed to create provider'];
    }
}

// Review functions
function addReview($providerId, $userId, $rating, $comment) {
    global $db;
    
    // Insert review
    $reviewId = $db->insert('reviews', [
        'provider_id' => $providerId,
        'user_id' => $userId,
        'rating' => $rating,
        'comment' => $comment
    ]);
    
    if ($reviewId) {
        // Update provider rating and review count
        $reviews = $db->select("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE provider_id = ?", [$providerId]);
        
        if ($reviews && isset($reviews[0])) {
            $db->update('providers', [
                'avg_rating' => $reviews[0]['avg_rating'],
                'review_count' => $reviews[0]['count']
            ], 'id = ?', [$providerId]);
        }
        
        return ['success' => true, 'review_id' => $reviewId];
    } else {
        return ['success' => false, 'message' => 'Failed to add review'];
    }
}

// Utility functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function uploadImage($file, $directory = 'providers') {
    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed with error code: ' . $file['error']];
    }
    
    // Check file size (limit to 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File is too large. Maximum size is 5MB.'];
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'];
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = UPLOAD_DIR . $directory . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $filename = generateRandomString() . '_' . time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [
            'success' => true,
            'path' => $directory . '/' . $filename,
            'url' => UPLOAD_URL . $directory . '/' . $filename
        ];
    } else {
        return ['success' => false, 'message' => 'Failed to move uploaded file.'];
    }
}

function formatDate($date, $format = 'M j, Y') {
    return date($format, strtotime($date));
}

function formatTime($time, $format = 'g:i A') {
    return date($format, strtotime($time));
}

function getTimeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } else {
        return formatDate($datetime);
    }
}
?>

