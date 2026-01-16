<?php
// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

session_start();

// Completely destroy all session data
$_SESSION = array();

// Delete the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => false, 
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

// Destroy the session
session_destroy();

// Redirect to login page with logout message
header("Location: login.php?logout=1");
exit();
?>