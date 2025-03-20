<?php
$pageTitle = "Register";
require_once 'includes/header.php';

// Check if already logged in
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = isset($_POST['first_name']) ? sanitizeInput($_POST['first_name']) : '';
    $lastName = isset($_POST['last_name']) ? sanitizeInput($_POST['last_name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $userType = isset($_POST['user_type']) ? sanitizeInput($_POST['user_type']) : 'customer';
    
    // Validate input
    if (empty($firstName)) {
        $error = "Please enter your first name.";
    } elseif (empty($lastName)) {
        $error = "Please enter your last name.";
    } elseif (empty($email)) {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (empty($password)) {
        $error = "Please enter a password.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $existingUser = $db->selectOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existingUser) {
            $error = "Email address is already registered.";
        } else {
            // Register user
            $result = registerUser($firstName, $lastName, $email, $password, $phone, $userType);
            
            if ($result['success']) {
                // If user type is provider, redirect to provider registration
                if ($userType === 'provider') {
                    $_SESSION['registration_user_id'] = $result['user_id'];
                    header('Location: ' . SITE_URL . '/provider-signup.php');
                    exit;
                } else {
                    // Automatically log in the user
                    loginUser($email, $password);
                    
                    // Redirect to dashboard or requested page
                    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : SITE_URL . '/dashboard.php';
                    header('Location: ' . $redirect);
                    exit;
                }
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Default user type
$defaultUserType = isset($_GET['type']) && $_GET['type'] === 'provider' ? 'provider' : 'customer';
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card wide">
            <div class="auth-header">
                <h1>Create an Account</h1>
                <p>Join our auto repair marketplace</p>
            </div>

            <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="user-type-toggle">
                <button class="user-type-btn <?php echo $defaultUserType === 'customer' ? 'active' : ''; ?>" data-type="customer">Customer</button>
                <button class="user-type-btn <?php echo $defaultUserType === 'provider' ? 'active' : ''; ?>" data-type="provider">Service Provider</button>
            </div>

            <form method="post" action="" data-validate>
                <input type="hidden" name="user_type" id="user_type" value="<?php echo $defaultUserType; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <span class="form-text">Must be at least 8 characters long</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">I agree to the <a href="terms-of-service.php" target="_blank">Terms of Service</a> and <a href="privacy-policy.php" target="_blank">Privacy Policy</a></label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">
                        <span id="customer-submit-text">Create Account</span>
                        <span id="provider-submit-text" style="display: none;">Continue to Business Details</span>
                    </button>
                </div>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">Sign in</a></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const userTypeButtons = document.querySelectorAll('.user-type-btn');
    const userTypeInput = document.getElementById('user_type');
    const customerSubmitText = document.getElementById('customer-submit-text');
    const providerSubmitText = document.getElementById('provider-submit-text');
    
    userTypeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            userTypeButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Update hidden input value
            userTypeInput.value = this.getAttribute('data-type');
            
            // Update submit button text
            if (this.getAttribute('data-type') === 'provider') {
                customerSubmitText.style.display = 'none';
                providerSubmitText.style.display = 'inline';
            } else {
                customerSubmitText.style.display = 'inline';
                providerSubmitText.style.display = 'none';
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>

