<?php
$pageTitle = "Provider Registration";
$useMapbox = true;
require_once 'includes/header.php';

// Check if user is logged in and is eligible to become a provider
if (!isLoggedIn() && !isset($_SESSION['registration_user_id'])) {
    header('Location: ' . SITE_URL . '/register.php?type=provider');
    exit;
}

// Get user ID
$userId = isLoggedIn() ? $_SESSION['user_id'] : $_SESSION['registration_user_id'];

// Check if user is already a provider
if (isLoggedIn()) {
    $existingProvider = $db->selectOne("SELECT id FROM providers WHERE user_id = ?", [$userId]);
    if ($existingProvider) {
        header('Location: ' . SITE_URL . '/provider-dashboard.php');
        exit;
    }
}

// Get service categories
$serviceCategories = $db->select("SELECT * FROM service_categories ORDER BY name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    $businessName = isset($_POST['business_name']) ? sanitizeInput($_POST['business_name']) : '';
    $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
    $address = isset($_POST['address']) ? sanitizeInput($_POST['address']) : '';
    $city = isset($_POST['city']) ? sanitizeInput($_POST['city']) : '';
    $state = isset($_POST['state']) ? sanitizeInput($_POST['state']) : '';
    $zipCode = isset($_POST['zip_code']) ? sanitizeInput($_POST['zip_code']) : '';
    $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
    $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
    $website = isset($_POST['website']) ? sanitizeInput($_POST['website']) : '';
    
    // Services
    $services = isset($_POST['services']) ? $_POST['services'] : [];
    $servicePricesMin = isset($_POST['service_price_min']) ? $_POST['service_price_min'] : [];
    $servicePricesMax = isset($_POST['service_price_max']) ? $_POST['service_price_max'] : [];
    
    // Business hours
    $openTimes = isset($_POST['open_time']) ? $_POST['open_time'] : [];
    $closeTimes = isset($_POST['close_time']) ? $_POST['close_time'] : [];
    $isClosed = isset($_POST['is_closed']) ? $_POST['is_closed'] : [];
    
    // Validate input
    if (empty($businessName)) {
        $error = "Please enter your business name.";
    } elseif (empty($description)) {
        $error = "Please provide a description of your business.";
    } elseif (empty($address) || empty($city) || empty($state) || empty($zipCode)) {
        $error = "Please enter your complete business address.";
    } elseif ($latitude === null || $longitude === null) {
        $error = "Please select a valid address from the dropdown.";
    } elseif (empty($services)) {
        $error = "Please select at least one service that you provide.";
    } else {
        // Create provider
        $providerData = [
            'business_name' => $businessName,
            'description' => $description,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'zip_code' => $zipCode,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'website' => $website
        ];
        
        $result = createProvider($userId, $providerData);
        
        if ($result['success']) {
            $providerId = $result['provider_id'];
            
            // Add services
            foreach ($services as $categoryId) {
                $serviceData = [
                    'provider_id' => $providerId,
                    'category_id' => $categoryId,
                    'name' => '',
                    'description' => '',
                    'price_min' => isset($servicePricesMin[$categoryId]) ? floatval($servicePricesMin[$categoryId]) : null,
                    'price_max' => isset($servicePricesMax[$categoryId]) ? floatval($servicePricesMax[$categoryId]) : null
                ];
                
                // Get category name
                $category = $db->selectOne("SELECT name FROM service_categories WHERE id = ?", [$categoryId]);
                if ($category) {
                    $serviceData['name'] = $category['name'];
                }
                
                $db->insert('services', $serviceData);
            }
            
            // Update business hours
            foreach ($openTimes as $day => $time) {
                $isClosedDay = isset($isClosed[$day]);
                
                $db->update('business_hours', [
                    'open_time' => $isClosedDay ? null : $time,
                    'close_time' => $isClosedDay ? null : $closeTimes[$day],
                    'is_closed' => $isClosedDay ? 1 : 0
                ], 'provider_id = ? AND day_of_week = ?', [$providerId, $day]);
            }
            
            // Handle profile image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = uploadImage($_FILES['profile_image'], 'providers');
                
                if ($uploadResult['success']) {
                    $db->insert('provider_images', [
                        'provider_id' => $providerId,
                        'image_path' => $uploadResult['path'],
                        'is_profile' => 1
                    ]);
                }
            }
            
            // Handle cover image upload
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = uploadImage($_FILES['cover_image'], 'providers');
                
                if ($uploadResult['success']) {
                    $db->insert('provider_images', [
                        'provider_id' => $providerId,
                        'image_path' => $uploadResult['path'],
                        'is_cover' => 1
                    ]);
                }
            }
            
            // If this was a fresh registration, clean up the session
            if (isset($_SESSION['registration_user_id'])) {
                unset($_SESSION['registration_user_id']);
                
                // Log user in
                $user = $db->selectOne("SELECT * FROM users WHERE id = ?", [$userId]);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];
            }
            
            // Redirect to provider dashboard
            header('Location: ' . SITE_URL . '/provider-dashboard.php?welcome=1');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>

