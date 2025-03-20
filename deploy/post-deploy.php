<?php
/**
 * Post-deployment script for cPanel
 * 
 * This script is executed after files are deployed to cPanel.
 * It sets proper file permissions and performs other necessary tasks.
 */

// Define paths
$basePath = __DIR__ . '/..';
$uploadsPath = $basePath . '/uploads';
$cachePath = $basePath . '/cache';
$logPath = $basePath . '/logs';

// Create directories if they don't exist
$directories = [$uploadsPath, $cachePath, $logPath];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        echo "Creating directory: $dir\n";
        mkdir($dir, 0755, true);
    }
}

// Set directory permissions
$directoriesToChmod = [
    $uploadsPath => 0755,
    $cachePath => 0755,
    $logPath => 0755
];

foreach ($directoriesToChmod as $dir => $perm) {
    echo "Setting permissions for $dir to " . decoct($perm) . "\n";
    chmod($dir, $perm);
}

// Set file permissions
$filesToChmod = [
    $basePath . '/.htaccess' => 0644,
    $basePath . '/includes/config.php' => 0644
];

foreach ($filesToChmod as $file => $perm) {
    if (file_exists($file)) {
        echo "Setting permissions for $file to " . decoct($perm) . "\n";
        chmod($file, $perm);
    }
}

echo "Post-deployment tasks completed successfully.\n";

