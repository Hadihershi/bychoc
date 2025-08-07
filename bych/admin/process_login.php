<?php
/**
 * BeyChoc Admin Login Processing
 * Handles admin login authentication and session management
 */

session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

// Admin credentials (as specified in requirements)
define('ADMIN_USERNAME', 'Lana Moghnieh');
define('ADMIN_PASSWORD', '123454321');

// Get form data
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Validate input
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Please enter both username and password.';
    header('Location: login.php');
    exit();
}

// Validate credentials
if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
    // Successful login
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Generate session ID
        $session_id = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Store session in database
        $sql = "INSERT INTO admin_sessions (session_id, admin_username, expires_at) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$session_id, $username, $expires_at]);
        
        // Set session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_session_id'] = $session_id;
        $_SESSION['login_time'] = time();
        
        // Set secure cookie
        setcookie('beychoc_admin_session', $session_id, [
            'expires' => time() + (24 * 60 * 60), // 24 hours
            'path' => '/bych/admin/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        // Clean up old sessions
        cleanupExpiredSessions($db);
        
        // Redirect to dashboard
        header('Location: dashboard.php');
        exit();
        
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['login_error'] = 'An error occurred during login. Please try again.';
        header('Location: login.php');
        exit();
    }
} else {
    // Invalid credentials
    $_SESSION['login_error'] = 'Invalid username or password.';
    
    // Log failed login attempt
    error_log("Failed login attempt: username='$username' from IP=" . $_SERVER['REMOTE_ADDR']);
    
    header('Location: login.php');
    exit();
}

/**
 * Clean up expired admin sessions
 */
function cleanupExpiredSessions($db) {
    try {
        $sql = "DELETE FROM admin_sessions WHERE expires_at < NOW() OR is_active = 0";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Session cleanup error: " . $e->getMessage());
    }
}
?>
