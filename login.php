<?php
$pageTitle = "Login";
require_once 'includes/header.php';

// Check if already logged in
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $rememberMe = isset($_POST['remember_me']);
    
    if (empty($email)) {
        $error = "Please enter your email address.";
    } elseif (empty($password)) {
        $error = "Please enter your password.";
    } else {
        $result = loginUser($email, $password);
        
        if ($result['success']) {
            // Set remember me cookie if checked
            if ($rememberMe) {
                $token = generateRandomString(32);
                $userId = $result['user']['id'];
                
                // Store token in database
                $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                $db->insert('remember_tokens', [
                    'user_id' => $userId,
                    'token' => $token,
                    'expires_at' => $expiry
                ]);
                
                // Set cookie
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
            }
            
            // Redirect to requested page or dashboard
            $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : SITE_URL . '/dashboard.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to your account</p>
            </div>

            <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" action="" data-validate>
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="flex-between">
                        <div class="checkbox-group">
                            <input type="checkbox" id="remember_me" name="remember_me" <?php echo isset($_POST['remember_me']) ? 'checked' : ''; ?>>
                            <label for="remember_me">Remember me</label>
                        </div>
                        <a href="forgot-password.php" class="text-sm">Forgot password?</a>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                </div>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">Sign up</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

