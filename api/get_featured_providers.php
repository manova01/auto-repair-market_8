<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/classes/FeaturedListings.php';

header('Content-Type: application/json');

try {
    // Get database connection
    $db = new Database();
    
    // Create FeaturedListings instance
    $featuredListings = new FeaturedListings($db);
    
    // Get limit parameter (default to 6)
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 6;
    
    // Get category filter if provided
    $categoryId = isset($_GET['category']) && !empty($_GET['category']) ? intval($_GET['category']) : null;
    
    // Get featured providers
    $providers = $featuredListings->getFeaturedProviders($limit, $categoryId);
    
    echo json_encode(['success' => true, 'providers' => $providers]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

