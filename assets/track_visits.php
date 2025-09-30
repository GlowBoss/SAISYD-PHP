<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Make PHP use Philippine time
date_default_timezone_set('Asia/Manila');

// Session timeout (30 minutes)
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    unset($_SESSION['logged_pages']);
}
$_SESSION['last_activity'] = time();

// Current page, PH timestamp, and IP
$pageVisit = basename($_SERVER['PHP_SELF']);
$visitDate = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d H:i:s');
$ipAddress = $_SERVER['REMOTE_ADDR']; // get visitor IP

if ($ipAddress === '::1') {
    $ipAddress = '127.0.0.1';
}

// Hash the IP for privacy
$hashedIp = hash('sha256', $ipAddress);

// Ensure session array exists
if (!isset($_SESSION['logged_pages'])) {
    $_SESSION['logged_pages'] = [];
}

// Log once per session per page
if (!in_array($pageVisit, $_SESSION['logged_pages'])) {
    $query = "INSERT INTO visits (visitDate, pageVisit, ipAddress) VALUES (?, ?, ?)";
    $insertVisit = $conn->prepare($query);

    if (!$insertVisit) {
        error_log("Prepare failed: " . $conn->error);
    } else {
        $insertVisit->bind_param("sss", $visitDate, $pageVisit, $hashedIp);
        if (!$insertVisit->execute()) {
            error_log("Execute failed: " . $insertVisit->error);
        }
        $insertVisit->close();
        $_SESSION['logged_pages'][] = $pageVisit;
    }
}

?>