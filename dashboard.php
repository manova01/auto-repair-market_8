<?php
$pageTitle = "Dashboard";
require_once 'includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get user info
$user = getCurrentUser();

// Get user's appointments
$appointments = $db->select("
    SELECT a.*, p.business_name, s.name as service_name
    FROM appointments a
    JOIN providers p ON a.provider_id = p.id
    LEFT JOIN services s ON a.service_id = s.id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
", [$user['id']]);

// Get user's reviews
$reviews = $db->select("
    SELECT r.*, p.business_name
    FROM reviews r
    JOIN providers p ON r.provider_id = p.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
", [$user['id']]);

// Get user's recent messages
$messages = $db->select("
    SELECT m.*, 
           CASE 
               WHEN m.sender_id = ? THEN 'sent' 
               ELSE 'received' 
           END as direction,
           CASE 
               WHEN m.sender_id = ? THEN u.first_name
               ELSE u2.first_name
           END as other_name,
           CASE 
               WHEN m.sender_id = ? THEN u.last_name
               ELSE u2.last_name
           END as other_last_name,
           CASE 
               WHEN m.sender_id = ? THEN u.id
               ELSE u2.id
           END as other_id,
           p.id as provider_id,
           p.business_name
    FROM messages m
    JOIN users u ON (m.sender_id = ? AND m.receiver_id = u.id) OR (m.receiver_id = ? AND m.sender_id = u.id)
    LEFT JOIN users u2 ON (m.sender_id = ? AND m.sender_id = u2.id) OR (m.receiver_id = ? AND m.receiver_id = u2.id)
    LEFT JOIN providers p ON (u.user_type = 'provider' AND u.id = p.user_id) OR (u2.user_type = 'provider' AND u2.id = p.user_id)
    WHERE m.sender_id = ? OR m.receiver_id = ?
    GROUP BY CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END
    ORDER BY m.created_at DESC
    LIMIT 5
", [$user['id'], $user['id'], $user['id'], $user['id'], $user['id'], $user['id'], $user['id'], $user['id'], $user['id'], $user['id'], $user['id']]);

// Handle welcome message
$showWelcome = isset($_GET['welcome']) && $_GET['welcome'] == 1;
?>

<div class="container">
    <div class="dashboard">
        <?php if ($showWelcome): ?>
        <div class="alert alert-success">
            Welcome to your dashboard! Your account has been created successfully.
        </div>
        <?php endif; ?>
        
        <div class="dashboard-header">
            <h1>Welcome, <?php echo $user['first_name']; ?>!</h1>
            <div class="dashboard-actions">
                <a href="<?php echo SITE_URL; ?>/profile.php" class="btn btn-outline">
                    <i class="fas fa-user"></i> My Profile
                </a>
                <?php if (isProvider()): ?>
                <a href="<?php echo SITE_URL; ?>/provider-dashboard.php" class="btn btn-primary">
                    <i class="fas fa-store"></i> Provider Dashboard
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <!-- Appointments Section -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-calendar-alt"></i> My Appointments</h2>
                    <a href="<?php echo SITE_URL; ?>/appointments.php" class="btn btn-sm btn-outline">View All</a>
                </div>
                
                <div class="card-content">
                    <?php if (empty($appointments)): ?>
                    <div class="empty-state">
                        <p>You don't have any appointments yet.</p>
                        <a href="<?php echo SITE_URL; ?>/listings.php" class="btn btn-primary">Find a Service Provider</a>
                    </div>
                    <?php else: ?>
                    <div class="appointments-list">
                        <?php foreach (array_slice($appointments, 0, 3) as $appointment): ?>
                        <div class="appointment-item">
                            <div class="appointment-date">
                                <span class="date"><?php echo formatDate($appointment['appointment_date']); ?></span>
                                <span class="time"><?php echo formatTime($appointment['appointment_time']); ?></span>
                            </div>
                            <div class="appointment-details">
                                <h3><?php echo $appointment['business_name']; ?></h3>
                                <p><?php echo $appointment['service_name'] ?: 'General Service'; ?></p>
                                <span class="status status-<?php echo strtolower($appointment['status']); ?>"><?php echo ucfirst($appointment['status']); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($appointments) > 3): ?>
                    <div class="view-more">
                        <a href="<?php echo SITE_URL; ?>/appointments.php">View all appointments</a>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Messages Section -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-comment-alt"></i> Recent Messages</h2>
                    <a href="<?php echo SITE_URL; ?>/messages.php" class="btn btn-sm btn-outline">View All</a>
                </div>
                
                <div class="card-content">
                    <?php if (empty($messages)): ?>
                    <div class="empty-state">
                        <p>You don't have any messages yet.</p>
                        <a href="<?php echo SITE_URL; ?>/listings.php" class="btn btn-primary">Find a Service Provider</a>
                    </div>
                    <?php else: ?>
                    <div class="messages-list">
                        <?php foreach ($messages as $message): ?>
                        <a href="<?php echo SITE_URL; ?>/messages.php?user=<?php echo $message['other_id']; ?>" class="message-item">
                            <div class="message-info">
                                <h3>
                                    <?php if ($message['business_name']): ?>
                                    <?php echo $message['business_name']; ?>
                                    <?php else: ?>
                                    <?php echo $message['other_name'] . ' ' . $message['other_last_name']; ?>
                                    <?php endif; ?>
                                </h3>
                                <span class="message-time"><?php echo getTimeAgo($message['created_at']); ?></span>
                            </div>
                            <p class="message-preview"><?php echo substr($message['message'], 0, 50); ?><?php echo strlen($message['message']) > 50 ? '...' : ''; ?></p>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Reviews Section -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-star"></i> My Reviews</h2>
                    <a href="<?php echo SITE_URL; ?>/my-reviews.php" class="btn btn-sm btn-outline">View All</a>
                </div>
                
                <div class="card-content">
                    <?php if (empty($reviews)): ?>
                    <div class="empty-state">
                        <p>You haven't written any reviews yet.</p>
                        <a href="<?php echo SITE_URL; ?>/listings.php" class="btn btn-primary">Find a Service Provider</a>
                    </div>
                    <?php else: ?>
                    <div class="reviews-list">
                        <?php foreach (array_slice($reviews, 0, 3) as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <h3><?php echo $review['business_name']; ?></h3>
                                <span class="review-date"><?php echo getTimeAgo($review['created_at']); ?></span>
                            </div>
                            <div class="review-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'filled' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="review-comment"><?php echo substr($review['comment'], 0, 100); ?><?php echo strlen($review['comment']) > 100 ? '...' : ''; ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($reviews) > 3): ?>
                    <div class="view-more">
                        <a href="<?php echo SITE_URL; ?>/my-reviews.php">View all reviews</a>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                </div>
                
                <div class="card-content">
                    <div class="quick-actions">
                        <a href="<?php echo SITE_URL; ?>/listings.php" class="quick-action">
                            <i class="fas fa-search"></i>
                            <span>Find Services</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/appointments.php?action=new" class="quick-action">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Book Appointment</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/messages.php" class="quick-action">
                            <i class="fas fa-comment"></i>
                            <span>Messages</span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/profile.php" class="quick-action">
                            <i class="fas fa-user-edit"></i>
                            <span>Edit Profile</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

