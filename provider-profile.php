<?php
$pageTitle = "Provider Profile";
$useMapbox = true;

require_once 'includes/header.php';

// Get provider ID from URL
$providerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$providerId) {
    // Redirect to listings if no ID provided
    header('Location: ' . SITE_URL . '/listings.php');
    exit;
}

// Get provider details
$provider = getProviderById($providerId);

if (!$provider) {
    // Provider not found
    header('Location: ' . SITE_URL . '/listings.php');
    exit;
}

// Get provider profile image
$profileImage = null;
foreach ($provider['images'] as $image) {
    if ($image['is_profile']) {
        $profileImage = $image;
        break;
    }
}

// Get provider cover image
$coverImage = null;
foreach ($provider['images'] as $image) {
    if ($image['is_cover']) {
        $coverImage = $image;
        break;
    }
}

// Format business hours
$formattedHours = [];
foreach ($provider['hours'] as $hour) {
    $formattedHours[$hour['day_of_week']] = $hour;
}

// Check if user has already reviewed this provider
$userHasReviewed = false;
if (isLoggedIn()) {
    $existingReview = $db->selectOne("
        SELECT id FROM reviews 
        WHERE provider_id = ? AND user_id = ?
    ", [$providerId, $_SESSION['user_id']]);
    
    $userHasReviewed = !empty($existingReview);
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isLoggedIn()) {
        // Redirect to login page
        header('Location: ' . SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
    
    if ($userHasReviewed) {
        $error = "You have already reviewed this provider.";
    } else {
        $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
        $comment = isset($_POST['comment']) ? sanitizeInput($_POST['comment']) : '';
        
        if ($rating < 1 || $rating > 5) {
            $error = "Please select a valid rating.";
        } elseif (empty($comment)) {
            $error = "Please enter a comment.";
        } else {
            $result = addReview($providerId, $_SESSION['user_id'], $rating, $comment);
            
            if ($result['success']) {
                $success = "Your review has been submitted successfully.";
                
                // Refresh provider data to include the new review
                $provider = getProviderById($providerId);
                $userHasReviewed = true;
            } else {
                $error = $result['message'];
            }
        }
    }
}
?>

<!-- Cover Image -->
<div class="cover-image">
    <?php if ($coverImage): ?>
    <img src="<?php echo UPLOAD_URL . $coverImage['image_path']; ?>" alt="<?php echo $provider['business_name']; ?> cover">
    <?php else: ?>
    <img src="<?php echo SITE_URL; ?>/assets/images/cover-placeholder.jpg" alt="<?php echo $provider['business_name']; ?> cover">
    <?php endif; ?>
</div>

<!-- Provider Info -->
<div class="container">
    <div class="provider-info">
        <div class="provider-avatar">
            <?php if ($profileImage): ?>
            <img src="<?php echo UPLOAD_URL . $profileImage['image_path']; ?>" alt="<?php echo $provider['business_name']; ?>">
            <?php else: ?>
            <img src="<?php echo SITE_URL; ?>/assets/images/avatar-placeholder.jpg" alt="<?php echo $provider['business_name']; ?>">
            <?php endif; ?>
        </div>

        <div class="provider-details">
            <div class="provider-name">
                <h1><?php echo $provider['business_name']; ?></h1>
                <?php if ($provider['is_verified']): ?>
                <span class="verified-badge"><i class="fas fa-check-circle"></i></span>
                <?php endif; ?>
            </div>

            <div class="provider-rating">
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
                <span class="rating-score"><?php echo number_format($rating, 1); ?></span>
                <span class="review-count">(<?php echo $provider['review_count']; ?> reviews)</span>
            </div>

            <div class="provider-location">
                <i class="fas fa-map-marker-alt"></i>
                <span><?php echo $provider['address'] . ', ' . $provider['city'] . ', ' . $provider['state'] . ' ' . $provider['zip_code']; ?></span>
            </div>
        </div>

        <div class="provider-actions">
            <a href="tel:<?php echo $provider['phone']; ?>" class="btn btn-primary">
                <i class="fas fa-phone"></i> Call Now
            </a>
            <?php if (isLoggedIn()): ?>
            <a href="<?php echo SITE_URL; ?>/messages.php?provider=<?php echo $provider['id']; ?>" class="btn btn-outline">
                <i class="fas fa-comment"></i> Message
            </a>
            <a href="<?php echo SITE_URL; ?>/booking.php?provider=<?php echo $provider['id']; ?>" class="btn btn-outline">
                <i class="fas fa-calendar"></i> Book Appointment
            </a>
            <?php else: ?>
            <a href="<?php echo SITE_URL; ?>/login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-outline">
                <i class="fas fa-comment"></i> Message
            </a>
            <a href="<?php echo SITE_URL; ?>/login.php?redirect=<?php echo urlencode(SITE_URL . '/booking.php?provider=' . $provider['id']); ?>" class="btn btn-outline">
                <i class="fas fa-calendar"></i> Book Appointment
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="provider-content">
        <!-- Left Column -->
        <div class="provider-main">
            <!-- About -->
            <section class="card">
                <h2>About</h2>
                <p><?php echo nl2br($provider['description']); ?></p>
            </section>

            <!-- Services -->
            <section class="card">
                <h2>Services</h2>
                <div class="services-grid">
                    <?php foreach ($provider['services'] as $service): ?>
                    <div class="service-item">
                        <i class="fas fa-tools"></i>
                        <div>
                            <h3><?php echo $service['name']; ?></h3>
                            <p>
                                <?php
                                if ($service['price_min'] && $service['price_max']) {
                                    echo '$' . number_format($service['price_min'], 2) . ' - $' . number_format($service['price_max'], 2);
                                } elseif ($service['price_min']) {
                                    echo 'From $' . number_format($service['price_min'], 2);
                                } elseif ($service['price_max']) {
                                    echo 'Up to $' . number_format($service['price_max'], 2);
                                } else {
                                    echo 'Price upon request';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Reviews -->
            <section class="card">
                <div class="section-header">
                    <h2>Reviews</h2>
                    <?php if (isLoggedIn() && !$userHasReviewed): ?>
                    <button id="write-review-btn" class="btn btn-outline btn-sm">Write a Review</button>
                    <?php endif; ?>
                </div>

                <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (isLoggedIn() && !$userHasReviewed): ?>
                <div id="review-form-container" style="display: none;">
                    <form id="review-form" method="post" action="" data-validate>
                        <div class="form-group">
                            <label class="form-label">Your Rating</label>
                            <div class="rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment" class="form-label">Your Review</label>
                            <textarea id="comment" name="comment" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                        <button type="button" id="cancel-review" class="btn btn-outline">Cancel</button>
                    </form>
                </div>
                <?php endif; ?>

                <div class="reviews-list">
                    <?php if (empty($provider['reviews'])): ?>
                    <p>No reviews yet. Be the first to review this provider!</p>
                    <?php else: ?>
                    <?php 
                    // Show first 3 reviews
                    $displayedReviews = array_slice($provider['reviews'], 0, 3);
                    foreach ($displayedReviews as $review): 
                    ?>
                    <div class="review-item">
                        <div class="review-header">
                            <h3><?php echo $review['first_name'] . ' ' . substr($review['last_name'], 0, 1) . '.'; ?></h3>
                            <span><?php echo getTimeAgo($review['created_at']); ?></span>
                        </div>
                        <div class="review-rating">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $review['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                            }
                            ?>
                        </div>
                        <p><?php echo nl2br($review['comment']); ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <?php if (count($provider['reviews']) > 3): ?>
                <div class="view-all">
                    <a href="<?php echo SITE_URL; ?>/reviews.php?provider=<?php echo $provider['id']; ?>" class="btn btn-outline">View All Reviews</a>
                </div>
                <?php endif; ?>
            </section>
        </div>

        <!-- Right Column -->
        <div class="provider-sidebar">
            <!-- Map -->
            <section class="card">
                <h2>Location</h2>
                <div id="provider-map" class="provider-map" data-lat="<?php echo $provider['latitude']; ?>" data-lng="<?php echo $provider['longitude']; ?>"></div>
                <p class="location-address"><?php echo $provider['address'] . ', ' . $provider['city'] . ', ' . $provider['state'] . ' ' . $provider['zip_code']; ?></p>
            </section>

            <!-- Business Hours -->
            <section class="card">
                <div class="hours-header">
                    <i class="fas fa-clock"></i>
                    <h2>Business Hours</h2>
                </div>
                <div class="business-hours">
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach ($days as $day):
                        $hour = isset($formattedHours[$day]) ? $formattedHours[$day] : null;
                    ?>
                    <div class="hours-row">
                        <span><?php echo $day; ?></span>
                        <span>
                            <?php
                            if ($hour && !$hour['is_closed']) {
                                echo formatTime($hour['open_time']) . ' - ' . formatTime($hour['close_time']);
                            } else {
                                echo 'Closed';
                            }
                            ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <?php if ($provider['website']): ?>
            <!-- Website -->
            <section class="card">
                <h2>Website</h2>
                <a href="<?php echo $provider['website']; ?>" target="_blank" class="provider-website">
                    <i class="fas fa-globe"></i> <?php echo $provider['website']; ?>
                </a>
            </section>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Set Mapbox API key for the map.js script
const mapboxApiKey = '<?php echo MAPBOX_API_KEY; ?>';

// Toggle review form
document.addEventListener('DOMContentLoaded', function() {
    const writeReviewBtn = document.getElementById('write-review-btn');
    const reviewFormContainer = document.getElementById('review-form-container');
    const cancelReviewBtn = document.getElementById('cancel-review');
    
    if (writeReviewBtn && reviewFormContainer) {
        writeReviewBtn.addEventListener('click', function() {
            reviewFormContainer.style.display = 'block';
            writeReviewBtn.style.display = 'none';
        });
    }
    
    if (cancelReviewBtn && reviewFormContainer && writeReviewBtn) {
        cancelReviewBtn.addEventListener('click', function() {
            reviewFormContainer.style.display = 'none';
            writeReviewBtn.style.display = 'inline-flex';
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

