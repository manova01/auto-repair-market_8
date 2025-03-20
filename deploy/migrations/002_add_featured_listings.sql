-- Add featured column to providers table
ALTER TABLE providers 
ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN featured_until DATE NULL,
ADD COLUMN featured_priority INT NOT NULL DEFAULT 0;

-- Create featured_packages table
CREATE TABLE featured_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    duration_days INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    priority INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create featured_purchases table to track purchases
CREATE TABLE featured_purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    package_id INT NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date DATE NOT NULL,
    transaction_id VARCHAR(100),
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled', 'refunded') DEFAULT 'completed',
    FOREIGN KEY (provider_id) REFERENCES providers(id),
    FOREIGN KEY (package_id) REFERENCES featured_packages(id)
);

-- Insert default featured packages
INSERT INTO featured_packages (name, description, duration_days, price, priority) VALUES
('Bronze', 'Basic featured listing for 7 days', 7, 19.99, 1),
('Silver', 'Enhanced featured listing for 14 days', 14, 34.99, 2),
('Gold', 'Premium featured listing for 30 days', 30, 59.99, 3);

