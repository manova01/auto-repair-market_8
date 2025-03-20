<?php
// This script should be run daily via cron job to update featured status

// Set the correct path to your includes directory
$basePath = dirname(dirname(__FILE__));
require_once $basePath . '/includes/config.php';
require_once $basePath . '/includes/db.php';
require_once $basePath . '/includes/classes/FeaturedListings.php';

// Get database connection
$db = new Database();
$featuredListings = new FeaturedListings($db);

try {
    // Update expired featured listings
    $result = $featuredListings->updateExpiredListings();
    $expiredCount = $result['count'];
    $expiredProviders = $result['providers'];
    
    if ($expiredCount > 0) {
        // Log the results
        $logMessage = date('Y-m-d H:i:s') . " - Updated featured status: $expiredCount providers expired.\n";
        foreach ($expiredProviders as $provider) {
            $logMessage .= "ID: {$provider['id']} - {$provider['business_name']}\n";
        }
        
        // Create logs directory if it doesn't exist
        $logsDir = $basePath . '/logs';
        if (!is_dir($logsDir)) {
            mkdir($logsDir, 0755, true);
        }
        
        file_put_contents($basePath . '/logs/featured_status.log', $logMessage, FILE_APPEND);
        
        echo "Updated featured status: $expiredCount providers expired.\n";
    } else {
        echo "No expired featured providers found.\n";
    }
    
} catch (Exception $e) {
    $errorMessage = date('Y-m-d H:i:s') . " - Error updating featured status: " . $e->getMessage() . "\n";
    
    // Create logs directory if it doesn't exist
    $logsDir = $basePath . '/logs';
    if (!is_dir($logsDir)) {
        mkdir($logsDir, 0755, true);
    }
    
    file_put_contents($basePath . '/logs/featured_status.log', $errorMessage, FILE_APPEND);
    
    echo "Error: " . $e->getMessage() . "\n";
}
?>

