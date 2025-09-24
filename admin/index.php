<?php
include '../assets/connect.php';
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
          <!-- Bell Icon with red dot -->
          <a href="#" class="btn bg-transparent icon-btn position-relative notification-bell" data-bs-toggle="modal"
            data-bs-target="#stockModal">
            <i class="bi bi-bell fs-3"></i>
            <!-- Badge -->
            <span id="lowStockBadge"
              class="position-absolute start-60 translate-middle badge rounded-pill d-none fs-6 px-2 py-1"
              style="top: 20%; background-color: var(--btn-hover1);">
            </span>
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
              <a href="point-of-sales.php" class="dashboard-box text-decoration-none">Point of Sale
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
              <a href="user_role.html" class="dashboard-box text-decoration-none">User  Role</a>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
              <a href="settings.php" class="dashboard-box text-decoration-none">Settings</a>
            </div>
          </div>
        </div>
      </main>

      <footer class="text-muted mt-5 p-3 small">
        <p>Saisyd Cafe 2025</p>
      </footer>
    </div>
  </div>

  <?php include '../modal/low_stock_modal.php'; ?>


  <!-- Low Stock Alert Modal -->
  <div id="stockModalContainer"></div>

  <script>
  let stockModalInstance = null;
  let modalDismissed = false;
  let lastLowStockSignature = ""; // track last stock list

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

          // 🔴 Get low stock count from modal attribute
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
            document.querySelector("#stockModal .modal-body").innerHTML =
              newBody.innerHTML;
          }

          // Reset dismissed state if new low-stock list detected
          if (newSignature !== lastLowStockSignature) {
            modalDismissed = false;
          }

          // Show modal if not dismissed
          if (!modalDismissed) {
            stockModalInstance.show();
          }

          // Save latest signature
          lastLowStockSignature = newSignature;

        } else {
          // No low stock → show fallback message instead of removing modal
          badge.classList.add("d-none");
          lastLowStockSignature = "";

        }
      })
      .catch(err => console.error("Error checking stock:", err));
  }

  // Run immediately on page load
  document.addEventListener("DOMContentLoaded", checkLowStock);

  // Run every 5 seconds
  setInterval(checkLowStock, 5000);
</script>



  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      var stockModal = new bootstrap.Modal(document.getElementById('stockModal'));
      stockModal.show();
    });
  </script>
</body>

</html>