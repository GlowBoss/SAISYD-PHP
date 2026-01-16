<?php
// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['userID']) || !isset($_SESSION['role'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Verify session authenticity with fingerprint
$current_fingerprint = md5(
    $_SERVER['HTTP_USER_AGENT'] ?? '' . 
    $_SERVER['REMOTE_ADDR'] ?? ''
);

if (!isset($_SESSION['fingerprint'])) {
    $_SESSION['fingerprint'] = $current_fingerprint;
} else if ($_SESSION['fingerprint'] !== $current_fingerprint) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=security");
    exit();
}

//Session timeout (30 minutes of inactivity)
$timeout_duration = 1800;
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit();
    }
}

//Update last activity time
$_SESSION['last_activity'] = time();

//Create session ID periodically (every 30 minutes)
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Add security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>