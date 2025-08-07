<?php
/**
 * Fix Missing admin_sessions Table
 * Creates the missing admin_sessions table for BeyChoc
 */

echo "<h2>🔧 Fixing Missing admin_sessions Table</h2>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; } .success { color: green; } .error { color: red; } .info { color: blue; }</style>";

require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Cannot connect to database");
    }
    
    echo "<p class='success'>✅ Connected to database successfully</p>";
    
    // Check existing tables
    echo "<h3>📋 Checking Existing Tables...</h3>";
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "<p class='info'>📄 Found table: <strong>$table</strong></p>";
    }
    
    // Create admin_sessions table if it doesn't exist
    if (!in_array('admin_sessions', $tables)) {
        echo "<h3>🛠️ Creating Missing admin_sessions Table...</h3>";
        
        $createSessionsTable = "
            CREATE TABLE admin_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                session_id VARCHAR(255) UNIQUE NOT NULL COMMENT 'Unique session identifier',
                admin_username VARCHAR(255) NOT NULL COMMENT 'Admin username',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Session creation time',
                expires_at DATETIME NOT NULL COMMENT 'Session expiry time',
                is_active BOOLEAN DEFAULT TRUE COMMENT 'Session status'
            )
        ";
        
        $db->exec($createSessionsTable);
        echo "<p class='success'>✅ admin_sessions table created successfully!</p>";
    } else {
        echo "<p class='info'>ℹ️ admin_sessions table already exists</p>";
    }
    
    // Create products table if it doesn't exist
    if (!in_array('products', $tables)) {
        echo "<h3>🛠️ Creating Missing products Table...</h3>";
        
        $createProductsTable = "
            CREATE TABLE products (
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
        
        $db->exec($createProductsTable);
        echo "<p class='success'>✅ products table created successfully!</p>";
        
        // Insert sample products
        echo "<p class='info'>📦 Adding sample products...</p>";
        $database->insertSampleProducts();
        echo "<p class='success'>✅ Sample products added!</p>";
    } else {
        // Check if products table has data
        $countStmt = $db->query("SELECT COUNT(*) as count FROM products");
        $productCount = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($productCount == 0) {
            echo "<p class='info'>📦 Adding sample products to empty products table...</p>";
            $database->insertSampleProducts();
            echo "<p class='success'>✅ Sample products added!</p>";
        } else {
            echo "<p class='info'>ℹ️ products table has $productCount products</p>";
        }
    }
    
    // Final verification
    echo "<h3>🎯 Final Verification...</h3>";
    $stmt = $db->query("SHOW TABLES");
    $finalTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $requiredTables = ['products', 'admin_sessions'];
    $allTablesExist = true;
    
    foreach ($requiredTables as $table) {
        if (in_array($table, $finalTables)) {
            echo "<p class='success'>✅ $table table exists and ready</p>";
        } else {
            echo "<p class='error'>❌ $table table is still missing</p>";
            $allTablesExist = false;
        }
    }
    
    if ($allTablesExist) {
        echo "<h3>🎉 Success!</h3>";
        echo "<p class='success'>All required tables have been created successfully!</p>";
        echo "<p><strong>You can now try logging in again:</strong></p>";
        echo "<p><a href='admin/login.php' style='background: #4C3D19; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Login</a></p>";
        
        echo "<h4>Admin Credentials:</h4>";
        echo "<ul>";
        echo "<li><strong>Username:</strong> Lana Moghnieh</li>";
        echo "<li><strong>Password:</strong> 123454321</li>";
        echo "</ul>";
    } else {
        echo "<h3>❌ Some Issues Remain</h3>";
        echo "<p class='error'>Please check the errors above and try again.</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Please ensure MySQL is running on port 3305 and try again.</p>";
}

echo "<p style='margin-top: 30px;'><a href='test_db_connection.php'>← Back to Database Test</a> | <a href='index.html'>Go to Main Website</a></p>";
?>
