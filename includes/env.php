<?php
/**
 * Environment variable loader for Rudzz Auto Repair Marketplace
 */

// Check if Composer autoloader exists
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // Load environment variables from .env file if it exists
    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
    }
}

/**
 * Get environment variable with fallback
 * 
 * @param string $key Environment variable name
 * @param mixed $default Default value if not found
 * @return mixed
 */
function env($key, $default = null) {
    $value = getenv($key);
    
    if ($value === false) {
        // Check if defined in $_ENV
        if (isset($_ENV[$key])) {
            $value = $_ENV[$key];
        } elseif (isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        } else {
            return $default;
        }
    }
    
    // Convert string values
    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'null':
        case '(null)':
            return null;
        case 'empty':
        case '(empty)':
            return '';
    }
    
    return $value;
}

