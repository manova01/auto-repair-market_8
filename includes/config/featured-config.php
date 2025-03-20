<?php
/**
 * Featured Listings Configuration
 */

// Default featured packages
$DEFAULT_FEATURED_PACKAGES = [
    [
        'name' => 'Basic',
        'description' => 'Get your business featured for 7 days',
        'duration_days' => 7,
        'price' => 29.99,
        'priority' => 1
    ],
    [
        'name' => 'Standard',
        'description' => 'Get your business featured for 30 days',
        'duration_days' => 30,
        'price' => 99.99,
        'priority' => 2
    ],
    [
        'name' => 'Premium',
        'description' => 'Get your business featured for 90 days',
        'duration_days' => 90,
        'price' => 249.99,
        'priority' => 3
    ]
];

// Featured settings
$FEATURED_SETTINGS = [
    'max_featured_homepage' => 6,
    'max_featured_category' => 12,
    'default_priority' => 1,
    'notification_days_before_expiry' => 3,
    'enable_auto_renewal' => false,
    'enable_featured_analytics' => true
];

// Featured display options
$FEATURED_DISPLAY = [
    'show_badge' => true,
    'highlight_background' => true,
    'show_on_homepage' => true,
    'show_on_search' => true,
    'prioritize_in_results' => true
];

/**
 * Initialize featured packages in database if they don't exist
 */
function initFeaturedPackages() {
    global $db, $DEFAULT_FEATURED_PACKAGES;
    
    // Check if packages exist
    $query = "SELECT COUNT(*) as count FROM featured_packages";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If no packages exist, create default ones
    if ($result['count'] == 0) {
        foreach ($DEFAULT_FEATURED_PACKAGES as $package) {
            $query = "INSERT INTO featured_packages 
                     (name, description, duration_days, price, priority) 
                     VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($query);
            $stmt->bindValue(1, $package['name']);
            $stmt->bindValue(2, $package['description']);
            $stmt->bindValue(3, $package['duration_days']);
            $stmt->bindValue(4, $package['price']);
            $stmt->bindValue(5, $package['priority']);
            $stmt->execute();
        }
    }
}
?>

