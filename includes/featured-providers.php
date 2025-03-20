<?php
// Initialize featured providers
$featuredProviders = [];

try {
    // Create FeaturedListings instance if not already created
    if (!isset($featuredListings)) {
        require_once 'classes/FeaturedListings.php';
        $featuredListings = new FeaturedListings($db);
    }
    
    // Get featured providers
    $featuredProviders = $featuredListings->getFeaturedProviders(6);
    
} catch (Exception $e) {
    // Log error
    error_log('Error fetching featured providers: ' . $e->getMessage());
}

// Only output the section if we have providers or if $showEmpty is true
$showEmpty = isset($showEmpty) ? $showEmpty : false;
if (!empty($featuredProviders) || $showEmpty):
?>

<!-- Featured Providers Section -->
<section class="featured-providers py-16">
    <div class="container">
        <div class="section-header text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">Featured Service Providers</h2>
            <p class="text-gray-600">Top-rated auto repair professionals ready to help you</p>
        </div>
        
        <?php if (empty($featuredProviders)): ?>
            <div class="text-center py-8">
                <p>No featured providers available at this time.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($featuredProviders as $provider): ?>
                    <div class="provider-card featured">
                        <div class="featured-badge">
                            <span>Featured</span>
                        </div>
                        <div class="provider-header">
                            <img src="<?php echo htmlspecialchars($provider['image']); ?>" alt="<?php echo htmlspecialchars($provider['name']); ?>" class="provider-image">
                            <div class="provider-info">
                                <h3 class="provider-name">
                                    <?php echo htmlspecialchars($provider['name']); ?>
                                    <?php if ($provider['verified']): ?>
                                        <span class="verified-badge" title="Verified Provider"><i class="fas fa-check-circle"></i></span>
                                    <?php endif; ?>
                                </h3>
                                <div class="provider-rating">
                                    <div class="stars">
                                        <?php 
                                        // Render stars more efficiently
                                        $fullStars = floor($provider['rating']);
                                        $halfStar = $provider['rating'] - $fullStars >= 0.5;
                                        
                                        for ($i = 1; $i <= 5; $i++): 
                                            if ($i <= $fullStars): ?>
                                                <i class="fas fa-star"></i>
                                            <?php elseif ($halfStar && $i == $fullStars + 1): ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif;
                                        endfor; ?>
                                    </div>
                                    <span class="rating-text"><?php echo $provider['rating']; ?> (<?php echo $provider['reviewCount']; ?> reviews)</span>
                                </div>
                                <div class="provider-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($provider['location']); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="provider-services">
                            <h4>Services:</h4>
                            <div class="service-tags">
                                <?php foreach ($provider['services'] as $service): ?>
                                    <span class="service-tag"><?php echo htmlspecialchars($service); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="provider-actions">
                            <a href="provider-profile.php?id=<?php echo $provider['id']; ?>" class="btn btn-outline">View Profile</a>
                            <a href="booking.php?provider=<?php echo $provider['id']; ?>" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-8">
                <a href="listings.php?featured=1" class="btn btn-outline-primary">View All Featured Providers</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

