<?php
include '../assets/connect.php';

// Columns for sorting
$allowedSort = ['inventoryID', 'ingredientName', 'quantity', 'lastUpdated', 'expirationDate'];

// Default sort
$sort = 'lastUpdated';
$order = 'DESC';

// Check GET parameters
if (isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort)) {
    $sort = $_GET['sort'];
}
if (isset($_GET['order']) && ($_GET['order'] === 'asc' || $_GET['order'] === 'desc')) {
    $order = strtoupper($_GET['order']);
}

// Query
$inventoryQuery = "SELECT i.inventoryID,
                          ing.ingredientName,
                          i.quantity,
                          i.unit,
                          i.lastUpdated,
                          i.expirationDate
                   FROM inventory i
                   LEFT JOIN ingredients ing 
                        ON i.ingredientID = ing.ingredientID
                   ORDER BY $sort $order";

$result = executeQuery($inventoryQuery);
$rows = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
}

// Helper to toggle order in links
function toggleOrder($currentOrder)
{
    return $currentOrder === 'ASC' ? 'desc' : 'asc';
}
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Management</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/inventory-management.css">
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css">

    <!-- Bootstrap Icons (latest version so cash-register works) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- WOW.js Animation -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" href="../assets/img/round_logo.png" type="image/png">
</head>

