<?php
/**
 * BeyChoc Admin Session Check
 * Validates admin sessions and handles authentication
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';

/**
 * Check if admin is logged in and session is valid
 */
function checkAdminSession() {
    // Check if session variables exist
    if (!isset($_SESSION['admin_logged_in']) || 
        !isset($_SESSION['admin_session_id']) || 
        $_SESSION['admin_logged_in'] !== true) {
        redirectToLogin();
        return false;
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $session_id = $_SESSION['admin_session_id'];
        
        // Check if session exists and is valid
        $sql = "SELECT * FROM admin_sessions 
                WHERE session_id = ? 
                AND is_active = 1 
                AND expires_at > NOW()";
        $stmt = $db->prepare($sql);
        $stmt->execute([$session_id]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$session) {
            // Session not found or expired
            destroySession();
            redirectToLogin();
            return false;
        }
        
        // Update session expiry (extend by 24 hours)
        $new_expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $updateSql = "UPDATE admin_sessions SET expires_at = ? WHERE session_id = ?";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->execute([$new_expiry, $session_id]);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Session check error: " . $e->getMessage());
        destroySession();
        redirectToLogin();
        return false;
    }
}

/**
 * Destroy admin session
 */
function destroySession() {
    try {
        if (isset($_SESSION['admin_session_id'])) {
            $database = new Database();
            $db = $database->getConnection();
            
            // Mark session as inactive in database
            $sql = "UPDATE admin_sessions SET is_active = 0 WHERE session_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$_SESSION['admin_session_id']]);
        }
    } catch (Exception $e) {
        error_log("Session destruction error: " . $e->getMessage());
    }
    
    // Clear session variables
    $_SESSION = array();
    
    // Delete session cookie
    if (isset($_COOKIE['beychoc_admin_session'])) {
        setcookie('beychoc_admin_session', '', time() - 3600, '/bych/admin/');
    }
    
    // Destroy session
    session_destroy();
}

/**
 * Redirect to login page
 */
function redirectToLogin() {
    header('Location: login.php');
    exit();
}

/**
 * Get current admin info
 */
function getCurrentAdmin() {
    if (isset($_SESSION['admin_username'])) {
        return [
            'username' => $_SESSION['admin_username'],
            'login_time' => $_SESSION['login_time'] ?? null,
            'session_id' => $_SESSION['admin_session_id'] ?? null
        ];
    }
    return null;
}

/**
 * Check session and redirect if invalid
 */
function requireAdminLogin() {
    if (!checkAdminSession()) {
        exit();
    }
}

// Auto-check session when file is included
requireAdminLogin();
?>
