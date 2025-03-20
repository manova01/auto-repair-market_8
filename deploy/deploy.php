<?php
/**
 * Rudzz Auto Repair Marketplace Deployment Script
 * 
 * This script handles database migrations and other deployment tasks
 * that need to be performed after files are uploaded to the server.
 */

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Database connection
$host = $_ENV['DB_HOST'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];
$name = $_ENV['DB_NAME'];

try {
    // Connect to database
    $db = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Check if migrations table exists
    $stmt = $db->query("SHOW TABLES LIKE 'migrations'");
    if ($stmt->rowCount() == 0) {
        // Create migrations table
        $db->exec("CREATE TABLE migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "Created migrations table.\n";
    }
    
    // Get executed migrations
    $executed = [];
    $stmt = $db->query("SELECT migration FROM migrations");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $executed[] = $row['migration'];
    }
    
    // Get all migration files
    $migrationFiles = glob(__DIR__ . '/migrations/*.sql');
    sort($migrationFiles);
    
    // Execute pending migrations
    foreach ($migrationFiles as $file) {
        $migrationName = basename($file);
        
        if (!in_array($migrationName, $executed)) {
            echo "Executing migration: $migrationName\n";
            
            // Read and execute migration
            $sql = file_get_contents($file);
            $db->exec($sql);
            
            // Record migration
            $stmt = $db->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $stmt->execute([$migrationName]);
            
            echo "Migration completed: $migrationName\n";
        }
    }
    
    echo "All migrations completed successfully.\n";
    
    // Additional deployment tasks
    echo "Clearing cache...\n";
    // Add cache clearing code here
    
    echo "Deployment completed successfully.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "Deployment error: " . $e->getMessage() . "\n";
    exit(1);
}

