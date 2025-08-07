-- ============================================================================
-- BeyChoc Chocolate Shop Database Schema
-- Database creation script with tables and sample data
-- MySQL Port: 3305 (configured in config/database.php)
-- ============================================================================

-- Create database
CREATE DATABASE IF NOT EXISTS beychoc_db 
CHARACTER SET utf8 
COLLATE utf8_general_ci;

-- Use the database
USE beychoc_db;

-- ============================================================================
-- TABLE: products
-- Stores all chocolate products with categories and details
-- ============================================================================

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Product name',
    code VARCHAR(100) UNIQUE NOT NULL COMMENT 'Unique product code',
    description TEXT COMMENT 'Product description',
    image VARCHAR(500) COMMENT 'Product image path',
    category ENUM(
        'White Chocolate', 
        'Milk Chocolate', 
        'Dark Chocolate', 
        'Light Chocolate', 
        'Bars', 
        'Packages'
    ) NOT NULL COMMENT 'Product category',
    weight VARCHAR(50) NULL COMMENT 'Product weight (required for Bars and Packages)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp'
);

-- Indexes will be created automatically by the PHP application if needed
-- You can manually add these indexes later for better performance:
-- CREATE INDEX idx_products_category ON products(category);
-- CREATE INDEX idx_products_name ON products(name);  
-- CREATE INDEX idx_products_code ON products(code);

-- ============================================================================
-- TABLE: admin_sessions
-- Manages admin login sessions for security
-- ============================================================================

CREATE TABLE IF NOT EXISTS admin_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) UNIQUE NOT NULL COMMENT 'Unique session identifier',
    admin_username VARCHAR(255) NOT NULL COMMENT 'Admin username',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Session creation time',
    expires_at DATETIME NOT NULL COMMENT 'Session expiry time',
    is_active BOOLEAN DEFAULT TRUE COMMENT 'Session status'
);

-- Indexes for session lookups (optional - add manually if needed for performance):
-- CREATE INDEX idx_sessions_session_id ON admin_sessions(session_id);
-- CREATE INDEX idx_sessions_expires ON admin_sessions(expires_at);

-- ============================================================================
-- SAMPLE DATA: Insert sample products
-- ============================================================================

INSERT INTO products (name, code, description, image, category, weight) VALUES
-- White Chocolate Products
('White Chocolate Dreams', 'WC001', 'Pure white chocolate with vanilla bean essence and smooth texture.', 'assets/images/products/white-chocolate.jpg', 'White Chocolate', NULL),

-- Milk Chocolate Products
('Silky Milk Chocolate', 'MC001', 'Creamy milk chocolate made with finest Belgian cocoa and fresh cream.', 'assets/images/products/milk-chocolate.jpg', 'Milk Chocolate', NULL),

-- Dark Chocolate Products
('Premium Dark Chocolate', 'DC001', 'Rich 85% cocoa dark chocolate with intense flavor profile. Made with finest Belgian cocoa beans.', 'assets/images/products/dark-chocolate.jpg', 'Dark Chocolate', NULL),

-- Light Chocolate Products
('Light Chocolate Delights', 'LC001', 'Low-calorie chocolate option without compromising taste and quality.', 'assets/images/products/light-chocolate.jpg', 'Light Chocolate', NULL),
('Sugar-Free Milk Chocolate', 'LC002', 'Delicious milk chocolate made with natural sweeteners.', 'assets/images/products/sugar-free.jpg', 'Light Chocolate', NULL),

-- Chocolate Bars
('Artisan Chocolate Bar', 'BAR001', 'Hand-crafted chocolate bar with sea salt finish and premium cocoa.', 'assets/images/products/chocolate-bar.jpg', 'Bars', '100g'),
('Dark Chocolate Bar Supreme', 'BAR002', 'Premium dark chocolate bar with 90% cocoa content for true connoisseurs.', 'assets/images/products/dark-bar.jpg', 'Bars', '150g'),

-- Gift Packages
('Luxury Gift Package', 'PKG001', 'Assorted chocolates in elegant gift packaging. Perfect for special occasions.', 'assets/images/products/gift-package.jpg', 'Packages', '500g'),
('Executive Chocolate Collection', 'PKG002', 'Premium collection of our finest chocolates in a luxury wooden box.', 'assets/images/products/executive-package.jpg', 'Packages', '750g');

-- ============================================================================
-- USEFUL QUERIES
-- ============================================================================

-- View all products by category
-- SELECT * FROM products ORDER BY category, name;

-- Search products by name or code
-- SELECT * FROM products WHERE name LIKE '%chocolate%' OR code LIKE '%001%';

-- Get products by specific category
-- SELECT * FROM products WHERE category = 'Bars';

-- Get products that require weight (Bars and Packages)
-- SELECT * FROM products WHERE category IN ('Bars', 'Packages') AND weight IS NOT NULL;

-- Clean up expired sessions (run periodically)
-- DELETE FROM admin_sessions WHERE expires_at < NOW() OR is_active = FALSE;

-- ============================================================================
-- DATABASE STATISTICS
-- ============================================================================

-- Show table information
-- SHOW TABLE STATUS;

-- Count products by category
-- SELECT category, COUNT(*) as product_count FROM products GROUP BY category;

-- ============================================================================
-- ADMIN USER INFORMATION
-- ============================================================================

-- Admin Credentials (hardcoded in PHP):
-- Username: Lana Moghnieh
-- Password: 123454321

-- Note: Admin authentication is handled in PHP files, not stored in database
-- for security reasons as specified in requirements.

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
