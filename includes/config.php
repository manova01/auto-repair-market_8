<?php
/**
 * Configuration file for Rudzz Auto Repair Marketplace
 */

// Load environment variables
require_once __DIR__ . '/env.php';

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'rudzz_user');  // Your database username
define('DB_PASS', 'your_strong_password');  // Your database password
define('DB_NAME', 'rudzz_auto');  // Your database name

// Application configuration
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', env('APP_DEBUG', false));
define('SITE_NAME', 'Rudzz Auto');
define('SITE_URL', 'https://rudzz.yourdomain.com'); // Update with your actual domain
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Authentication
define('JWT_SECRET', env('JWT_SECRET', ''));

// OAuth configuration
define('GOOGLE_CLIENT_ID', 'your-google-client-id.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'your-google-client-secret');
define('GOOGLE_REDIRECT_URI', SITE_URL . '/auth/google-callback.php');

define('FACEBOOK_APP_ID', 'your-facebook-app-id');
define('FACEBOOK_APP_SECRET', 'your-facebook-app-secret');
define('FACEBOOK_REDIRECT_URI', SITE_URL . '/auth/facebook-callback.php');

// SMS Service (Twilio)
define('TWILIO_ACCOUNT_SID', 'your-twilio-account-sid');
define('TWILIO_AUTH_TOKEN', 'your-twilio-auth-token');
define('TWILIO_PHONE_NUMBER', '+1234567890');

// Mapbox API key
define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY'); // Replace with your Google Maps API key
define('MAPBOX_API_KEY', 'pk.eyJ1Ijoicm9iMjMiLCJhIjoiY2tvbzViOHdsMDg1bTJvcGljbHp0ZTZrYyJ9.12KbwskPePI6RYd0K6E5Ew');

// Email configuration
define('SMTP_HOST', 'smtp.yourdomain.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@yourdomain.com');
define('SMTP_PASS', 'your_email_password');
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// Session configuration
session_start();

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

