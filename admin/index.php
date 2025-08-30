<?php
session_start();

// Check if user is logged in and is an admin 
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Saisyd Admin Dashboard</title>
  <link rel="icon" href="../assets/img/round_logo.png" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/admin_home.css">

</head>

<body>
  <div class="dashboard-container text-white text-center">
    <div class="container">
      <header class="d-flex justify-content-between align-items-center p-3 flex-wrap">
        <div class="logo">
          <img src="../assets/img/saisydLogo.png" alt="Saisyd Logo" height="70" />
        </div>

        <div class="d-flex gap-3 align-items-center flex-wrap justify-content-end">
          <!-- 🔔 Bell Icon with red dot -->
          <a href="#" class="btn bg-transparent icon-btn notification-bell" data-bs-toggle="modal" data-bs-target="#stockModal">
            <i class="bi bi-bell"></i>
            <span class="notification-badge"></span>
          </a>
          <a href="../index.html" class="btn custom-visit">
            <i class="bi bi-globe"></i> <span class="d-none d-md-inline">Visit Site</span>
          </a>
          <a class="btn custom-logout" href="login.php">
            <i class="bi bi-power"></i> <span class="d-none d-md-inline">Log Out</span>
          </a>
        </div>
      </header>

      <main class="text-center mt-4">
        <h1 class="dashboard-title">ADMIN DASHBOARD</h1>
        <p class="dashboard-subtitle">Unlock your potential. Let earnings follow.</p>

        <div class="container mt-5 mb-5">
          <div class="row g-4 justify-content-center">
            <div class="col-12 col-sm-6 col-md-4">
              <a href="point-of-sales.html" class="dashboard-box text-decoration-none">Point of Sale
                System</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <a href="inventory-management.php" class="dashboard-box text-decoration-none">Inventory
                Management</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <a href="sales-and-report.php" class="dashboard-box text-decoration-none">Sales and
                Report</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <a href="menu-management.php" class="dashboard-box text-decoration-none">Menu
                Management</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <a href="user_role.html" class="dashboard-box text-decoration-none">User Role</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <a href="setting.php" class="dashboard-box text-decoration-none">Settings</a>
            </div>
          </div>
        </div>
      </main>

      <footer class="text-muted mt-5 p-3 small">
        <p>Saisyd Cafe 2025</p>
      </footer>
    </div>
  </div>

  <!-- Low Stock Alert Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content stock-modal">
      
      <!-- Header -->
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="stockModalLabel">Low Stock Alert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <!-- Body -->
      <div class="modal-body text-center">
        <p class="mb-3">The following items are running low on stock:</p>
        
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>🥤 Sugar</span>
            <span class="badge bg-danger rounded-pill">5</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>🥛 Milk</span>
            <span class="badge bg-danger rounded-pill">2</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span>🧈 Butter</span>
            <span class="badge bg-danger rounded-pill">8</span>
          </li>
        </ul>
      </div>
      
      <!-- Footer -->
      <div class="modal-footer border-0 d-flex justify-content-center">
        <a href="inventory-management.php" class="btn custom-visit px-4 py-2 rounded-pill">
          Go to Inventory
        </a>
        <button type="button" class="btn custom-logout px-4 py-2 rounded-pill" data-bs-dismiss="modal">
          Dismiss
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var stockModal = new bootstrap.Modal(document.getElementById('stockModal'));
    stockModal.show();
  });
</script>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
