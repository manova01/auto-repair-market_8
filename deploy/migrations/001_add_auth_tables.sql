-- Migration: Add authentication tables
-- Date: 2023-03-17

-- Create social_logins table if it doesn't exist
CREATE TABLE IF NOT EXISTS social_logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    provider VARCHAR(20) NOT NULL,
    provider_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_provider_id (provider, provider_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create phone_verifications table if it doesn't exist
CREATE TABLE IF NOT EXISTS phone_verifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) NOT NULL,
    code VARCHAR(10) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    verified TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_phone (phone)
);

-- Add phone_verified column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone_verified TINYINT(1) DEFAULT 0;

