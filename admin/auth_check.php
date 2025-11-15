<?php
// auth_check.php - MAXIMUM SECURITY VERSION
// Include this at the top of EVERY admin page

// 1. Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    // Secure session configuration
    ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access
    ini_set('session.cookie_secure', 0);    // Set to 1 if using HTTPS
    ini_set('session.use_only_cookies', 1); // Only use cookies, not URL
    ini_set('session.cookie_samesite', 'Strict'); // CSRF protection
    
    session_start();
}

// 2. Check if user is logged in
if (!isset($_SESSION['userID']) || !isset($_SESSION['role'])) {
    // Not logged in, destroy any partial session and redirect
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// 3. Verify session authenticity with fingerprint
$current_fingerprint = md5(
    $_SERVER['HTTP_USER_AGENT'] ?? '' . 
    $_SERVER['REMOTE_ADDR'] ?? ''
);

if (!isset($_SESSION['fingerprint'])) {
    // First time, set fingerprint
    $_SESSION['fingerprint'] = $current_fingerprint;
} else if ($_SESSION['fingerprint'] !== $current_fingerprint) {
    // Session hijacking attempt detected
    session_unset();
    session_destroy();
    header("Location: login.php?error=security");
    exit();
}

// 4. Session timeout (30 minutes of inactivity)
$timeout_duration = 1800; // 30 minutes in seconds

if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
        // Session expired
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=1");
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// 5. Regenerate session ID periodically (every 30 minutes)
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// 6. Add security headers
header("X-Frame-Options: DENY"); // Prevent clickjacking
header("X-Content-Type-Options: nosniff"); // Prevent MIME sniffing
header("X-XSS-Protection: 1; mode=block"); // XSS protection
header("Referrer-Policy: strict-origin-when-cross-origin");

// Session is valid - continue to page
?>