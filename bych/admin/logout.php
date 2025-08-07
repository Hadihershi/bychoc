<?php
/**
 * BeyChoc Admin Logout
 * Handles admin logout and session cleanup
 */

session_start();

require_once 'session_check.php';

// Destroy the session
destroySession();

// Redirect to login page
header('Location: login.php');
exit();
?>
