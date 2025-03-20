<?php
/**
 * FeaturedListings Class
 * Handles all featured listings functionality in a centralized way
 */
class FeaturedListings {
    private $db;
    
    /**
     * Constructor
     * 
     * @param Database $db Database connection
     */
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    /**
     * Get featured providers
     * 
     * @param int $limit Maximum number of providers to return
     * @param int|null $categoryId Optional category filter
     * @return array Array of featured providers
     */
    public function getFeaturedProviders($limit = 6, $categoryId = null) {
        $currentDate = date('Y-m-d');
        $params = [$currentDate];
        
        $categoryFilter = "";
        if ($categoryId) {
            $categoryFilter = "AND EXISTS (
                SELECT 1 FROM provider_services ps 
                JOIN services s ON ps.service_id = s.id 
                WHERE ps.provider_id = p.id AND s.category_id = ?
            )";
            $params[] = $categoryId;
        }
        
        $query = "SELECT p.id, p.business_name, p.city, p.state, p.is_verified, p.featured_priority,
                  (SELECT COUNT(*) FROM reviews r WHERE r.provider_id = p.id) as review_count,
                  (SELECT AVG(rating) FROM reviews r WHERE r.provider_id = p.id) as avg_rating,
                  (SELECT image_url FROM provider_images WHERE provider_id = p.id AND is_primary = 1 LIMIT 1) as image_url
                  FROM providers p 
                  WHERE p.is_featured = 1 
                  AND (p.featured_until IS NULL OR p.featured_until >= ?) 
                  $categoryFilter
                  ORDER BY p.featured_priority DESC, p.avg_rating DESC 
                  LIMIT ?";
        
        $params[] = $limit;
        
        $stmt = $this->db->prepare($query);
        
