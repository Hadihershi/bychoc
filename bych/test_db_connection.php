<?php
/**
 * Database Connection Test for BeyChoc
 * Test if the database connection works with port 3305
 */

echo "<h2>BeyChoc Database Connection Test</h2>";
echo "<style>body { font-family: Arial, sans-serif; margin: 20px; } .success { color: green; } .error { color: red; } .warning { color: orange; }</style>";

require_once 'config/database.php';

echo "<h3>Testing Database Connection...</h3>";

try {
    $database = new Database();
    echo "<p class='success'>✅ Database class loaded successfully</p>";
    
    // Test basic connection
    $db = $database->getConnection();
    
    if ($db) {
        echo "<p class='success'>✅ Database connection successful!</p>";
        echo "<p>Connected to: <strong>localhost:3305</strong></p>";
        
        // Test if database exists
        try {
            $db->exec("USE beychoc_db");
            echo "<p class='success'>✅ Database 'beychoc_db' exists and accessible</p>";
            
            // Check if tables exist
            $tables = ['products', 'admin_sessions'];
            foreach ($tables as $table) {
                $stmt = $db->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    echo "<p class='success'>✅ Table '$table' exists</p>";
                    
                    // Count records
                    $countStmt = $db->query("SELECT COUNT(*) as count FROM $table");
                    $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
                    echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;Records in $table: <strong>$count</strong></p>";
                } else {
                    echo "<p class='error'>❌ Table '$table' does not exist</p>";
                }
            }
            
        } catch (Exception $e) {
            echo "<p class='warning'>⚠️ Database exists but not accessible: " . $e->getMessage() . "</p>";
            echo "<p>Attempting to create database...</p>";
            
            // Try to create database and tables
            $success = $database->createDatabaseAndTables();
            if ($success) {
                echo "<p class='success'>✅ Database and tables created successfully!</p>";
            } else {
                echo "<p class='error'>❌ Failed to create database and tables</p>";
            }
        }
        
    } else {
        echo "<p class='error'>❌ Database connection failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Database connection error: " . $e->getMessage() . "</p>";
    echo "<p class='warning'>Possible issues:</p>";
    echo "<ul>";
    echo "<li>MySQL is not running on port 3305</li>";
    echo "<li>Port 3305 is not configured in XAMPP</li>";
    echo "<li>Database credentials are incorrect</li>";
    echo "<li>PDO MySQL extension is not installed</li>";
    echo "</ul>";
}

echo "<h3>Testing Admin Login Credentials...</h3>";
define('ADMIN_USERNAME', 'Lana Moghnieh');
define('ADMIN_PASSWORD', '123454321');

echo "<p>Admin Username: <strong>" . ADMIN_USERNAME . "</strong></p>";
echo "<p>Admin Password: <strong>" . ADMIN_PASSWORD . "</strong></p>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Make sure MySQL is running on port 3305 in XAMPP</li>";
echo "<li>If database connection fails, check your MySQL port configuration</li>";
echo "<li>If tables don't exist, visit <a href='index.html'>index.html</a> first to auto-create them</li>";
echo "<li>Try the admin login again at <a href='admin/login.php'>admin/login.php</a></li>";
echo "</ol>";

echo "<p><a href='admin/login.php'>← Back to Admin Login</a></p>";
?>
