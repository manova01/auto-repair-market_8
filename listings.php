<?php
$pageTitle = "Find Auto Repair Shops";
$useMapbox = true;

require_once 'includes/header.php';

// Get filter parameters
$serviceCategory = isset($_GET['service']) ? intval($_GET['service']) : null;
$city = isset($_GET['city']) ? sanitizeInput($_GET['city']) : null;
$minRating = isset($_GET['rating']) ? floatval($_GET['rating']) : null;
$sortBy = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'rating';

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build filters array
$filters = [];
if ($serviceCategory) {
    $filters['service_category'] = $serviceCategory;
}
if ($city) {
    $filters['city'] = $city;
}
if ($minRating) {
    $filters['min_rating'] = $minRating;
}
$filters['sort_by'] = $sortBy;

// Get providers
$providers = getProviders($perPage, $offset, $filters);

// Get total count for pagination
$countSql = "SELECT COUNT(DISTINCT p.id) as total FROM providers p";
if ($serviceCategory) {
    $countSql .= " JOIN services s ON p.id = s.provider_id AND s.category_id = " . $serviceCategory;
}
$whereConditions = [];
if ($city) {
    $whereConditions[] = "p.city = '" . $db->getConnection()->real_escape_string($city) . "'";
}
if ($minRating) {
    $whereConditions[] = "p.avg_rating >= " . $minRating;
}
if (!empty($whereConditions)) {
    $countSql .= " WHERE " . implode(" AND ", $whereConditions);
}
$totalCount = $db->selectOne($countSql)['total'];
$totalPages = ceil($totalCount / $perPage);

// Get service categories for filter
$serviceCategories = $db->select("SELECT * FROM service_categories ORDER BY name");

// Get cities for filter
$cities = $db->select("SELECT DISTINCT city FROM providers ORDER BY city");
?>