<div class="container">
    <div class="page-header">
        <h1>Register as a Service Provider</h1>
        <p>Complete your business profile to start receiving customers</p>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="provider-registration-card">
        <form id="provider-registration-form" method="post" action="" enctype="multipart/form-data">
            <div class="form-section">
                <h2>Business Information</h2>
                
                <div class="form-group">
                    <label for="business_name" class="form-label">Business Name</label>
                    <input type="text" id="business_name" name="business_name" class="form-control" required value="<?php echo isset($_POST['business_name']) ? htmlspecialchars($_POST['business_name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description" class="form-label">Business Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    <span class="form-text">Describe your business, services, and expertise</span>
                </div>
                
                <div class="form-group">
                    <label for="website" class="form-label">Website (Optional)</label>
                    <input type="url" id="website" name="website" class="form-control" value="<?php echo isset($_POST['website']) ? htmlspecialchars($_POST['website']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-section">
                <h2>Business Address</h2>
                
                <div class="form-group">
                    <label class="form-label">Search for your address</label>
                    <div id="geocoder-container"></div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="address" class="form-label">Street Address</label>
                        <input type="text" id="address" name="address" class="form-control" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="city" class="form-label">City</label>
                        <input type="text" id="city" name="city" class="form-control" required value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="state" class="form-label">State</label>
                        <input type="text" id="state" name="state" class="form-control" required value="<?php echo isset($_POST['state']) ? htmlspecialchars($_POST['state']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="zip_code" class="form-label">ZIP Code</label>
                        <input type="text" id="zip_code" name="zip_code" class="form-control" required value="<?php echo isset($_POST['zip_code']) ? htmlspecialchars($_POST['zip_code']) : ''; ?>">
                    </div>
                </div>
                
                <input type="hidden" id="latitude" name="latitude" value="<?php echo isset($_POST['latitude']) ? htmlspecialchars($_POST['latitude']) : ''; ?>">
                <input type="hidden" id="longitude" name="longitude" value="<?php echo isset($_POST['longitude']) ? htmlspecialchars($_POST['longitude']) : ''; ?>">
            </div>
            
            <div class="form-section">
                <h2>Services Offered</h2>
                <p>Select the services you provide and their price ranges</p>
                
                <div class="services-grid">
                    <?php foreach ($serviceCategories as $category): ?>
                    <div class="service-checkbox">
                        <input type="checkbox" id="service_<?php echo $category['id']; ?>" name="services[]" value="<?php echo $category['id']; ?>" <?php echo isset($_POST['services']) && in_array($category['id'], $_POST['services']) ? 'checked' : ''; ?>>
                        <label for="service_<?php echo $category['id']; ?>"><?php echo $category['name']; ?></label>
                        
                        <div class="service-prices" id="price_fields_<?php echo $category['id']; ?>">
                            <div class="price-field">
                                <label for="min_price_<?php echo $category['id']; ?>">Min $</label>
                                <input type="number" id="min_price_<?php echo $category['id']; ?>" name="service_price_min[<?php echo $category['id']; ?>]" min="0" step="0.01" value="<?php echo isset($_POST['service_price_min'][$category['id']]) ? htmlspecialchars($_POST['service_price_min'][$category['id']]) : ''; ?>">
                            </div>
                            <div class="price-field">
                                <label for="max_price_<?php echo $category['id']; ?>">Max $</label>
                                <input type="number" id="max_price_<?php echo $category['id']; ?>" name="service_price_max[<?php echo $category['id']; ?>]" min="0" step="0.01" value="<?php echo isset($_POST['service_price_max'][$category['id']]) ? htmlspecialchars($_POST['service_price_max'][$category['id']]) : ''; ?>">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-section">
                <h2>Business Hours</h2>
                
                <div class="business-hours-grid">
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach ($days as $index => $day):
                    ?>
                    <div class="business-day">
                        <div class="day-name">
                            <span><?php echo $day; ?></span>
                            <div class="closed-toggle">
                                <input type="checkbox" id="closed_<?php echo $day; ?>" name="is_closed[<?php echo $day; ?>]" data-day-index="<?php echo $day; ?>" <?php echo isset($_POST['is_closed'][$day]) ? 'checked' : ''; ?>>
                                <label for="closed_<?php echo $day; ?>">Closed</label>
                            </div>
                        </div>
                        <div class="day-hours">
                            <div class="time-field">
                                <label for="open_<?php echo $day; ?>">Open</label>
                                <input type="time" id="open_<?php echo $day; ?>" name="open_time[<?php echo $day; ?>]" value="<?php echo isset($_POST['open_time'][$day]) ? htmlspecialchars($_POST['open_time'][$day]) : '09:00'; ?>" <?php echo isset($_POST['is_closed'][$day]) ? 'disabled' : ''; ?>>
                            </div>
                            <div class="time-field">
                                <label for="close_<?php echo $day; ?>">Close</label>
                                <input type="time" id="close_<?php echo $day; ?>" name="close_time[<?php echo $day; ?>]" value="<?php echo isset($_POST['close_time'][$day]) ? htmlspecialchars($_POST['close_time'][$day]) : '17:00'; ?>" <?php echo isset($_POST['is_closed'][$day]) ? 'disabled' : ''; ?>>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-section">
                <h2>Profile Images</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" id="profile_image" name="profile_image" class="form-control" accept="image/*">
                        <span class="form-text">Recommended size: 400x400 pixels</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="cover_image" class="form-label">Cover Image</label>
                        <input type="file" id="cover_image" name="cover_image" class="form-control" accept="image/*">
                        <span class="form-text">Recommended size: 1200x300 pixels</span>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">Complete Registration</button>
            </div>
        </form>
    </div>
</div>

<script>
// Set Mapbox API key
mapboxgl.accessToken = '<?php echo MAPBOX_API_KEY; ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Mapbox geocoder
    const geocoder = new MapboxGeocoder({
        accessToken: mapboxgl.accessToken,
        types: 'address',
        countries: 'us'
    });
    
    // Add geocoder to container
    geocoder.addTo('#geocoder-container');
    
    // When an address is selected, fill in the form fields
    geocoder.on('result', function(e) {
        const result = e.result;
        
        // Extract address components
        let address = '';
        let city = '';
        let state = '';
        let zipCode = '';
        
        if (result.context) {
            result.context.forEach(context => {
                if (context.id.startsWith('postcode')) {
                    zipCode = context.text;
                } else if (context.id.startsWith('place')) {
                    city = context.text;
                } else if (context.id.startsWith('region')) {
                    state = context.text;
                }
            });
        }
        
        // Set address to the main text
        address = result.place_name.split(',')[0];
        
        // Fill form fields
        document.getElementById('address').value = address;
        document.getElementById('city').value = city;
        document.getElementById('state').value = state;
        document.getElementById('zip_code').value = zipCode;
        document.getElementById('latitude').value = result.center[1];
        document.getElementById('longitude').value = result.center[0];
    });
    
    // Toggle service price fields based on checkbox
    const serviceCheckboxes = document.querySelectorAll('input[name="services[]"]');
    serviceCheckboxes.forEach(checkbox => {
        const categoryId = checkbox.value;
        const priceFields = document.getElementById('price_fields_' + categoryId);
        
        // Initial state
        if (!checkbox.checked) {
            priceFields.style.display = 'none';
        }
        
        // Handle change
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                priceFields.style.display = 'flex';
            } else {
                priceFields.style.display = 'none';
            }
        });
    });
    
    // Toggle business hours inputs based on "Closed" checkbox
    const closedCheckboxes = document.querySelectorAll('input[name^="is_closed"]');
    closedCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const dayIndex = this.getAttribute('data-day-index');
            const openTimeInput = document.querySelector(`input[name="open_time[${dayIndex}]"]`);
            const closeTimeInput = document.querySelector(`input[name="close_time[${dayIndex}]"]`);
            
            if (this.checked) {
                openTimeInput.disabled = true;
                closeTimeInput.disabled = true;
            } else {
                openTimeInput.disabled = false;
                closeTimeInput.disabled = false;
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>

