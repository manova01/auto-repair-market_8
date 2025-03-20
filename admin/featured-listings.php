<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/classes/FeaturedListings.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

$db = new Database();
$featuredListings = new FeaturedListings($db);
$successMessage = $errorMessage = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $errorMessage = "Invalid form submission.";
    } else {
        // Add provider to featured listings
        if ($_POST['action'] === 'add_featured') {
            $providerId = filter_input(INPUT_POST, 'provider_id', FILTER_VALIDATE_INT);
            $packageId = filter_input(INPUT_POST, 'package_id', FILTER_VALIDATE_INT);
            $manualExpiry = filter_input(INPUT_POST, 'manual_expiry', FILTER_SANITIZE_STRING);
            
            if (!$providerId || !$packageId) {
                $errorMessage = "Invalid input data.";
            } else {
                $result = $featuredListings->addFeaturedProvider($providerId, $packageId, $manualExpiry);
                if ($result['success']) {
                    $successMessage = $result['message'];
                } else {
                    $errorMessage = $result['message'];
                }
            }
        }
        
        // Remove provider from featured listings
        if ($_POST['action'] === 'remove_featured') {
            $providerId = filter_input(INPUT_POST, 'provider_id', FILTER_VALIDATE_INT);
            
            if (!$providerId) {
                $errorMessage = "Invalid provider ID.";
            } else {
                $result = $featuredListings->removeFeaturedProvider($providerId);
                if ($result['success']) {
                    $successMessage = $result['message'];
                } else {
                    $errorMessage = $result['message'];
                }
            }
        }
    }
}

// Get featured packages
$packages = $featuredListings->getFeaturedPackages();

// Get featured providers
$featuredQuery = "SELECT p.*, u.email, u.phone, 
                 (SELECT COUNT(*) FROM reviews r WHERE r.provider_id = p.id) as review_count,
                 (SELECT AVG(rating) FROM reviews r WHERE r.provider_id = p.id) as avg_rating
                 FROM providers p 
                 JOIN users u ON p.user_id = u.id 
                 WHERE p.is_featured = 1 
                 ORDER BY p.featured_priority DESC, p.business_name ASC";

$featuredStmt = $db->prepare($featuredQuery);
$featuredStmt->execute();
$featuredProviders = $featuredStmt->fetchAll(PDO::FETCH_ASSOC);

// Get non-featured providers
$nonFeaturedQuery = "SELECT p.*, u.email, u.phone 
                    FROM providers p 
                    JOIN users u ON p.user_id = u.id 
                    WHERE p.is_featured = 0 
                    ORDER BY p.business_name ASC";

$nonFeaturedStmt = $db->prepare($nonFeaturedQuery);
$nonFeaturedStmt->execute();
$nonFeaturedProviders = $nonFeaturedStmt->fetchAll(PDO::FETCH_ASSOC);

// Page title
$pageTitle = "Manage Featured Listings";
require_once '../includes/admin-header.php';
?>

<div class="admin-container">
    <h1>Manage Featured Listings</h1>
    
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
    
    <div class="admin-card">
        <div class="card-header">
            <h2>Featured Packages</h2>
        </div>
        <div class="card-content">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Package</th>
                        <th>Description</th>
                        <th>Duration</th>
                        <th>Price</th>
                        <th>Priority</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($packages as $package): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($package['name']); ?></td>
                            <td><?php echo htmlspecialchars($package['description']); ?></td>
                            <td><?php echo $package['duration_days']; ?> days</td>
                            <td>$<?php echo number_format($package['price'], 2); ?></td>
                            <td><?php echo $package['priority']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="admin-card">
        <div class="card-header">
            <h2>Current Featured Providers</h2>
        </div>
        <div class="card-content">
            <?php if (empty($featuredProviders)): ?>
                <p>No featured providers at this time.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Provider</th>
                            <th>Contact</th>
                            <th>Featured Until</th>
                            <th>Priority</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($featuredProviders as $provider): ?>
                            <tr>
                                <td>
                                    <a href="../provider-profile.php?id=<?php echo $provider['id']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($provider['business_name']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($provider['email']); ?><br>
                                    <?php echo htmlspecialchars($provider['phone']); ?>
                                </td>
                                <td>
                                    <?php if ($provider['featured_until']): ?>
                                        <?php echo date('M j, Y', strtotime($provider['featured_until'])); ?>
                                        <?php 
                                            $daysLeft = ceil((strtotime($provider['featured_until']) - time()) / 86400);
                                            echo "<br><span class='" . ($daysLeft <= 3 ? 'text-danger' : 'text-muted') . "'>";
                                            echo $daysLeft > 0 ? "($daysLeft days left)" : "(Expired)";
                                            echo "</span>";
                                        ?>
                                    <?php else: ?>
                                        Unlimited
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $provider['featured_priority']; ?></td>
                                <td>
                                    <form method="post" onsubmit="return confirm('Are you sure you want to remove this provider from featured listings?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="remove_featured">
                                        <input type="hidden" name="provider_id" value="<?php echo $provider['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="admin-card">
        <div class="card-header">
            <h2>Add Provider to Featured Listings</h2>
        </div>
        <div class="card-content">
            <form method="post" class="admin-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="add_featured">
                
                <div class="form-group">
                    <label for="provider_id">Select Provider:</label>
                    <select name="provider_id" id="provider_id" class="form-control" required>
                        <option value="">-- Select Provider --</option>
                        <?php foreach ($nonFeaturedProviders as $provider): ?>
                            <option value="<?php echo $provider['id']; ?>">
                                <?php echo htmlspecialchars($provider['business_name']); ?> 
                                (<?php echo htmlspecialchars($provider['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="package_id">Select Package:</label>
                    <select name="package_id" id="package_id" class="form-control" required>
                        <option value="">-- Select Package --</option>
                        <?php foreach ($packages as $package): ?>
                            <option value="<?php echo $package['id']; ?>">
                                <?php echo htmlspecialchars($package['name']); ?> 
                                (<?php echo $package['duration_days']; ?> days - $<?php echo number_format($package['price'], 2); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="manual_expiry">Custom Expiry Date (Optional):</label>
                    <input type="date" name="manual_expiry" id="manual_expiry" class="form-control" 
                           min="<?php echo date('Y-m-d'); ?>">
                    <small class="form-text text-muted">Leave blank to use package duration</small>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Add to Featured</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>

