<?php
include('auth_check.php');
include '../assets/connect.php';

// Check if user is logged in and is an admin 
if (!isset($_SESSION['userID']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: logout.php");
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
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="../assets/css/admin_home.css">
</head>

<body>
  <div class="admin-wrapper">
    <!-- Header Section -->
    <header class="admin-header">
      <div class="container-fluid">
        <div class="header-content">
          <!-- Logo Section -->
          <div class="logo-section">
            <div style="
                width:50px;
                height:50px;
                background-color:var(--text-color-dark); 
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
            <div class="logo-text d-none d-md-block">
              <h2 class="brand-name">SAISYD CAFE</h2>
              <p class="brand-subtitle">Admin Portal</p>
            </div>
          </div>

          <!-- Actions Section -->
          <div class="header-actions">
            <!-- Notification Bell -->
            <button class="notification-btn" data-bs-toggle="modal" data-bs-target="#stockModal">
              <i class="bi bi-bell"></i>
              <span id="lowStockBadge" class="notification-badge d-none"></span>
            </button>

            <!-- Visit Site Button -->
            <a href="../index.php" class="action-btn visit-btn">
              <i class="bi bi-globe"></i>
              <span class="btn-text d-none d-lg-inline">Visit Site</span>
            </a>

            <!-- Logout Button -->
            <a class="action-btn logout-btn" href="logout.php">
              <i class="bi bi-power"></i>
              <span class="btn-text d-none d-lg-inline">Log Out</span>
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Dashboard Content -->
    <main class="dashboard-main">
      <div class="container-fluid h-100">
        <!-- Welcome Section -->
        <section class="welcome-section">
          <div class="welcome-content">
            <h1 class="dashboard-title">ADMIN DASHBOARD</h1>
            <p class="dashboard-subtitle">Unlock your potential. Let earnings follow.</p>
          </div>
        </section>

        <!-- Dashboard Grid -->
        <section class="dashboard-grid">
          <div class="row g-3 h-100">
           
           <!-- Order Management -->
            <div class="col-12 col-sm-6 col-lg-4">
              <a href="orders.php" class="dashboard-card">
                <div class="card-icon">
                  <i class="bi bi-people"></i>
                </div>
                <div class="card-content">
                  <h3 class="card-title">Order Management</h3>
                  <p class="card-description">View and track customer orders</p>
                </div>
                <div class="card-arrow">
                  <i class="bi bi-arrow-right"></i>
                </div>
              </a>
            </div>
          
          <!-- Point of Sale -->
            <div class="col-12 col-sm-6 col-lg-4">
              <a href="point-of-sales.php" class="dashboard-card">
                <div class="card-icon">
                  <i class="bi bi-cash-coin"></i>
                </div>
                <div class="card-content">
                  <h3 class="card-title">Point of Sale</h3>
                  <p class="card-description">Process transactions & orders</p>
                </div>
                <div class="card-arrow">
                  <i class="bi bi-arrow-right"></i>
                </div>
              </a>
            </div>

            <!-- Inventory Management -->
            <div class="col-12 col-sm-6 col-lg-4">
              <a href="inventory-management.php" class="dashboard-card">
                <div class="card-icon">
                  <i class="bi bi-boxes"></i>
                </div>
                <div class="card-content">
                  <h3 class="card-title">Inventory Management</h3>
                  <p class="card-description">Manage stock & supplies</p>
                </div>
                <div class="card-arrow">
                  <i class="bi bi-arrow-right"></i>
                </div>
              </a>
            </div>

             <!-- Menu Management -->
            <div class="col-12 col-sm-6 col-lg-4">
              <a href="menu-management.php" class="dashboard-card">
                <div class="card-icon">
                  <i class="bi bi-menu-button-wide"></i>
                </div>
                <div class="card-content">
                  <h3 class="card-title">Menu Management</h3>
                  <p class="card-description">Update menu items</p>
                </div>
                <div class="card-arrow">
                  <i class="bi bi-arrow-right"></i>
                </div>
              </a>
            </div>

            <!-- Sales & Reports -->
            <div class="col-12 col-sm-6 col-lg-4">
              <a href="sales-and-report.php" class="dashboard-card">
                <div class="card-icon">
                  <i class="bi bi-graph-up"></i>
                </div>
                <div class="card-content">
                  <h3 class="card-title">Sales & Reports</h3>
                  <p class="card-description">Analytics & insights</p>
                </div>
                <div class="card-arrow">
                  <i class="bi bi-arrow-right"></i>
                </div>
              </a>
            </div>

            <!-- Settings -->
            <div class="col-12 col-sm-6 col-lg-4">
              <a href="settings.php" class="dashboard-card">
                <div class="card-icon">
                  <i class="bi bi-gear"></i>
                </div>
                <div class="card-content">
                  <h3 class="card-title">Settings</h3>
                  <p class="card-description">System configuration</p>
                </div>
                <div class="card-arrow">
                  <i class="bi bi-arrow-right"></i>
                </div>
              </a>
            </div>
          </div>
        </section>
      </div>
    </main>

    <!-- Footer -->
    <footer class="admin-footer">
      <div class="container-fluid">
        <p class="footer-text">© 2025 Saisyd Cafe. All rights reserved.</p>
      </div>
    </footer>
  </div>

  <!-- Stock Modal Container -->
  <div id="stockModalContainer"></div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Stock Monitoring Script -->
  <script>
    let stockModalInstance = null;
    let modalDismissed = false;
    let lastLowStockSignature = "";

    function checkLowStock() {
      fetch("../modal/stock-modal.php")
        .then(res => res.text())
        .then(html => {
          let badge = document.getElementById("lowStockBadge");

          if (html.trim() !== "") {
            let parsedDoc = new DOMParser().parseFromString(html, "text/html");
            let modalEl = parsedDoc.querySelector("#stockModal");
            let newBody = parsedDoc.querySelector(".modal-body");
            let newSignature = newBody ? newBody.innerText.trim() : "";

            // Get low stock count from modal attribute
            let count = modalEl ? parseInt(modalEl.dataset.lowstockCount) || 0 : 0;

            // Update bell badge
            if (count > 0) {
              badge.textContent = count;
              badge.classList.remove("d-none");
            } else {
              badge.classList.add("d-none");
            }

            // If modal not created yet, inject it
            if (!document.getElementById("stockModal")) {
              document.getElementById("stockModalContainer").innerHTML = html;
              let modalElNew = document.getElementById("stockModal");
              stockModalInstance = new bootstrap.Modal(modalElNew);

              modalElNew.addEventListener("hidden.bs.modal", () => {
                modalDismissed = true;
              });
            } else {
              // Update modal body if it already exists
              document.querySelector("#stockModal .modal-body").innerHTML = newBody.innerHTML;
            }

            // Reset dismissed state if new low-stock list detected
            if (newSignature !== lastLowStockSignature) {
              modalDismissed = false;
            }

            // Show modal if not dismissed
            if (!modalDismissed && !sessionStorage.getItem('lowStockShown')) {
              stockModalInstance.show();
              sessionStorage.setItem('lowStockShown', 'true');
            }

            // Save latest signature
            lastLowStockSignature = newSignature;
          } else {
            // No low stock
            badge.classList.add("d-none");
            lastLowStockSignature = "";
          }
        })
        .catch(err => console.error("Error checking stock:", err));
    }

    // Run immediately on page load
    document.addEventListener("DOMContentLoaded", checkLowStock);

    // Run every 3 minutes
    setInterval(checkLowStock, 300000);
  </script>
</body>

</html>