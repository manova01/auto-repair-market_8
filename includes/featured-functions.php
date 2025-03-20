<?php
/**
 * Featured Listings Utility Functions
 */

/**
 * Check if a provider is featured
 * 
 * @param int $providerId Provider ID
 * @return bool True if provider is featured, false otherwise
 */
function isProviderFeatured($providerId) {
    global $db;
    
    $query = "SELECT is_featured, featured_until FROM providers WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $providerId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        return false;
    }
    
    // Check if featured and not expired
    if ($result['is_featured'] == 1) {
        if ($result['featured_until'] === null) {
            return true; // No expiry date, always featured
        }
        
        // Check if not expired
        return strtotime($result['featured_until']) >= strtotime(date('Y-m-d'));
    }
    
    return false;
}

/**
 * Get days remaining for featured status
 * 
 * @param int $providerId Provider ID
 * @return int|null Number of days remaining or null if not featured or no expiry
 */
function getFeaturedDaysRemaining($providerId) {
    global $db;
    
    $query = "SELECT featured_until FROM providers WHERE id = ? AND is_featured = 1";
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $providerId);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result || $result['featured_until'] === null) {
        return null;
    }
    
    $daysLeft = ceil((strtotime($result['featured_until']) - time()) / 86400);
    return $daysLeft > 0 ? $daysLeft : 0;
}

/**
 * Get featured package by ID
 * 
 * @param int $packageId Package ID
 * @return array|null Package details or null if not found
 */
function getFeaturedPackage($packageId) {
    global $db;
    
    $query = "SELECT * FROM featured_packages WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $packageId);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

/**
 * Format featured expiry date with days remaining
 * 
 * @param string $expiryDate Expiry date in Y-m-d format
 * @return string Formatted date with days remaining
 */
function formatFeaturedExpiry($expiryDate) {
    if (!$expiryDate) {
        return 'Unlimited';
    }
    
    $formattedDate = date('M j, Y', strtotime($expiryDate));
    $daysLeft = ceil((strtotime($expiryDate) - time()) / 86400);
    
    $daysText = $daysLeft > 0 
        ? "($daysLeft days left)" 
        : "(Expired)";
    
    $colorClass = $daysLeft <= 3 ? 'text-danger' : 'text-muted';
    
    return "$formattedDate <span class='$colorClass'>$daysText</span>";
}
?>

