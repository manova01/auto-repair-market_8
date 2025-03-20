<footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo SITE_NAME; ?></h3>
                    <p>Connecting car owners with trusted auto repair professionals since 2023.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/listings.php">Find Repair Shops</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php">Services</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about.php">About Us</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>For Providers</h3>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/provider-signup.php">Join as a Provider</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/provider-login.php">Provider Login</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/provider-resources.php">Resources</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <p>Email: info@autorepairmarketplace.com</p>
                    <p>Phone: (555) 123-4567</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <?php if (isset($useMapbox) && $useMapbox): ?>
    <script src="<?php echo SITE_URL; ?>/assets/js/map.js"></script>
    <?php  && $useMapbox): ?>
    <script src="<?php echo SITE_URL; ?>/assets/js/map.js"></script>
    <?php endif; ?>
    
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo SITE_URL; ?>/assets/js/<?php echo $script; ?>.js"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

Let's create the main CSS file:

```css file="assets/css/styles.css"
/* Base Styles */
:root {
  --primary-color: #3b82f6;
  --primary-dark: #2563eb;
  --secondary-color: #f59e0b;
  --text-color: #1f2937;
  --text-muted: #6b7280;
  --background-color: #f9fafb;
  --card-background: #ffffff;
  --border-color: #e5e7eb;
  --success-color: #10b981;
  --error-color: #ef4444;
  --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  color: var(--text-color);
  background-color: var(--background-color);
  line-height: 1.5;
}

.container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

a {
  color: var(--primary-color);
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

h1, h2, h3, h4, h5, h6 {
  margin-bottom: 0.5rem;
  font-weight: 600;
  line-height: 1.25;
}

p {
  margin-bottom: 1rem;
}

img {
  max-width: 100%;
  height: auto;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.btn:hover {
  text-decoration: none;
}

.btn i {
  margin-right: 0.5rem;
}

.btn-primary {
  background-color: var(--primary-color);
  color: white;
  border: 1px solid var(--primary-color);
}

.btn-primary:hover {
  background-color: var(--primary-dark);
  border-color: var(--primary-dark);
}

.btn-outline {
  background-color: transparent;
  color: var(--text-color);
  border: 1px solid var(--border-color);
}

.btn-outline:hover {
  background-color: var(--background-color);
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

/* Forms */
.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-control {
  display: block;
  width: 100%;
  padding: 0.5rem 0.75rem;
  font-size: 1rem;
  line-height: 1.5;
  color: var(--text-color);
  background-color: var(--card-background);
  background-clip: padding-box;
  border: 1px solid var(--border-color);
  border-radius: 0.375rem;
  transition: border-color 0.15s ease-in-out;
}

.form-control:focus {
  border-color: var(--primary-color);
  outline: 0;
}

.form-text {
  display: block;
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: var(--text-muted);
}

.form-error {
  color: var(--error-color);
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

/* Header */
.header {
  background-color: var(--card-background);
  box-shadow: var(--shadow);
  padding: 1rem 0;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.logo {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-color);
}

.logo span {
  color: var(--primary-color);
}

.main-nav ul {
  display: flex;
  list-style: none;
  gap: 1.5rem;
}

.main-nav a {
  color: var(--text-color);
  font-weight: 500;
}

.main-nav a.active {
  color: var(--primary-color);
}

.auth-buttons {
  display: flex;
  gap: 0.75rem;
}

/* Dropdown */
.dropdown {
  position: relative;
}

.dropdown-toggle {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  background: none;
  border: none;
  cursor: pointer;
  font-weight: 500;
}

.dropdown-menu {
  position: absolute;
  right: 0;
  top: 100%;
  min-width: 200px;
  background-color: var(--card-background);
  border-radius: 0.375rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  padding: 0.5rem 0;
  z-index: 10;
  display: none;
}

.dropdown-menu.show {
  display: block;
}

.dropdown-menu a {
  display: block;
  padding: 0.5rem 1rem;
  color: var(--text-color);
}

.dropdown-menu a:hover {
  background-color: var(--background-color);
  text-decoration: none;
}

/* Hero Section */
.hero {
  background-color: var(--primary-color);
  color: white;
  padding: 4rem 0;
  text-align: center;
}

.hero h1 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.hero p {
  font-size: 1.25rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.search-form {
  max-width: 600px;
  margin: 0 auto;
  display: flex;
  gap: 0.5rem;
}

.search-form .form-control {
  flex: 1;
}

/* Cards */
.card {
  background-color: var(--card-background);
  border-radius: 0.5rem;
  border: 1px solid var(--border-color);
  padding: 1.5rem;
  box-shadow: var(--shadow);
  margin-bottom: 1.5rem;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.card-title {
  font-size: 1.25rem;
  margin-bottom: 0;
}

/* Provider Profile */
.cover-image {
  height: 200px;
  overflow: hidden;
  border-radius: 0.5rem;
  margin-top: 2rem;
}

.cover-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.provider-info {
  position: relative;
  margin-top: -4rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: var(--card-background);
  border-radius: 0.5rem;
  border: 1px solid var(--border-color);
  padding: 1.5rem;
  box-shadow: var(--shadow);
}

.provider-avatar {
  width: 8rem;
  height: 8rem;
  border-radius: 50%;
  border: 4px solid var(--card-background);
  overflow: hidden;
  margin-top: -5rem;
}

.provider-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.provider-details {
  margin-top: 1rem;
  text-align: center;
  flex: 1;
}

.provider-name {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.verified-badge {
  color: var(--success-color);
  font-size: 1.25rem;
}

.provider-rating {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.stars {
  color: var(--secondary-color);
}

.rating-score {
  font-weight: 600;
}

.review-count {
  color: var(--text-muted);
}

.provider-location {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
  color: var(--text-muted);
}

.provider-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 1.5rem;
  width: 100%;
}

/* Provider Content */
.provider-content {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  margin-top: 2rem;
  margin-bottom: 2rem;
}

/* Services */
.services-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

.service-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 0.375rem;
}

.service-item i {
  color: var(--primary-color);
  font-size: 1.25rem;
  margin-top: 0.125rem;
}

.service-item h3 {
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.service-item p {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 0;
}

/* Reviews */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.review-item {
  padding-bottom: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.review-item:last-child {
  border-bottom: none;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.review-header h3 {
  font-size: 1rem;
  margin-bottom: 0;
}

.review-header span {
  font-size: 0.875rem;
  color: var(--text-muted);
}

.review-rating {
  margin: 0.25rem 0;
  color: var(--secondary-color);
}

.review-item p {
  margin-top: 0.5rem;
  margin-bottom: 0;
  color: var(--text-muted);
}

.view-all {
  text-align: center;
  margin-top: 1.5rem;
}

/* Map */
.provider-map {
  height: 300px;
  border-radius: 0.375rem;
  margin-bottom: 0.75rem;
}

.location-address {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 0;
}

/* Business Hours */
.hours-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.hours-header i {
  color: var(--primary-color);
}

.business-hours {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.hours-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
}

.hours-row span:first-child {
  font-weight: 500;
}

.hours-row span:last-child {
  color: var(--text-muted);
}

/* Footer */
.footer {
  background-color: var(--text-color);
  color: white;
  padding: 3rem 0 1.5rem;
}

.footer-content {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
}

.footer-section h3 {
  font-size: 1.125rem;
  margin-bottom: 1rem;
}

.footer-section p {
  color: rgba(255, 255, 255, 0.7);
}

.footer-section ul {
  list-style: none;
}

.footer-section ul li {
  margin-bottom: 0.5rem;
}

.footer-section a {
  color: rgba(255, 255, 255, 0.7);
  transition: color 0.2s ease;
}

.footer-section a:hover {
  color: white;
  text-decoration: none;
}

.social-icons {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.social-icons a {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.1);
  transition: background-color 0.2s ease;
}

.social-icons a:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.footer-bottom {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  text-align: center;
  font-size: 0.875rem;
  color: rgba(255, 255, 255, 0.5);
}

/* Alerts */
.alert {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 0.375rem;
}

.alert-success {
  background-color: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.2);
  color: var(--success-color);
}

.alert-error {
  background-color: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  color: var(--error-color);
}

.alert-info {
  background-color: rgba(59, 130, 246, 0.1);
  border: 1px solid rgba(59, 130, 246, 0.2);
  color: var(--primary-color);
}

