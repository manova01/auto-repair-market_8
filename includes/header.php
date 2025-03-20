<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/styles.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/responsive.css">
    <?php if (isset($useMapbox) && $useMapbox): ?>
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
    <?php endif; ?>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo SITE_URL; ?>" class="logo">Auto<span>Repair</span></a>
                
                <nav class="main-nav">
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/listings.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'listings.php') ? 'class="active"' : ''; ?>>Find Repair Shops</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'services.php') ? 'class="active"' : ''; ?>>Services</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'class="active"' : ''; ?>>About</a></li>
                    </ul>
                </nav>
                
                <div class="auth-buttons">
                    <?php if (isLoggedIn()): ?>
                        <div class="dropdown">
                            <button class="dropdown-toggle">
                                <i class="fas fa-user-circle"></i>
                                <?php echo $_SESSION['user_name']; ?>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a>
                                <?php if (isProvider()): ?>
                                <a href="<?php echo SITE_URL; ?>/provider-dashboard.php">Provider Dashboard</a>
                                <?php endif; ?>
                                <?php if (isAdmin()): ?>
                                <a href="<?php echo SITE_URL; ?>/admin/">Admin Panel</a>
                                <?php endif; ?>
                                <a href="<?php echo SITE_URL; ?>/profile.php">My Profile</a>
                                <a href="<?php echo SITE_URL; ?>/messages.php">Messages</a>
                                <a href="<?php echo SITE_URL; ?>/logout.php">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline">Log In</a>
                        <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

