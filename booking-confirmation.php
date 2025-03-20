<?php
$pageTitle = "Booking Confirmation";
require_once 'includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get appointment ID from URL
$appointmentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$appointmentId) {
    // Redirect to dashboard if no ID provided
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}

// Get appointment details
$appointment = $db->selectOne("
    SELECT a.*, p.business_name, p.address, p.city, p.state, p.zip_code, p.phone, s.name as service_name
    FROM appointments a
    JOIN providers p ON a.provider_id = p.id
    LEFT JOIN services s ON a.service_id = s.id
    WHERE a.id = ? AND a.user_id = ?
", [$appointmentId, $_SESSION['user_id']]);

if (!$appointment) {
    // Appointment not found or doesn't belong to the user
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}
?>

<div class="container">
    <div class="confirmation-container">
        <div class="confirmation-card">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>Your Appointment is Confirmed!</h1>
            <p class="confirmation-message">We've sent a confirmation email to your registered email address.</p>
            
            <div class="appointment-details">
                <div class="detail-group">
                    <h3>Appointment Details</h3>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value"><?php echo formatDate($appointment['appointment_date']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Time:</span>
                        <span class="detail-value"><?php echo formatTime($appointment['appointment_time']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Service:</span>
                        <span class="detail-value"><?php echo $appointment['service_name'] ?: 'General Service'; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value status-<?php echo strtolower($appointment['status']); ?>"><?php echo ucfirst($appointment['status']); ?></span>
                    </div>
                </div>
                
                <div class="detail-group">
                    <h3>Provider Information</h3>
                    <div class="detail-row">
                        <span class="detail-label">Name:</span>
                        <span class="detail-value"><?php echo $appointment['business_name']; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value"><?php echo $appointment['address'] . ', ' . $appointment['city'] . ', ' . $appointment['state'] . ' ' . $appointment['zip_code']; ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value"><?php echo $appointment['phone']; ?></span>
                    </div>
                </div>
                
                <?php if ($appointment['notes']): ?>
                <div class="detail-group">
                    <h3>Notes</h3>
                    <div class="detail-row">
                        <span class="detail-value"><?php echo nl2br($appointment['notes']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="confirmation-actions">
                <a href="<?php echo SITE_URL; ?>/appointments.php" class="btn btn-primary">View All Appointments</a>
                <a href="<?php echo SITE_URL; ?>/provider-profile.php?id=<?php echo $appointment['provider_id']; ?>" class="btn btn-outline">Provider Profile</a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

