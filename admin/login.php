<?php
session_start();
include('../assets/connect.php');

$error = ""; // store error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
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
            $_SESSION['userID'] = $userID;
            $_SESSION['role']   = $role;
            header("Location: ../admin/index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }

    $stmt->close();
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
  <link rel="stylesheet" href="../assets/css/admin_login.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
</head>

<body class="loading">

  <!-- Loading Screen -->
  <div id="loading-screen">
    <div class="loading-logo">
      <div class="coffee-cup-container">
        <div class="steam">
          <div class="steam-line"></div>
          <div class="steam-line"></div>
          <div class="steam-line"></div>
        </div>
        <div class="coffee-cup">
          <div class="cup">
            <div class="cup-handle"></div>
          </div>
          <div class="saucer"></div>
        </div>
      </div>

      <div class="loading-text">
        <div class="cafe-name">SAISYD CAFÉ</div>
        <div class="tagline">THE HIDDEN FARM</div>
      </div>
    </div>

    <div class="loading-progress">
      <div class="progress-bar-container">
        <div class="progress-bar" id="progress-bar"></div>
      </div>
      <div class="loading-percentage" id="loading-percentage">0%</div>
    </div>

    <div class="loading-dots">
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
    </div>

    <div class="loading-message">
      Brewing the perfect experience...
    </div>
  </div>

  <!-- Login Content -->
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-8">
        <div class="login-card d-flex flex-column justify-content-center align-items-center flex-md-row">
          <!-- Right side: Logo -->
          <div class="login-logo col-md-6 order-md-1">
            <img src="../assets/img/round_logo.png" alt="Saisyd Café Logo">
          </div>

          <!-- Left side: Form -->
          <div class="login-left col-md-6 order-md-0">
            <h2 class="mb-3 text-center">Log in</h2>
            <p class="text-muted text-center">Username | Email</p>
            <form action="" method="post">
              <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
              </div>
              <p class="text-muted text-center">Password</p>
              <div class="mb-2">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
              <div class="form-text mb-3 text-center">
                Forgot Your <a href="#">Password?</a>
              </div>
              <!-- Error message -->
              <div id="errorMsg" class="text-danger text-center mb-3"
                   style="<?php echo empty($error) ? 'display:none;' : ''; ?>">
                <?php echo htmlspecialchars($error); ?>
              </div>
              <div class="text-center">
                <button type="submit" name="btnLogin" class="btn btn-login px-4">SIGN IN</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      let hasLoaded = localStorage.getItem("loginLoaded");

      if (!hasLoaded) {
        document.body.classList.add("loading"); // show loader
        localStorage.setItem("loginLoaded", "true"); // mark as seen
      } else {
        document.getElementById("loading-screen").style.display = "none"; // skip loader
      }
    });
  </script>

  <!-- JS: Remove Loader After Simulated Load -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      let progress = 0;
      const progressBar = document.getElementById("progress-bar");
      const percentageText = document.getElementById("loading-percentage");

      const interval = setInterval(() => {
        if (progress >= 100) {
          clearInterval(interval);
          document.body.classList.remove("loading");
          document.getElementById("loading-screen").style.display = "none";
        } else {
          progress += 1;
          progressBar.style.width = progress + "%";
          percentageText.textContent = progress + "%";
        }
      }, 20);
    });
  </script>
  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
