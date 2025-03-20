<?php
$pageTitle = "Home";
require_once 'includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h1>Find Trusted Auto Repair Services</h1>
        <p>Connect with top-rated mechanics and auto repair shops in your area</p>
        
        <form action="listings.php" method="get" class="search-form">
            <input type="text" name="location" class="form-control" placeholder="Enter your location" required>
            <select name="service" class="form-control">
                <option value="">All Services</option>
                <?php
                // Get service categories from database
                $serviceCategories = $db->select("SELECT * FROM service_categories ORDER BY name");
                foreach ($serviceCategories as $category) {
                    echo '<option value="' . $category['id'] . '">' . $category['name'] . '</option>';
                }
                ?>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="text-center">Why Choose Us</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h3>Qualified Professionals</h3>
                <p>All service providers are vetted and qualified to ensure quality repairs.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3>Verified Reviews</h3>
                <p>Read honest reviews from real customers to make informed decisions.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Easy Booking</h3>
                <p>Schedule appointments online with just a few clicks.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Service Guarantee</h3>
                <p>Our providers offer guarantees on their work for your peace of mind.</p>
            </div>
        </div>
    </div>
</section>

<section class="popular-services">
    <div class="container">
        <h2 class="text-center">Popular Services</h2>
        
        <div class="services-grid">
            <?php
            // Get service categories with icons
            $serviceIcons = [
                'Engine Repair' => 'engine',
                'Brake Service' => 'brake-warning',
                'Oil Change' => 'oil-can',
                'Tire Service' => 'tire',
                'Wheel Alignment' => 'cog',
                'AC Service' => 'snowflake',
                'Body Work' => 'car',
                'Auto Electrical' => 'bolt'
            ];
            
            foreach ($serviceCategories as $category) {
                $icon = isset($serviceIcons[$category['name']]) ? $serviceIcons[$category['name']] : 'wrench';
                ?>
                <a href="listings.php?service=<?php echo $category['id']; ?>" class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-<?php echo $icon; ?>"></i>
                    </div>
                    <h3><?php echo $category['name']; ?></h3>
                    <p><?php echo $category['description']; ?></p>
                </a>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<section class="top-providers">
    <div class="container">
        <h2 class="text-center">Top-Rated Providers</h2>
        
        <div class="providers-grid">
            <?php
            // Get top-rated providers
            $topProviders = $db->select("
                SELECT p.*, COUNT(r.id) as review_count, AVG(r.rating) as avg_rating 
                FROM providers p 
                JOIN reviews r ON p.id = r.provider_id 
                GROUP BY p.id 
                ORDER BY avg_rating DESC, review_count DESC 
                LIMIT 4
            ");
            
            foreach ($topProviders as $provider) {
                // Get provider image
                $image = $db->selectOne("
                    SELECT image_path FROM provider_images 
                    WHERE provider_id = ? AND is_profile = 1 
                    LIMIT 1
                ", [$provider['id']]);
                
                $imagePath = $image ? UPLOAD_URL . $image['image_path'] : SITE_URL . '/assets/images/placeholder.jpg';
                ?>
                <div class="provider-card">
                    <div class="provider-card-image">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo $provider['business_name']; ?>">
                    </div>
                    <div class="provider-card-content">
                        <h3><?php echo $provider['business_name']; ?></h3>
                        <div class="provider-card-rating">
                            <div class="stars">
                                <?php
                                $rating = round($provider['avg_rating'] * 2) / 2; // Round to nearest 0.5
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($i - 0.5 == $rating) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <span><?php echo number_format($rating, 1); ?> (<?php echo $provider['review_count']; ?>)</span>
                        </div>
                        <p><?php echo $provider['city'] . ', ' . $provider['state']; ?></p>
                        <a href="provider-profile.php?id=<?php echo $provider['id']; ?>" class="btn btn-outline btn-sm">View Profile</a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="listings.php" class="btn btn-primary">View All Providers</a>
        </div>
    </div>
</section>

<section class="testimonials">
    <div class="container">
        <h2 class="text-center">What Our Users Say</h2>
        
        <div class="testimonials-slider">
            <?php
            // Get recent reviews with high ratings
            $testimonials = $db->select("
                SELECT r.*, u.first_name, u.last_name, p.business_name 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                JOIN providers p ON r.provider_id = p.id 
                WHERE r.rating >= 4 
                ORDER BY r.created_at DESC 
                LIMIT 5
            ");
            
            foreach ($testimonials as $testimonial) {
                ?>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"<?php echo substr($testimonial['comment'], 0, 150); ?><?php echo strlen($testimonial['comment']) > 150 ? '...' : ''; ?>"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="testimonial-rating">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $testimonial['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                            }
                            ?>
                        </div>
                        <p><strong><?php echo $testimonial['first_name'] . ' ' . substr($testimonial['last_name'], 0, 1) . '.'; ?></strong></p>
                        <p class="testimonial-meta">on <?php echo $testimonial['business_name']; ?></p>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2>Are You an Auto Repair Professional?</h2>
            <p>Join our platform to connect with customers in your area and grow your business.</p>
            <a href="provider-signup.php" class="btn btn-primary">Join as a Provider</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>

