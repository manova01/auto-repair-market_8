<?php
$pageTitle = "Book Appointment";
require_once 'includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get provider ID from URL
$providerId = isset($_GET['provider']) ? intval($_GET['provider']) : 0;

if (!$providerId) {
    // Redirect to listings if no provider ID provided
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

// Format business hours
$formattedHours = [];
foreach ($provider['hours'] as $hour) {
    $formattedHours[$hour['day_of_week']] = $hour;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = isset($_POST['service_id']) ? intval($_POST['service_id']) : null;
    $appointmentDate = isset($_POST['appointment_date']) ? sanitizeInput($_POST['appointment_date']) : '';
    $appointmentTime = isset($_POST['appointment_time']) ? sanitizeInput($_POST['appointment_time']) : '';
    $notes = isset($_POST['notes']) ? sanitizeInput($_POST['notes']) : '';
    
    // Validate input
    if (empty($appointmentDate)) {
        $error = "Please select an appointment date.";
    } elseif (empty($appointmentTime)) {
        $error = "Please select an appointment time.";
    } else {
        // Check if date is in the future
        $appointmentDateTime = new DateTime($appointmentDate . ' ' . $appointmentTime);
        $now = new DateTime();
        
        if ($appointmentDateTime <= $now) {
            $error = "Please select a future date and time.";
        } else {
            // Check if the provider is open on that day and time
            $dayOfWeek = date('l', strtotime($appointmentDate));
            
            if (!isset($formattedHours[$dayOfWeek]) || $formattedHours[$dayOfWeek]['is_closed']) {
                $error = "The provider is closed on $dayOfWeek. Please select another day.";
            } else {
                $openTime = strtotime($formattedHours[$dayOfWeek]['open_time']);
                $closeTime = strtotime($formattedHours[$dayOfWeek]['close_time']);
                $selectedTime = strtotime($appointmentTime);
                
                if ($selectedTime < $openTime || $selectedTime > $closeTime) {
                    $error = "The selected time is outside of business hours. Business hours on $dayOfWeek are " . 
                             formatTime($formattedHours[$dayOfWeek]['open_time']) . " to " . 
                             formatTime($formattedHours[$dayOfWeek]['close_time']);
                } else {
                    // Check if the slot is available
                    $existingAppointment = $db->selectOne("
                        SELECT id FROM appointments 
                        WHERE provider_id = ? AND appointment_date = ? AND appointment_time = ? AND status != 'cancelled'
                    ", [$providerId, $appointmentDate, $appointmentTime]);
                    
                    if ($existingAppointment) {
                        $error = "This time slot is already booked. Please select another time.";
                    } else {
                        // Create appointment
                        $appointmentId = $db->insert('appointments', [
                            'provider_id' => $providerId,
                            'user_id' => $_SESSION['user_id'],
                            'service_id' => $serviceId ?: null,
                            'appointment_date' => $appointmentDate,
                            'appointment_time' => $appointmentTime,
                            'status' => 'pending',
                            'notes' => $notes
                        ]);
                        
                        if ($appointmentId) {
                            // Success! Redirect to confirmation page
                            header('Location: ' . SITE_URL . '/booking-confirmation.php?id=' . $appointmentId);
                            exit;
                        } else {
                            $error = "Failed to create appointment. Please try again.";
                        }
                    }
                }
            }
        }
    }
}

// Get provider's services
$services = $provider['services'];
?>

<div class="container">
    <div class="page-header">
        <h1>Book an Appointment</h1>
        <p>with <?php echo $provider['business_name']; ?></p>
    </div>

    <div class="booking-container">
        <div class="booking-provider-info">
            <div class="provider-snapshot">
                <?php
                // Get provider profile image
                $profileImage = null;
                foreach ($provider['images'] as $image) {
                    if ($image['is_profile']) {
                        $profileImage = $image;
                        break;
                    }
                }
                ?>
                <div class="provider-avatar">
                    <?php if ($profileImage): ?>
                    <img src="<?php echo UPLOAD_URL . $profileImage['image_path']; ?>" alt="<?php echo $provider['business_name']; ?>">
                    <?php else: ?>
                    <img src="<?php echo SITE_URL; ?>/assets/images/avatar-placeholder.jpg" alt="<?php echo $provider['business_name']; ?>">
                    <?php endif; ?>
                </div>
                
                <div class="provider-details">
                    <h2><?php echo $provider['business_name']; ?></h2>
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
                        <span><?php echo number_format($rating, 1); ?> (<?php echo $provider['review_count']; ?> reviews)</span>
                    </div>
                    
                    <div class="provider-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo $provider['address'] . ', ' . $provider['city'] . ', ' . $provider['state']; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="business-hours">
                <h3>Business Hours</h3>
                <div class="hours-list">
                    <?php
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    foreach ($days as $day):
                        $hour = isset($formattedHours[$day]) ? $formattedHours[$day] : null;
                    ?>
                    <div class="hours-item">
                        <span class="day"><?php echo $day; ?></span>
                        <span class="hours">
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
            </div>
        </div>
        
        <div class="booking-form-container">
            <div class="card">
                <div class="card-header">
                    <h2>Schedule Your Appointment</h2>
                </div>
                
                <div class="card-content">
                    <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" action="" data-validate>
                        <div class="form-group">
                            <label for="service_id" class="form-label">Select Service</label>
                            <select id="service_id" name="service_id" class="form-control">
                                <option value="">General Service</option>
                                <?php foreach ($services as $service): ?>
                                <option value="<?php echo $service['id']; ?>" <?php echo isset($_POST['service_id']) && $_POST['service_id'] == $service['id'] ? 'selected' : ''; ?>>
                                    <?php echo $service['name']; ?> 
                                    <?php
                                    if ($service['price_min'] && $service['price_max']) {
                                        echo '($' . number_format($service['price_min'], 2) . ' - $' . number_format($service['price_max'], 2) . ')';
                                    } elseif ($service['price_min']) {
                                        echo '(From $' . number_format($service['price_min'], 2) . ')';
                                    } elseif ($service['price_max']) {
                                        echo '(Up to $' . number_format($service['price_max'], 2) . ')';
                                    }
                                    ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="appointment_date" class="form-label">Date</label>
                                <input type="date" id="appointment_date" name="appointment_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo isset($_POST['appointment_date']) ? htmlspecialchars($_POST['appointment_date']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="appointment_time" class="form-label">Time</label>
                                <select id="appointment_time" name="appointment_time" class="form-control" required>
                                    <option value="">Select a date first</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea id="notes" name="notes" class="form-control" rows="3"><?php echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : ''; ?></textarea>
                            <span class="form-text">Describe your service needs or any special instructions</span>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-block">Book Appointment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    
    // Provider's business hours
    const businessHours = {
        <?php foreach ($formattedHours as $day => $hour): ?>
        '<?php echo $day; ?>': {
            is_closed: <?php echo $hour['is_closed'] ? 'true' : 'false'; ?>,
            open_time: '<?php echo $hour['open_time']; ?>',
            close_time: '<?php echo $hour['close_time']; ?>'
        },
        <?php endforeach; ?>
    };
    
    // Update available time slots when date changes
    dateInput.addEventListener('change', function() {
        // Clear existing options
        timeSelect.innerHTML = '';
        
        if (!this.value) {
            timeSelect.innerHTML = '<option value="">Select a date first</option>';
            return;
        }
        
        // Get day of week
        const date = new Date(this.value);
        const dayOfWeek = date.toLocaleDateString('en-US', { weekday: 'long' });
        
        // Check if business is closed on selected day
        if (!businessHours[dayOfWeek] || businessHours[dayOfWeek].is_closed) {
            timeSelect.innerHTML = '<option value="">Closed on ' + dayOfWeek + '</option>';
            return;
        }
        
        // Generate time slots
        const openTime = new Date('2000-01-01T' + businessHours[dayOfWeek].open_time);
        const closeTime = new Date('2000-01-01T' + businessHours[dayOfWeek].close_time);
        
        // Generate 30-minute intervals
        const interval = 30 * 60 * 1000; // 30 minutes in milliseconds
        let currentTime = openTime;
        
        while (currentTime < closeTime) {
            const timeString = currentTime.toTimeString().substring(0, 5);
            const formattedTime = formatTime(timeString);
            
            const option = document.createElement('option');
            option.value = timeString;
            option.textContent = formattedTime;
            
            // Check if this is the previously selected time
            if ('<?php echo isset($_POST['appointment_time']) ? $_POST['appointment_time'] : ''; ?>' === timeString) {
                option.selected = true;
            }
            
            timeSelect.appendChild(option);
            
            // Add 30 minutes
            currentTime = new Date(currentTime.getTime() + interval);
        }
    });
    
    // Trigger change event to populate time slots if date is already selected
    if (dateInput.value) {
        dateInput.dispatchEvent(new Event('change'));
    }
    
    // Helper function to format time
    function formatTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours, 10);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return hour12 + ':' + minutes + ' ' + ampm;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