        // Bind parameters
        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindValue($i + 1, $params[$i]);
        }
        
        $stmt->execute();
        $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get services for each provider in a single query
        if (!empty($providers)) {
            $providerIds = array_column($providers, 'id');
            $placeholders = str_repeat('?,', count($providerIds) - 1) . '?';
            
            $servicesQuery = "SELECT ps.provider_id, s.name 
                             FROM provider_services ps 
                             JOIN services s ON ps.service_id = s.id 
                             WHERE ps.provider_id IN ($placeholders)
                             ORDER BY s.name ASC";
            
            $servicesStmt = $this->db->prepare($servicesQuery);
            
            // Bind provider IDs
            foreach ($providerIds as $index => $id) {
                $servicesStmt->bindValue($index + 1, $id);
            }
            
            $servicesStmt->execute();
            $allServices = $servicesStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Group services by provider
            $providerServices = [];
            foreach ($allServices as $service) {
                $providerServices[$service['provider_id']][] = $service['name'];
            }
            
            // Add services to each provider
            foreach ($providers as &$provider) {
                $provider['services'] = isset($providerServices[$provider['id']]) 
                    ? array_slice($providerServices[$provider['id']], 0, 5) 
                    : [];
                
                // Format data
                $provider['rating'] = round($provider['avg_rating'], 1) ?: 0;
                $provider['reviewCount'] = $provider['review_count'] ?: 0;
                $provider['location'] = $provider['city'] . ', ' . $provider['state'];
                $provider['verified'] = $provider['is_verified'] == 1;
                $provider['image'] = $provider['image_url'] ?: '/images/default-provider.jpg';
                $provider['featured'] = true;
                
                // Remove redundant fields
                unset($provider['avg_rating'], $provider['review_count'], $provider['city'], 
                      $provider['state'], $provider['is_verified'], $provider['image_url']);
            }
        }
        
        return $providers;
    }
    
    /**
     * Add provider to featured listings
     * 
     * @param int $providerId Provider ID
     * @param int $packageId Package ID
     * @param string|null $manualExpiry Optional manual expiry date
     * @return array Result with success status and message
     */
    public function addFeaturedProvider($providerId, $packageId, $manualExpiry = null) {
        try {
            // Get package details
            $packageQuery = "SELECT * FROM featured_packages WHERE id = ?";
            $packageStmt = $this->db->prepare($packageQuery);
            $packageStmt->bindValue(1, $packageId);
            $packageStmt->execute();
            $package = $packageStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$package) {
                return ['success' => false, 'message' => 'Invalid package selected.'];
            }
            
            // Calculate expiry date
            if ($manualExpiry) {
                $expiryDate = $manualExpiry;
            } else {
                $expiryDate = date('Y-m-d', strtotime('+' . $package['duration_days'] . ' days'));
            }
            
            // Begin transaction
            $this->db->beginTransaction();
            
            // Update provider
            $updateQuery = "UPDATE providers SET 
                            is_featured = 1, 
                            featured_until = ?, 
                            featured_priority = ? 
                            WHERE id = ?";
            
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindValue(1, $expiryDate);
            $updateStmt->bindValue(2, $package['priority']);
            $updateStmt->bindValue(3, $providerId);
            $updateStmt->execute();
            
            // Record purchase
            $purchaseQuery = "INSERT INTO featured_purchases 
                             (provider_id, package_id, expiry_date, amount, status) 
                             VALUES (?, ?, ?, ?, 'completed')";
            
            $purchaseStmt = $this->db->prepare($purchaseQuery);
            $purchaseStmt->bindValue(1, $providerId);
            $purchaseStmt->bindValue(2, $packageId);
            $purchaseStmt->bindValue(3, $expiryDate);
            $purchaseStmt->bindValue(4, $package['price']);
            $purchaseStmt->execute();
            
            // Commit transaction
            $this->db->commit();
            
            return [
                'success' => true, 
                'message' => 'Provider successfully added to featured listings until ' . date('F j, Y', strtotime($expiryDate)) . '.'
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Remove provider from featured listings
     * 
     * @param int $providerId Provider ID
     * @return array Result with success status and message
     */
    public function removeFeaturedProvider($providerId) {
        try {
            $updateQuery = "UPDATE providers SET 
                            is_featured = 0, 
                            featured_until = NULL, 
                            featured_priority = 0 
                            WHERE id = ?";
            
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindValue(1, $providerId);
            $updateStmt->execute();
            
            return ['success' => true, 'message' => 'Provider removed from featured listings.'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get featured packages
     * 
     * @param string $orderBy Field to order by
     * @return array Array of featured packages
     */
    public function getFeaturedPackages($orderBy = 'price ASC') {
        $query = "SELECT * FROM featured_packages ORDER BY $orderBy";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get purchase history for a provider
     * 
     * @param int $providerId Provider ID
     * @return array Array of purchase history
     */
    public function getPurchaseHistory($providerId) {
        $query = "SELECT fp.*, fp2.name as package_name 
                 FROM featured_purchases fp
                 JOIN featured_packages fp2 ON fp.package_id = fp2.id
                 WHERE fp.provider_id = ?
                 ORDER BY fp.purchase_date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $providerId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Update expired featured listings
     * 
     * @return array Result with count of updated providers and their details
     */
    public function updateExpiredListings() {
        $currentDate = date('Y-m-d');
        
        // Find providers whose featured status has expired
        $query = "SELECT id, business_name FROM providers 
                  WHERE is_featured = 1 
                  AND featured_until IS NOT NULL 
                  AND featured_until < ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, $currentDate);
        $stmt->execute();
        
        $expiredProviders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $expiredCount = count($expiredProviders);
        
        if ($expiredCount > 0) {
            // Update expired providers
            $updateQuery = "UPDATE providers 
                           SET is_featured = 0, 
                               featured_until = NULL, 
                               featured_priority = 0 
                           WHERE is_featured = 1 
                           AND featured_until IS NOT NULL 
                           AND featured_until < ?";
            
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindValue(1, $currentDate);
            $updateStmt->execute();
        }
        
        return [
            'count' => $expiredCount,
            'providers' => $expiredProviders
        ];
    }
}
?>