<body>
    <!-- Desktop Sidebar (visible on md+ screens) -->
    <div class="d-none d-md-block">
        <div class="desktop-sidebar p-4">
            <!-- Logo Section -->
            <div class="text-center mb-4">
                <img src="../assets/img/saisydLogo.png" class="admin-logo" alt="Saisyd Cafe Admin" />
            </div>

            <!-- MENU Section -->
            <div class="section-header">Menu</div>
            <div class="mb-3">
                <a href="index.php" class="admin-nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="notification.php" class="admin-nav-link">
                    <i class="bi bi-bell"></i>
                    <span>Notifications</span>
                </a>
                <a href="point-of-sales.php" class="admin-nav-link">
                    <i class="bi bi-shop-window"></i>
                    <span>Point of Sales</span>
                </a>
                <a href="inventory-management.php" class="admin-nav-link">
                    <i class="bi bi-boxes"></i>
                    <span>Inventory Management</span>
                </a>
                <a href="menu-management.php" class="admin-nav-link active">
                    <i class="bi bi-menu-button-wide"></i>
                    <span>Menu Management</span>
                </a>
            </div>

            <!-- FINANCIAL Section -->
            <div class="section-header">Financial</div>
            <div class="mb-3">
                <a href="sales-and-report.php" class="admin-nav-link">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Sales & Reports</span>
                </a>
            </div>

            <!-- TOOLS Section -->
            <div class="section-header">Tools</div>
            <div>
                <a href="#" class="admin-nav-link">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
                <a href="login.php" class="admin-nav-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Mobile Sidebar -->
    <div id="adminSidebar" class="admin-sidebar">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <img src="../assets/img/saisydLogo.png" class="admin-logo me-2" alt="Saisyd Cafe" />
            </div>
            <button id="closeSidebar">&times;</button>
        </div>

        <div id="sidebarNav">
            <!-- MENU Section -->
            <div class="section-header">Menu</div>
            <a href="index.php" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.1s">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="notification.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.15s">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
            </a>
            <a href="point-of-sales.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.2s">
                <i class="bi bi-shop-window"></i>
                <span>Point of Sales</span>
            </a>
            <a href="inventory-management.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.25s">
                <i class="bi bi-boxes"></i>
                <span>Inventory Management</span>
            </a>
            <a href="menu-management.php" class="admin-nav-link active wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.3s">
                <i class="bi bi-menu-button-wide"></i>
                <span>Menu Management</span>
            </a>

            <!-- FINANCIAL Section -->
            <div class="section-header">Financial</div>
            <a href="sales-and-report.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.35s">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Sales & Reports</span>
            </a>

            <!-- TOOLS Section -->
            <div class="section-header">Tools</div>
            <a href="#" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.4s">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
            <a href="login.php" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.45s">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="cardMain shadow-lg">

                <!-- Header Row  -->
                <div class="d-none d-md-block align-items-center py-4 px-lg-3 px-2">
                    <div class="subheading fw-bold m-1 d-flex align-items-center">
                        <span style="color: var(--text-color-dark);">Inventory Management</span>
                    </div>
                </div>

                <div class="row g-2 align-items-center mb-3 px-2 px-lg-3">
                    <!-- Title -->
                    <div class="col-12">
                        <h4 class="subheading fw-bold ms-1 mt-3 mt-md-0 mb-3 mb-md-0">Current Stock</h4>
                    </div>

                    <!-- Search bar -->
                    <div class="col-12 col-md">
                        <input type="text" class="form-control search" placeholder="Search" aria-label="search-bar"
                            id="search-inventory">
                    </div>

                    <!-- Buttons -->
                    <div class="col-12 col-md-auto d-flex flex-column flex-md-row gap-2">
                        <button class="btn categorybtn" type="button">
                            Search
                        </button>
                        <button class="btn categorybtn" type="button" data-bs-toggle="modal"
                            data-bs-target="#addItemModal">
                            <i class="bi bi-plus-circle"></i>
                            <span class="d-none d-sm-inline ms-2">Add</span>
                        </button>
                        <button class="btn btnExport" type="button" 
                        data-bs-toggle="modal" data-bs-target="#confirmModal">
                            Export
                        </button>

                    </div>
                </div>

                <!-- TABLE -->
                <div class="card tableCard m-5">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle text-nowrap">
                                <thead class="text-center custom-thead">
                                    <tr>
                                        <th scope="col">
                                            <a href="?sort=inventoryID&order=<?= ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc' ?>"
                                                class="filterStyle">
                                                Item Code</a>
                                        </th>
                                        <th scope="col">
                                            Item Name
                                        </th>
                                        <th scope="col">
                                            <a href="?sort=quantity&order=<?= ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc' ?>"
                                                class="filterStyle">
                                                Item Quantity</a>
                                        </th>
                                        <th scope="col">
                                            <a href="?sort=lastUpdated&order=<?= ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc' ?>"
                                                class="filterStyle">
                                                Date Purchased</a>
                                        </th>
                                        <th scope="col">
                                            <a href="?sort=expirationDate&order=<?= ($_GET['order'] ?? 'asc') === 'asc' ? 'desc' : 'asc' ?>"
                                                class="filterStyle">
                                                Expiration Date</a>

                                        </th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php if (!empty($rows)): ?>
                                        <?php foreach ($rows as $row): ?>
                                            <tr>
                                                <!-- Item Code from inventoryID -->
                                                <th scope="row">
                                                    <?= str_pad($row['inventoryID'], 3, "0", STR_PAD_LEFT) ?>
                                                </th>

                                                <!-- Item Name from ingredientName -->
                                                <td><?= $row['ingredientName'] ?? 'No Ingredient' ?></td>

                                                <!-- Item Quantity -->
                                                <td><?= $row['quantity'] ?> <strong><?= $row['unit'] ?></strong></td>

                                                <!-- Last Purchase (formatted date) -->
                                                <td><?= date("M d Y", strtotime($row['lastUpdated'])) ?></td>

                                                <!-- Expiration Date -->
                                                <td><?= date("M d Y", strtotime($row['expirationDate'])) ?></td>

                                                <!-- Actions -->
                                                <td>
                                                    <button class="btn btn-sm categorybtn"><i
                                                            class="bi bi-pencil-square"></i></button>
                                                    <button class="btn btn-sm btnExport"><i
                                                            class="bi bi-three-dots-vertical"></i></button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">No records found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../modal/inventory-management-modal.php'; ?>

    <script src="../assets/js/inventory-management.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script src="../assets/js/inventory-export.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>
    <script>
        document.getElementById("downloadInventoryBtn").addEventListener("click", function () {
            // Delay to let download start
            setTimeout(() => {
                const toast = new bootstrap.Toast(document.getElementById("inventoryToast"));
                toast.show();
            }, 1500);
        });
    </script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const downloadBtn = document.querySelector("#confirmModal .btnDownload");
    const toastEl = document.getElementById("inventoryToast");

    if (downloadBtn && toastEl) {
        downloadBtn.addEventListener("click", function () {
            // Bootstrap toast init
            const toast = new bootstrap.Toast(toastEl);

            // Wait a moment so download triggers first, then show toast
            setTimeout(() => {
                toast.show();
            }, 1000);
        });
    }
});
</script>


</body>

</html>