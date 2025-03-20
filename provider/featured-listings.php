<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/classes/FeaturedListings.php';

// Check if user is logged in and is a provider
if (!isLoggedIn() || !isProvider()) {
    header('Location: ' . SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$db = new Database();
$featuredListings = new FeaturedListings($db);
$successMessage = $errorMessage = null;

// Get current user and provider info
$user = getCurrentUser();
$provider = getCurrentProvider();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['package_id'])) {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errorMessage = "Invalid form submission.";
    } else {
        $packageId = filter_input(INPUT_POST, 'package_id', FILTER_VALIDATE_INT);
        
        if (!$packageId) {
            $errorMessage = "Invalid package selected.";
        } else {
            $result = $featuredListings->addFeaturedProvider($provider['id'], $packageId);
            if ($result['success']) {
                $successMessage = $result['message'];
                // Refresh provider data
                $provider = getCurrentProvider(true); // true forces a refresh
            } else {
                $errorMessage = $result['message'];
            }
        }
    }
}

// Get featured packages
$packages = $featuredListings->getFeaturedPackages('price ASC');

// Check if provider is already featured
$isFeatured = $provider['is_featured'] == 1;
$featuredUntil = $provider['featured_until'] ? date('F j, Y', strtotime($provider['featured_until'])) : null;

// Get purchase history
$purchaseHistory = $featuredListings->getPurchaseHistory($provider['id']);

// Page title
$pageTitle = "Featured Listings";
require_once '../includes/provider-header.php';
?>

<div class="provider-container">
    <h1>Featured Listings</h1>
    
    <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($successMessage); ?>
        </div>
    <?php endif; ?>
    
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>
    
    <div class="provider-card">
        <div class="card-header">
            <h2>Featured Status</h2>
        </div>
        <div class="card-content">
            <?php if ($isFeatured): ?>
                <div class="featured-status active">
                    <i class="fas fa-star"></i>
                    <div>
                        <h3>Your business is currently featured!</h3>
                        <?php if ($featuredUntil): ?>
                            <p>Featured until: <?php echo $featuredUntil; ?></p>
                            <?php 
                                $daysLeft = ceil((strtotime($provider['featured_until']) - time()) / 86400);
                                if ($daysLeft > 0) {
                                    echo "<p>$daysLeft days remaining</p>";
                                } else {
                                    echo "<p>Expiring today</p>";
                                }
                            ?>
                        <?php else: ?>
                            <p>Your business is permanently featured.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="featured-status inactive">
                    <i class="far fa-star"></i>
                    <div>
                        <h3>Your business is not currently featured</h3>
                        <p>Boost your visibility by purchasing a featured listing package below.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="provider-card">
        <div class="card-header">
            <h2>Featured Packages</h2>
        </div>
        <div class="card-content">
            <div class="packages-container">
                <?php foreach ($packages as $package): ?>
                    <div class="package-card">
                        <div class="package-header">
                            <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                            <div class="package-price">
                                <span class="price">$<?php echo number_format($package['price'], 2); ?></span>
                            </div>
                        </div>
                        <div class="package-content">
                            <p class="package-duration"><?php echo $package['duration_days']; ?> days</p>
                            <p class="package-description"><?php echo htmlspecialchars($package['description']); ?></p>
                            <ul class="package-features">
                                <li>Priority placement in search results</li>
                                <li>Featured badge on your listing</li>
                                <li>Highlighted in the featured providers section</li>
                                <li>Increased visibility to potential customers</li>
                            </ul>
                        </div>
                        <div class="package-footer">
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="package_id" value="<?php echo $package['id']; ?>">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <?php echo $isFeatured ? 'Extend Featured Status' : 'Get Featured'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <?php if (!empty($purchaseHistory)): ?>
    <div class="provider-card">
        <div class="card-header">
            <h2>Purchase History</h2>
        </div>
        <div class="card-content">
            <table class="provider-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Package</th>
                        <th>Amount</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchaseHistory as $purchase): ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($purchase['purchase_date'])); ?></td>
                            <td><?php echo htmlspecialchars($purchase['package_name']); ?></td>
                            <td>$<?php echo number_format($purchase['amount'], 2); ?></td>
                            <td><?php echo date('M j, Y', strtotime($purchase['expiry_date'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($purchase['status']); ?>">
                                    <?php echo ucfirst($purchase['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="provider-card">
        <div class="card-header">
            <h2>Benefits of Featured Listings</h2>
        </div>
        <div class="card-content">
            <div class="benefits-container">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Increased Visibility</h3>
                        <p>Featured listings appear at the top of search results, ensuring more potential customers see your business first.</p>
                    </div>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Stand Out from Competitors</h3>
                        <p>Your listing will be highlighted with a featured badge, making it stand out from regular listings.</p>
                    </div>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>More Bookings</h3>
                        <p>Featured providers receive up to 3x more bookings than non-featured providers.</p>
                    </div>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="benefit-content">
                        <h3>Homepage Exposure</h3>
                        <p>Featured providers are showcased on the homepage, exposing your business to all site visitors.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/provider-footer.php'; ?>