<div class="container">
    <div class="page-header">
        <h1>Find Auto Repair Shops</h1>
        <p>Browse trusted auto repair professionals in your area</p>
    </div>

    <div class="listings-container">
        <!-- Filters -->
        <div class="filters">
            <div class="card">
                <h2>Filters</h2>
                
                <form action="listings.php" method="get" id="filter-form">
                    <div class="filter-group">
                        <label for="service-type">Service Type</label>
                        <select id="service-type" name="service" class="form-control">
                            <option value="">All Services</option>
                            <?php foreach ($serviceCategories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $serviceCategory == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="city">City</label>
                        <select id="city" name="city" class="form-control">
                            <option value="">All Cities</option>
                            <?php foreach ($cities as $cityOption): ?>
                            <option value="<?php echo $cityOption['city']; ?>" <?php echo $city == $cityOption['city'] ? 'selected' : ''; ?>>
                                <?php echo $cityOption['city']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="rating">Minimum Rating</label>
                        <select id="rating" name="rating" class="form-control">
                            <option value="">Any Rating</option>
                            <option value="3" <?php echo $minRating == 3 ? 'selected' : ''; ?>>3+ Stars</option>
                            <option value="4" <?php echo $minRating == 4 ? 'selected' : ''; ?>>4+ Stars</option>
                            <option value="4.5" <?php echo $minRating == 4.5 ? 'selected' : ''; ?>>4.5+ Stars</option>
                            <option value="5" <?php echo $minRating == 5 ? 'selected' : ''; ?>>5 Stars</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="sort">Sort By</label>
                        <select id="sort" name="sort" class="form-control">
                            <option value="rating" <?php echo $sortBy == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                            <option value="reviews" <?php  ? 'selected' : ''; ?>>Highest Rated</option>
                            <option value="reviews" <?php echo $sortBy == 'reviews' ? 'selected' : ''; ?>>Most Reviews</option>
                            <option value="name" <?php echo $sortBy == 'name' ? 'selected' : ''; ?>>Name (A-Z)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="listings.php" class="btn btn-outline">Reset</a>
                </form>
            </div>
            
            <div class="card map-card">
                <h2>Map View</h2>
                <div id="listings-map" class="listings-map"></div>
            </div>
        </div>
        
        <!-- Results -->
        <div class="results">
            <div class="results-header">
                <div class="results-count">
                    <p><strong><?php echo $totalCount; ?></strong> repair shops found</p>
                </div>
                <div class="results-sort">
                    <label for="sort-by">Sort by:</label>
                    <select id="sort-by" class="form-control" onchange="updateSort(this.value)">
                        <option value="rating" <?php echo $sortBy == 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                        <option value="reviews" <?php echo $sortBy == 'reviews' ? 'selected' : ''; ?>>Most Reviews</option>
                        <option value="name" <?php echo $sortBy == 'name' ? 'selected' : ''; ?>>Name (A-Z)</option>
                    </select>
                </div>
            </div>
            
            <div class="provider-cards">
                <?php if (empty($providers)): ?>
                <div class="no-results">
                    <p>No providers found matching your criteria. Try adjusting your filters.</p>
                </div>
                <?php else: ?>
                <?php foreach ($providers as $provider): ?>
                <?php
                // Get provider image
                $image = $db->selectOne("
                    SELECT image_path FROM provider_images 
                    WHERE provider_id = ? AND is_profile = 1 
                    LIMIT 1
                ", [$provider['id']]);
                
                $imagePath = $image ? UPLOAD_URL . $image['image_path'] : SITE_URL . '/assets/images/placeholder.jpg';
                
                // Get provider services
                $services = $db->select("
                    SELECT s.name, c.name as category_name 
                    FROM services s 
                    JOIN service_categories c ON s.category_id = c.id 
                    WHERE s.provider_id = ? 
                    LIMIT 5
                ", [$provider['id']]);
                ?>
                <div class="provider-card">
                    <div class="provider-card-image">
                        <img src="<?php echo $imagePath; ?>" alt="<?php echo $provider['business_name']; ?>">
                    </div>
                    <div class="provider-card-content">
                        <div class="provider-card-header">
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
                        </div>
                        <div class="provider-card-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo $provider['city'] . ', ' . $provider['state']; ?></span>
                        </div>
                        <div class="provider-card-services">
                            <?php 
                            $displayedServices = array_slice($services, 0, 3);
                            foreach ($displayedServices as $service): 
                            ?>
                            <span class="service-tag"><?php echo $service['name']; ?></span>
                            <?php endforeach; ?>
                            <?php if (count($services) > 3): ?>
                            <span class="service-tag">+<?php echo count($services) - 3; ?> more</span>
                            <?php endif; ?>
                        </div>
                        <div class="provider-card-footer">
                            <a href="provider-profile.php?id=<?php echo $provider['id']; ?>" class="btn btn-outline btn-sm">View Profile</a>
                            <a href="tel:<?php echo $provider['phone']; ?>" class="btn btn-primary btn-sm">Call Now</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="pagination-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php else: ?>
                <button class="pagination-btn" disabled><i class="fas fa-chevron-left"></i></button>
                <?php endif; ?>
                
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $startPage + 4);
                if ($endPage - $startPage < 4) {
                    $startPage = max(1, $endPage - 4);
                }
                
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="pagination-btn <?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($endPage < $totalPages): ?>
                <span class="pagination-ellipsis">...</span>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>" class="pagination-btn">
                    <?php echo $totalPages; ?>
                </a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="pagination-btn">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php else: ?>
                <button class="pagination-btn" disabled><i class="fas fa-chevron-right"></i></button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Set Mapbox API key for the map.js script
const mapboxApiKey = '<?php echo MAPBOX_API_KEY; ?>';

// Provider data for map
const providers = [
    <?php foreach ($providers as $provider): ?>
    {
        id: <?php echo $provider['id']; ?>,
        name: "<?php echo addslashes($provider['business_name']); ?>",
        lat: <?php echo $provider['latitude']; ?>,
        lng: <?php echo $provider['longitude']; ?>,
        rating: <?php echo $provider['avg_rating']; ?>,
        reviewCount: <?php echo $provider['review_count']; ?>
    },
    <?php endforeach; ?>
];

// Update sort function
function updateSort(value) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', value);
    window.location.href = '?' + urlParams.toString();
}
</script>

<?php require_once 'includes/footer.php'; ?>

