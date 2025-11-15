<?php
// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');


// Check if user is already logged in, redirect to dashboard
if (isset($_SESSION['userID']) && isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

include('../assets/connect.php');

$error = "";
$show_timeout = isset($_GET['timeout']);
$show_logout = isset($_GET['logout']);
$show_security = isset($_GET['error']) && $_GET['error'] === 'security';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Rate limiting check (prevent brute force)
  if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
  }
  
  // Reset attempts after 15 minutes
  if (time() - $_SESSION['last_attempt_time'] > 900) {
    $_SESSION['login_attempts'] = 0;
  }
  
  // Block after 5 failed attempts
  if ($_SESSION['login_attempts'] >= 5) {
    $time_left = 900 - (time() - $_SESSION['last_attempt_time']);
    $error = "Too many failed attempts. Please try again in " . ceil($time_left / 60) . " minutes.";
  } else {
    $username = trim($_POST['username']);
    $enteredPassword = $_POST['password'];

    // Get user by username
    $stmt = $conn->prepare("SELECT userID, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $stmt->bind_result($userID, $storedPassword, $role);
      $stmt->fetch();

      // Check Hashed Password
      if (password_verify($enteredPassword, $storedPassword)) {
        // Clear old session data completely
        session_unset();
        session_destroy();
        
        // Start fresh session
        session_start();
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['userID'] = $userID;
        $_SESSION['role'] = $role;
        $_SESSION['last_activity'] = time();
        $_SESSION['created'] = time();
        $_SESSION['login_attempts'] = 0;
        
        // Create session fingerprint
        $_SESSION['fingerprint'] = md5(
          $_SERVER['HTTP_USER_AGENT'] ?? '' . 
          $_SERVER['REMOTE_ADDR'] ?? ''
        );
        
        header("Location: index.php");
        exit();
      } else {
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
        $error = "Invalid username or password.";
      }
    } else {
      $_SESSION['login_attempts']++;
      $_SESSION['last_attempt_time'] = time();
      $error = "Invalid username or password.";
    }

    $stmt->close();
  }
}
$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Saisyd Café Login</title>
  <link rel="icon" href="../assets/img/round_logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="../assets/css/admin_login.css">
</head>

<body>

  <!-- Login Content -->
  <div class="login-wrapper">
    <!-- Background Message -->
    <div class="background-message">
      <h1 class="bg-title">SAISYD CAFÉ</h1>
      <p class="bg-subtitle">THE HIDDEN FARM</p>
      <div class="bg-decoration">
        <div class="decoration-line"></div>
        <i class="bi bi-cup-hot-fill decoration-icon"></i>
        <div class="decoration-line"></div>
      </div>
      <p class="bg-tagline">Unlock your potential. Let earnings follow.</p>
    </div>

    <!-- Login Card Container -->
    <div class="login-container">
      <div class="login-card">
        <!-- Logo Section -->
        <div class="login-header">
          <div class="logo-container">
            <div style="
                width:70px;
                height:70px;
                background-color:var(--primary-color); 
                -webkit-mask-image:url('../assets/img/saisydLogo.png');
                -webkit-mask-repeat:no-repeat;
                -webkit-mask-size:contain;
                -webkit-mask-position:center;
                mask-image:url('../assets/img/saisydLogo.png');
                mask-repeat:no-repeat;
                mask-size:contain;
                mask-position:center;
                display:inline-block;
              " role="img" aria-label="Saisyd Logo">
            </div>
          </div>
          <h1 class="login-title">ADMIN PORTAL</h1>
          <p class="login-subtitle">Saisyd Café Management</p>
        </div>

        <!-- Form Section -->
        <div class="login-form">
          <h2 class="form-title">Welcome Back</h2>
          <p class="form-subtitle">Sign in to access your dashboard</p>

          <!-- Security alert -->
          <?php if ($show_security): ?>
            <div class="alert alert-danger">
              <i class="bi bi-shield-exclamation"></i>
              Security alert: Suspicious activity detected. Please login again.
            </div>
          <?php endif; ?>

          <!-- Timeout message -->
          <?php if ($show_timeout): ?>
            <div class="alert alert-warning">
              <i class="bi bi-clock-history"></i>
              Your session has expired. Please login again.
            </div>
          <?php endif; ?>

          <!-- Logout message -->
          <?php if ($show_logout): ?>
            <div class="alert alert-success">
              <i class="bi bi-check-circle"></i>
              You have been successfully logged out.
            </div>
          <?php endif; ?>

          <form action="" method="post">
            <div class="form-group">
              <label class="form-label">Username</label>
              <div class="input-wrapper">
                <i class="bi bi-person input-icon"></i>
                <input type="text" class="form-control form-input" name="username" placeholder="Enter your username"
                  required>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Password</label>
              <div class="input-wrapper position-relative">
                <i class="bi bi-lock input-icon"></i>
                <input type="password" class="form-control form-input" id="password" name="password"
                  placeholder="Enter your password" required>
                <i class="bi bi-eye-slash toggle-password" id="togglePassword" style="
         position:absolute;
         right:15px;
         top:50%;
         transform:translateY(-50%);
         cursor:pointer;
         color:var(--text-muted-color, #888);
         font-size:1.1rem;
         transition:color 0.2s ease;
       "></i>
              </div>
            </div>


            <!-- Error message -->
            <?php if (!empty($error)): ?>
              <div class="error-message">
                <i class="bi bi-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
              </div>
            <?php endif; ?>

            <button type="submit" name="btnLogin" class="btn-login mt-3 mb-0">
              <span>SIGN IN</span>
              <i class="bi bi-arrow-right"></i>
            </button>
          </form>
        </div>

      </div>
    </div>
  </div>


  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Check if loading screen exists before trying to hide it
      const loadingScreen = document.getElementById("loading-screen");
      const progressBar = document.getElementById("progress-bar");
      const percentageText = document.getElementById("loading-percentage");
      
      if (loadingScreen && progressBar && percentageText) {
        let progress = 0;
        const interval = setInterval(() => {
          if (progress >= 100) {
            clearInterval(interval);
            document.body.classList.remove("loading");
            loadingScreen.style.display = "none";
          } else {
            progress += 1;
            progressBar.style.width = progress + "%";
            percentageText.textContent = progress + "%";
          }
        }, 20);
      }
    });

    //Password Toggle
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#password");

    togglePassword.addEventListener("click", function () {
      const type = password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);

      this.classList.toggle("bi-eye");
      this.classList.toggle("bi-eye-slash");

      // Optional hover effect
      this.style.color = type === "text" ? "var(--primary-color, #7b4b2a)" : "#888";
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>