<?php
/**
 * BeyChoc Database Configuration
 * Database connection and configuration settings
 */

class Database {
    private $host = "localhost";
    private $port = "3305";
    private $db_name = "beychoc_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, 
                                  $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

    public function createDatabaseAndTables() {
        try {
            // First, connect without database to create it
            $tempConn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port, $this->username, $this->password);
            $tempConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create database if it doesn't exist
            $tempConn->exec("CREATE DATABASE IF NOT EXISTS " . $this->db_name . " CHARACTER SET utf8 COLLATE utf8_general_ci");
            
            // Now connect to the created database
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, 
                                  $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create products table
            $createProductsTable = "
                CREATE TABLE IF NOT EXISTS products (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    code VARCHAR(100) UNIQUE NOT NULL,
                    description TEXT,
                    image VARCHAR(500),
                    category ENUM('White Chocolate', 'Milk Chocolate', 'Dark Chocolate', 'Light Chocolate', 'Bars', 'Packages') NOT NULL,
                    weight VARCHAR(50) NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ";
            
            $this->conn->exec($createProductsTable);
            
            // Create admin_sessions table
            $createSessionsTable = "
                CREATE TABLE IF NOT EXISTS admin_sessions (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    session_id VARCHAR(255) UNIQUE NOT NULL,
                    admin_username VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    expires_at TIMESTAMP NOT NULL,
                    is_active BOOLEAN DEFAULT TRUE
                )
            ";
            
            $this->conn->exec($createSessionsTable);
            
            // Insert sample products if table is empty
            $checkProducts = "SELECT COUNT(*) as count FROM products";
            $stmt = $this->conn->prepare($checkProducts);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] == 0) {
                $this->insertSampleProducts();
            }
            
            return true;
        } catch(PDOException $exception) {
            echo "Database setup error: " . $exception->getMessage();
            return false;
        }
    }

    public function insertSampleProducts() {
        $sampleProducts = [
            [
                'name' => 'Premium Dark Chocolate',
                'code' => 'DC001',
                'description' => 'Rich 85% cocoa dark chocolate with intense flavor profile. Made with finest Belgian cocoa beans.',
                'image' => 'assets/images/products/dark-chocolate.jpg',
                'category' => 'Dark Chocolate',
                'weight' => null
            ],
            [
                'name' => 'Silky Milk Chocolate',
                'code' => 'MC001',
                'description' => 'Creamy milk chocolate made with finest Belgian cocoa and fresh cream.',
                'image' => 'assets/images/products/milk-chocolate.jpg',
                'category' => 'Milk Chocolate',
                'weight' => null
            ],
            [
                'name' => 'White Chocolate Dreams',
                'code' => 'WC001',
                'description' => 'Pure white chocolate with vanilla bean essence and smooth texture.',
                'image' => 'assets/images/products/white-chocolate.jpg',
                'category' => 'White Chocolate',
                'weight' => null
            ],
            [
                'name' => 'Artisan Chocolate Bar',
                'code' => 'BAR001',
                'description' => 'Hand-crafted chocolate bar with sea salt finish and premium cocoa.',
                'image' => 'assets/images/products/chocolate-bar.jpg',
                'category' => 'Bars',
                'weight' => '100g'
            ],
            [
                'name' => 'Dark Chocolate Bar Supreme',
                'code' => 'BAR002',
                'description' => 'Premium dark chocolate bar with 90% cocoa content for true connoisseurs.',
                'image' => 'assets/images/products/dark-bar.jpg',
                'category' => 'Bars',
                'weight' => '150g'
            ],
            [
                'name' => 'Luxury Gift Package',
                'code' => 'PKG001',
                'description' => 'Assorted chocolates in elegant gift packaging. Perfect for special occasions.',
                'image' => 'assets/images/products/gift-package.jpg',
                'category' => 'Packages',
                'weight' => '500g'
            ],
            [
                'name' => 'Executive Chocolate Collection',
                'code' => 'PKG002',
                'description' => 'Premium collection of our finest chocolates in a luxury wooden box.',
                'image' => 'assets/images/products/executive-package.jpg',
                'category' => 'Packages',
                'weight' => '750g'
            ],
            [
                'name' => 'Light Chocolate Delights',
                'code' => 'LC001',
                'description' => 'Low-calorie chocolate option without compromising taste and quality.',
                'image' => 'assets/images/products/light-chocolate.jpg',
                'category' => 'Light Chocolate',
                'weight' => null
            ],
            [
                'name' => 'Sugar-Free Milk Chocolate',
                'code' => 'LC002',
                'description' => 'Delicious milk chocolate made with natural sweeteners.',
                'image' => 'assets/images/products/sugar-free.jpg',
                'category' => 'Light Chocolate',
                'weight' => null
            ]
        ];

        $insertSQL = "INSERT INTO products (name, code, description, image, category, weight) 
                      VALUES (:name, :code, :description, :image, :category, :weight)";
        
        $stmt = $this->conn->prepare($insertSQL);
        
        foreach ($sampleProducts as $product) {
            $stmt->execute([
                ':name' => $product['name'],
                ':code' => $product['code'],
                ':description' => $product['description'],
                ':image' => $product['image'],
                ':category' => $product['category'],
                ':weight' => $product['weight']
            ]);
        }
    }

    public function testConnection() {
        try {
            $this->getConnection();
            if ($this->conn) {
                return [
                    'success' => true,
                    'message' => 'Database connection successful'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to establish database connection'
                ];
            }
        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection error: ' . $e->getMessage()
            ];
        }
    }
}

// Auto-initialize database when this file is included
if (!defined('DB_INIT_SKIP')) {
    try {
        $db = new Database();
        $db->createDatabaseAndTables();
    } catch(Exception $e) {
        // Silently handle for now, will be caught by individual API calls
    }
}
?>
