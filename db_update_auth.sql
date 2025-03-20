-- Add new columns to users table for multiple auth methods
ALTER TABLE users ADD COLUMN auth_type ENUM('email', 'phone', 'google', 'facebook') DEFAULT 'email';
ALTER TABLE users ADD COLUMN social_id VARCHAR(255) NULL;
ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL;
ALTER TABLE users ADD COLUMN phone_verified TINYINT(1) DEFAULT 0;
ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL;

-- Create verification codes table for phone auth
CREATE TABLE IF NOT EXISTS verification_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) NOT NULL,
    code VARCHAR(10) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (phone)
);

-- Create oauth tokens table for managing social logins
CREATE TABLE IF NOT EXISTS oauth_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    provider ENUM('google', 'facebook') NOT NULL,
    access_token TEXT NOT NULL,
    refresh_token TEXT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY (user_id, provider)
);

-- Add indexes for better performance
ALTER TABLE users ADD INDEX (auth_type);
ALTER TABLE users ADD INDEX (social_id);
ALTER TABLE users ADD INDEX (phone);

