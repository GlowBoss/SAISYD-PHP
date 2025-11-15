<?php
include('auth_check.php');
include '../assets/connect.php';


// Check if user is logged in and is an admin 
if (!isset($_SESSION['userID']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php");
    exit();
}

// Handle form submissions
$message = '';
$messageType = '';

// Helper function to format quantity (removes unnecessary decimals)
function formatQuantity($quantity)
{
    $quantity = round($quantity, 2); // Round to 2 decimal places
    // Remove unnecessary trailing zeros and decimal point
    return rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.');
}

// CREATE - Add new inventory item (Updated to handle new ingredients)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
    $ingredientID = mysqli_real_escape_string($conn, $_POST['ingredientID']);
    $ingredientName = mysqli_real_escape_string($conn, trim($_POST['ingredientName']));
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $expirationDate = mysqli_real_escape_string($conn, $_POST['expirationDate']);
    $threshold = mysqli_real_escape_string($conn, $_POST['threshold']);
    $lastUpdated = date('Y-m-d');

    // Ensure quantity is not negative and round to 2 decimal places
    if ($quantity < 0) {
        $quantity = 0;
    }
    $quantity = round($quantity, 2); // Round to 2 decimal places

    // Validation
    if (empty($ingredientName) || empty($unit) || empty($expirationDate) || empty($threshold)) {
        $message = 'All fields are required!';
        $messageType = 'error';
    } else {
        // Check if this is a new ingredient (ingredientID is empty)
        if (empty($ingredientID)) {
            // Check if ingredient name already exists
            $checkIngredientQuery = "SELECT ingredientID FROM ingredients WHERE ingredientName = '$ingredientName'";
            $checkIngredientResult = executeQuery($checkIngredientQuery);

            if (mysqli_num_rows($checkIngredientResult) > 0) {
                // Ingredient exists, get the ID
                $existingIngredient = mysqli_fetch_assoc($checkIngredientResult);
                $ingredientID = $existingIngredient['ingredientID'];
            } else {
                // Insert new ingredient
                $insertIngredientQuery = "INSERT INTO ingredients (ingredientName) VALUES ('$ingredientName')";
                if (executeQuery($insertIngredientQuery)) {
                    $ingredientID = mysqli_insert_id($conn);
                } else {
                    $message = 'Error adding new ingredient!';
                    $messageType = 'error';
                }
            }
        }

        // If we have ingredientID (either existing or newly created), proceed with inventory
        if (!empty($ingredientID) && empty($message)) {
            // Check if this ingredient already exists in inventory
            $checkInventoryQuery = "SELECT inventoryID FROM inventory WHERE ingredientID = '$ingredientID'";
            $checkInventoryResult = executeQuery($checkInventoryQuery);

            if (mysqli_num_rows($checkInventoryResult) > 0) {
                $message = 'This ingredient already exists in inventory!';
                $messageType = 'error';
            } else {
                // Insert new inventory item with rounded quantity
                $insertInventoryQuery = "INSERT INTO inventory (ingredientID, quantity, unit, lastUpdated, expirationDate, threshold) 
                                       VALUES ('$ingredientID', '$quantity', '$unit', '$lastUpdated', '$expirationDate', '$threshold')";

                if (executeQuery($insertInventoryQuery)) {
                    $message = 'Inventory item added successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error adding inventory item!';
                    $messageType = 'error';
                }
            }
        }
    }
}

// UPDATE - Edit inventory item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $inventoryID = mysqli_real_escape_string($conn, $_POST['inventoryID']);
    $ingredientID = mysqli_real_escape_string($conn, $_POST['ingredientID']);
    $ingredientName = mysqli_real_escape_string($conn, trim($_POST['ingredientName']));
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $expirationDate = mysqli_real_escape_string($conn, $_POST['expirationDate']);
    $threshold = mysqli_real_escape_string($conn, $_POST['threshold']);
    $lastUpdated = date('Y-m-d');

    // Ensure quantity is not negative and round to 2 decimal places
    if ($quantity < 0) {
        $quantity = 0;
        $message = 'Quantity cannot be negative. Set to 0 instead.';
        $messageType = 'warning';
    }
    $quantity = round($quantity, 2); // Round to 2 decimal places

    // Validation
    if (empty($inventoryID) || empty($ingredientName) || empty($unit) || empty($expirationDate) || empty($threshold)) {
        $message = 'All fields are required!';
        $messageType = 'error';
    } else {
        // Update ingredient name if changed
        $updateIngredientQuery = "UPDATE ingredients SET ingredientName = '$ingredientName' WHERE ingredientID = '$ingredientID'";
        executeQuery($updateIngredientQuery);

        // Update inventory with rounded quantity
        $updateQuery = "UPDATE inventory 
                       SET quantity = '$quantity', 
                           unit = '$unit', 
                           lastUpdated = '$lastUpdated', 
                           expirationDate = '$expirationDate',
                           threshold = '$threshold'
                       WHERE inventoryID = '$inventoryID'";

        if (executeQuery($updateQuery)) {
            if (empty($message)) {
                $message = 'Inventory item updated successfully!';
                $messageType = 'success';
            }
        } else {
            $message = 'Error updating inventory item!';
            $messageType = 'error';
        }
    }
}

// DELETE - Remove inventory item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $inventoryID = mysqli_real_escape_string($conn, $_POST['inventoryID']);

    if (empty($inventoryID)) {
        $message = 'Invalid inventory item!';
        $messageType = 'error';
    } else {
        $deleteQuery = "DELETE FROM inventory WHERE inventoryID = '$inventoryID'";

        if (executeQuery($deleteQuery)) {
            $message = 'Inventory item deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error deleting inventory item!';
            $messageType = 'error';
        }
    }
}

// Get ingredients for dropdown/autocomplete
$ingredientsQuery = "SELECT ingredientID, ingredientName FROM ingredients ORDER BY ingredientName";
$ingredientsResult = executeQuery($ingredientsQuery);
$ingredients = [];
if ($ingredientsResult && mysqli_num_rows($ingredientsResult) > 0) {
    while ($row = mysqli_fetch_assoc($ingredientsResult)) {
        $ingredients[] = $row;
    }
}

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

// Query - Round quantity to 2 decimal places and make sure it's never negative
$inventoryQuery = "SELECT i.inventoryID,
                          i.ingredientID,
                          ing.ingredientName,
                          ROUND(GREATEST(i.quantity, 0), 2) as quantity,
                          i.unit,
                          i.lastUpdated,
                          i.expirationDate,
                          i.threshold
                   FROM inventory i
                   LEFT JOIN ingredients ing 
                        ON i.ingredientID = ing.ingredientID
                   ORDER BY $sort $order";


$result = executeQuery($inventoryQuery);
$rows = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Format quantity to remove unnecessary decimals (5.00 becomes 5, 5.50 stays 5.5, 5.23 stays 5.23)
        $row['quantity'] = formatQuantity($row['quantity']);
        $rows[] = $row;
    }
}

// Helper to toggle order in links
function toggleOrder($currentOrder)
{
    return $currentOrder === 'ASC' ? 'desc' : 'asc';
}

// Calculate statistics
$totalItems = count($rows);
$lowStockCount = 0;
$outOfStockCount = 0;
$expiredCount = 0;

foreach ($rows as $row) {
    // Count out of stock items (ONLY when quantity is exactly 0)
    if (floatval($row['quantity']) == 0) {
        $outOfStockCount++;
    }
    // Count low stock items (excluding out of stock, only when quantity > 0 but <= threshold)
    elseif (floatval($row['quantity']) > 0 && floatval($row['quantity']) <= floatval($row['threshold'])) {
        $lowStockCount++;
    }

    $expirationDate = strtotime($row['expirationDate']);
    $currentDate = time();
    if ($expirationDate < $currentDate) {
        $expiredCount++;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Management - Saisyd Cafe</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/inventory-management.css">
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css">

    <!-- Bootstrap Icons -->
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
    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Mobile Menu Toggle Button -->
    <div class="d-md-none mobile-header d-flex align-items-center pt-3 px-3">
        <button id="menuToggle" class="mobile-menu-toggle me-3">
            <i class="fas fa-bars"></i>
        </button>
    </div>

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
                <a href="orders.php" class="admin-nav-link">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Order Management</span>
                </a>
                <a href="point-of-sales.php" class="admin-nav-link">
                    <i class="bi bi-shop-window"></i>
                    <span>Point of Sales</span>
                </a>
                <a href="inventory-management.php" class="admin-nav-link active">
                    <i class="bi bi-boxes"></i>
                    <span>Inventory Management</span>
                </a>
                <a href="menu-management.php" class="admin-nav-link">
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
                <a href="settings.php" class="admin-nav-link">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
                <a href="logout.php" class="admin-nav-link">
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
            <a href="orders.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.15s">
                <i class="bi bi-clipboard-check"></i>
                <span>Order Management</span>
            </a>
            <a href="point-of-sales.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.2s">
                <i class="bi bi-shop-window"></i>
                <span>Point of Sales</span>
            </a>
            <a href="inventory-management.php" class="admin-nav-link active wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.25s">
                <i class="bi bi-boxes"></i>
                <span>Inventory Management</span>
            </a>
            <a href="menu-management.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
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
            <a href="settings.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.4s">
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
        <div class="container-fluid px-3 px-lg-4">

            <!-- Header Section -->
            <div class="header-section">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start mb-4">
                    <div class="text-center text-md-start w-100">
                        <h1 class="page-title pt-lg-4 pt-0">Inventory Management</h1>

                    </div>

                    <div class="stats-cards d-none d-lg-flex">
                        <div class="stat-card">
                            <div class="stat-number" id="totalItems"><?php echo $totalItems; ?></div>
                            <div class="stat-label">Total Items</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number warning" id="lowStockItems"><?php echo $lowStockCount; ?></div>
                            <div class="stat-label">Low Stock</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number danger" id="outOfStockItems"><?php echo $outOfStockCount; ?></div>
                            <div class="stat-label">Out of Stock</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number danger" id="expiredItems"><?php echo $expiredCount; ?></div>
                            <div class="stat-label">Expired</div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Stats Cards -->
                <div class="mobile-stats d-lg-none mb-4">
                    <div class="row g-2">
                        <div class="col-3">
                            <div class="stat-card">
                                <div class="stat-number" id="totalItemsMobile"><?php echo $totalItems; ?></div>
                                <div class="stat-label">Total</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-card">
                                <div class="stat-number warning" id="lowStockMobile"><?php echo $lowStockCount; ?></div>
                                <div class="stat-label">Low Stock</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-card">
                                <div class="stat-number danger" id="outOfStockMobile"><?php echo $outOfStockCount; ?>
                                </div>
                                <div class="stat-label">Out of Stock</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-card">
                                <div class="stat-number danger" id="expiredMobile"><?php echo $expiredCount; ?></div>
                                <div class="stat-label">Expired</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Bar -->
            <div class="action-bar mb-4">
                <div class="row g-3 align-items-end">
                    <!-- Search Section -->
                    <div class="col-12 col-md-12 col-lg-5">
                        <label class="form-label fw-semibold">Quick Search</label>
                        <div class="search-container">
                            <div class="input-group">
                                <input type="text" class="form-control search-input"
                                    placeholder="Search by item name or code..." id="searchInput">
                                <button class="btn search-btn" type="button" id="searchBtn">
                                    <i class="bi bi-search"></i>
                                    <span class="d-none d-sm-inline ms-1">Search</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Toggle & Actions -->
                    <div class="col-12 col-md-12 col-lg-7">
                        <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-end w-100">

                            <!-- Filter Toggle -->
                            <button class="btn filter-toggle-btn flex-grow-1 flex-md-grow-0" type="button"
                                data-bs-toggle="collapse" data-bs-target="#filterSection" id="filterToggle"
                                style="font-size: 0.9rem; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                                <i class="bi bi-funnel" style="font-size: 1rem;"></i>
                                <span class="ms-1">Filters</span>
                                <span class="filter-count d-none" id="filterCount">0</span>
                            </button>

                            <!-- Action Buttons -->
                            <div class="btn-group flex-grow-1 flex-md-grow-0" role="group">
                                <button class="btn action-btn add-btn" type="button" data-bs-toggle="modal"
                                    data-bs-target="#addItemModal"
                                    style="font-size: 0.9rem; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                                    <i class="bi bi-plus-circle" style="font-size: 1rem;"></i>
                                    <span class="ms-1">Add Item</span>
                                </button>
                                <button class="btn action-btn export-btn" type="button" data-bs-toggle="modal"
                                    data-bs-target="#exportModal"
                                    style="font-size: 0.9rem; box-shadow: 0 4px 6px rgba(0,0,0,0.15);">
                                    <i class="bi bi-download" style="font-size: 1rem;"></i>
                                    <span class="ms-1">Export</span>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Filters Section -->
            <div class="collapse mb-4" id="filterSection">
                <div class="filter-panel">
                    <div class="filter-header">
                        <h5 class="filter-title">
                            <i class="bi bi-sliders me-2"></i>Advanced Filters
                        </h5>
                    </div>

                    <!-- Active Filters Display -->
                    <div id="activeFilters" class="active-filters d-none">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="active-filters-label">Active Filters:</span>
                            <button class="btn btn-sm btn-link text-danger p-0" id="clearFilters">
                                Clear All
                            </button>
                        </div>
                        <div id="activeFilterTags" class="filter-tags"></div>
                    </div>

                    <div class="filter-grid">
                        <div class="filter-group">
                            <label class="filter-label">Stock Status</label>
                            <select class="filter-select" id="filterStockStatus">
                                <option value="">All Items</option>
                                <option value="low">Low Stock</option>
                                <option value="normal">Normal Stock</option>
                                <option value="out">Out of Stock</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Expiry Status</label>
                            <select class="filter-select" id="filterExpiryStatus">
                                <option value="">All Items</option>
                                <option value="expired">Expired</option>
                                <option value="expiring">Expiring Soon (7 days)</option>
                                <option value="fresh">Fresh</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label class="filter-label">Sort By Name</label>
                            <select class="filter-select" id="filterSortOrder">
                                <option value="asc">Name: A → Z</option>
                                <option value="desc">Name: Z → A</option>
                            </select>
                        </div>

                        <div class="filter-group d-flex align-items-end">
                            <button class="btn filter-apply-btn w-100" id="applyFilters">
                                <i class="bi bi-check-circle me-1"></i>Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Summary -->
            <div class="results-summary mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="results-info">
                        <span class="results-text">
                            Showing <span id="showingCount"><?php echo count($rows); ?></span>
                            of <span id="totalCount"><?php echo count($rows); ?></span> items
                        </span>
                    </div>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="inventory-table-container">
                <div class="table-responsive">
                    <table class="table inventory-table" id="inventoryTable">
                        <thead class="table-header">
                            <tr>
                                <th scope="col">
                                    <div class="th-content">
                                        <span>Item Code</span>
                                        <i class="bi bi-chevron-expand sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="th-content">
                                        <span>Item Name</span>
                                        <i class="bi bi-chevron-expand sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="th-content">
                                        <span>Quantity</span>
                                        <i class="bi bi-chevron-expand sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="th-content">
                                        <span>Date Purchased</span>
                                        <i class="bi bi-chevron-expand sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="th-content">
                                        <span>Expiration Date</span>
                                        <i class="bi bi-chevron-expand sort-icon"></i>
                                    </div>
                                </th>
                                <th scope="col" class="actions-col">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-body" id="inventoryTableBody">
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr data-item-code="<?= str_pad($row['inventoryID'], 3, "0", STR_PAD_LEFT) ?>"
                                        data-quantity="<?= $row['quantity'] ?>" data-purchase-date="<?= $row['lastUpdated'] ?>"
                                        data-expiry-date="<?= $row['expirationDate'] ?>"
                                        data-threshold="<?= $row['threshold'] ?>">

                                        <td class="item-code-cell">
                                            <span
                                                class="item-code"><?= str_pad($row['inventoryID'], 3, "0", STR_PAD_LEFT) ?></span>
                                        </td>

                                        <td class="item-name-cell">
                                            <div class="item-name-content">
                                                <span class="item-name"><?= $row['ingredientName'] ?? 'No Ingredient' ?></span>
                                            </div>
                                        </td>

                                        <td class="quantity-cell">
                                            <div class="quantity-content">
                                                <span class="quantity-value"><?= $row['quantity'] ?></span>
                                                <span class="quantity-unit"><?= $row['unit'] ?></span>
                                                <?php 
                                                // FIXED: Only show "Out of Stock" when quantity is EXACTLY 0
                                                if (floatval($row['quantity']) == 0): ?>
                                                    <span class="status-badge out-of-stock">Out of Stock</span>
                                                <?php 
                                                // Show "Low Stock" only when quantity > 0 BUT <= threshold
                                                elseif (floatval($row['quantity']) > 0 && floatval($row['quantity']) <= floatval($row['threshold'])): ?>
                                                    <span class="status-badge low-stock">Low Stock</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <td class="date-cell">
                                            <span
                                                class="date-value"><?= date("M d, Y", strtotime($row['lastUpdated'])) ?></span>
                                        </td>

                                        <td class="expiry-cell">
                                            <div class="expiry-content">
                                                <?php
                                                $expirationDate = strtotime($row['expirationDate']);
                                                $currentDate = time();
                                                $daysUntilExpiry = ceil(($expirationDate - $currentDate) / (60 * 60 * 24));
                                                ?>
                                                <span class="date-value"><?= date("M d, Y", $expirationDate) ?></span>
                                                <?php if ($daysUntilExpiry <= 0): ?>
                                                    <span class="status-badge expired">Expired</span>
                                                <?php elseif ($daysUntilExpiry <= 7): ?>
                                                    <span class="status-badge expiring">Expiring Soon</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <td class="actions-cell">
                                            <div class="action-buttons">
                                                <button class="btn action-btn-sm edit-btn" data-id="<?= $row['inventoryID'] ?>"
                                                    data-ingredient-id="<?= $row['ingredientID'] ?>"
                                                    data-ingredient="<?= htmlspecialchars($row['ingredientName']) ?>"
                                                    data-quantity="<?= $row['quantity'] ?>" data-unit="<?= $row['unit'] ?>"
                                                    data-expiration="<?= $row['expirationDate'] ?>"
                                                    data-threshold="<?= $row['threshold'] ?>" data-bs-toggle="modal"
                                                    data-bs-target="#editItemModal">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn action-btn-sm delete-btn" title="Delete Item"
                                                    onclick="confirmDelete(<?= $row['inventoryID'] ?>, '<?= addslashes($row['ingredientName']) ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="noRecordsRow">
                                    <td colspan="6" class="no-records">
                                        <div class="no-records-content">
                                            <i class="bi bi-inbox"></i>
                                            <p>No inventory items found</p>
                                            <button class="btn action-btn add-btn" data-bs-toggle="modal"
                                                data-bs-target="#addItemModal">
                                                <i class="bi bi-plus-circle"></i> Add First Item
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination (if needed) -->
            <div class="pagination-container d-none">
                <nav aria-label="Inventory pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- MODALS -->
    <?php include '../modal/add-item-inventory-modal.php'; ?>
    <?php include '../modal/edit-item-inventory-modal.php'; ?>
    <?php include '../modal/delete-confirm-modal.php'; ?>
    <?php include '../modal/export-modal.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function () {
            // Loop through all edit buttons
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function () {
                    // Grab dataset values
                    const inventoryID = this.dataset.id;
                    const ingredientID = this.dataset.ingredientId;
                    const ingredient = this.dataset.ingredient;
                    const quantity = this.dataset.quantity;
                    const unit = this.dataset.unit;
                    const expiration = this.dataset.expiration;

                    // Fill modal fields
                    document.getElementById('inventoryID').value = inventoryID;
                    document.getElementById('ingredientIDEdit').value = ingredientID;
                    document.getElementById('ingredientNameEdit').value = ingredient;
                    document.getElementById('quantityEdit').value = quantity;
                    document.getElementById('unitEdit').value = unit;
                    document.getElementById('expirationEdit').value = expiration;
                    document.getElementById('thresholdEdit').value = this.dataset.threshold;
                });
            });

            // CLIENT-SIDE VALIDATION: Prevent negative quantity input
            const quantityInputs = document.querySelectorAll('input[name="quantity"]');
            quantityInputs.forEach(input => {
                input.addEventListener('input', function () {
                    if (this.value < 0) {
                        this.value = 0;
                        showToast('Quantity cannot be negative. Set to 0.', 'warning');
                    }
                });

                // Also check on blur (when user leaves the field)
                input.addEventListener('blur', function () {
                    if (this.value === '' || this.value < 0) {
                        this.value = 0;
                    }
                });
            });

            // Set minimum attribute to 0 for quantity inputs
            quantityInputs.forEach(input => {
                input.setAttribute('min', '0');
                input.setAttribute('step', '0.01');
            });
        });

        // Toast Notification System
        function showToast(message, type = 'success') {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();

            const toastHtml = `
            <div class="toast ${type}" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}" data-bs-autohide="true" data-bs-delay="5000">
                <div class="toast-header">
                    <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : type === 'warning' ? 'bi-exclamation-triangle-fill' : 'bi-exclamation-triangle-fill'} me-2"></i>
                    <strong class="me-auto">${type === 'success' ? 'Success' : type === 'warning' ? 'Warning' : 'Error'}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

            toastContainer.insertAdjacentHTML('beforeend', toastHtml);

            const toast = new bootstrap.Toast(document.getElementById(toastId));
            toast.show();

            // Remove toast element after it's hidden
            document.getElementById(toastId).addEventListener('hidden.bs.toast', function () {
                this.remove();
            });
        }

        // Show PHP messages as toasts
        <?php if (!empty($message)): ?>
            showToast('<?= addslashes($message) ?>', '<?= $messageType ?>');
        <?php endif; ?>

        // Enhanced Inventory Management System with Individual Filter Removal
        class InventoryManager {
            constructor() {
                this.originalRows = Array.from(document.querySelectorAll('#inventoryTableBody tr:not(#noRecordsRow)'));
                this.filteredRows = [...this.originalRows];
                this.activeFilters = {};
                this.init();
            }

            init() {
                this.bindEvents();
                this.updateStats();
            }

            bindEvents() {
                // Search functionality
                const searchInput = document.getElementById('searchInput');
                const searchBtn = document.getElementById('searchBtn');

                searchInput.addEventListener('input', (e) => this.debounceSearch(e.target.value));
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.performSearch(e.target.value);
                    }
                });
                searchBtn.addEventListener('click', () => this.performSearch(searchInput.value));

                // Filter functionality
                document.getElementById('applyFilters').addEventListener('click', () => this.applyFilters());
                document.getElementById('clearFilters').addEventListener('click', () => this.clearAllFilters());

                // Filter toggle
                const filterToggle = document.getElementById('filterToggle');
                filterToggle.addEventListener('click', () => {
                    const isExpanded = filterToggle.getAttribute('aria-expanded') === 'true';
                    if (!isExpanded) {
                        filterToggle.classList.add('active');
                    } else {
                        filterToggle.classList.remove('active');
                    }
                });

                // Enter key support for all filter inputs
                document.querySelectorAll('.filter-input, .filter-select').forEach(input => {
                    input.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter') {
                            this.applyFilters();
                        }
                    });

                    input.addEventListener('change', () => {
                        this.updateFilterCount();
                    });
                });
            }

            debounceSearch(value) {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.performSearch(value);
                }, 300);
            }

            performSearch(searchValue) {
                if (!searchValue.trim()) {
                    this.filteredRows = [...this.originalRows];
                } else {
                    const searchLower = searchValue.toLowerCase();
                    this.filteredRows = this.originalRows.filter(row => {
                        const itemName = row.querySelector('.item-name').textContent.toLowerCase();
                        const itemCode = row.dataset.itemCode.toLowerCase();
                        return itemName.includes(searchLower) || itemCode.includes(searchLower);
                    });
                }

                this.renderTable();
                this.updateStats();

                if (searchValue.trim()) {
                    this.showResultsMessage(`Found ${this.filteredRows.length} items matching "${searchValue}"`);
                }
            }

            applyFilters() {
                const filters = {
                    stockStatus: document.getElementById('filterStockStatus').value,
                    expiryStatus: document.getElementById('filterExpiryStatus').value,
                    sortOrder: document.getElementById('filterSortOrder').value,
                    search: document.getElementById('searchInput').value.trim()
                };

                this.activeFilters = filters;

                // Start with original rows
                this.filteredRows = [...this.originalRows];

                // Apply search filter first
                if (filters.search) {
                    const searchLower = filters.search.toLowerCase();
                    this.filteredRows = this.filteredRows.filter(row => {
                        const itemName = row.querySelector('.item-name').textContent.toLowerCase();
                        const itemCode = row.dataset.itemCode.toLowerCase();
                        return itemName.includes(searchLower) || itemCode.includes(searchLower);
                    });
                }

                // Apply other filters
                this.filteredRows = this.filteredRows.filter(row => {
                    const quantity = parseFloat(row.dataset.quantity);
                    const expiryDate = row.dataset.expiryDate;
                    const threshold = parseFloat(row.dataset.threshold);

                    // Stock status filter - FIXED: Only "Out of Stock" when quantity is exactly 0
                    if (filters.stockStatus) {
                        const isOutOfStock = quantity === 0;
                        const isLowStock = quantity > 0 && quantity <= threshold;

                        switch (filters.stockStatus) {
                            case 'low':
                                if (!isLowStock) return false;
                                break;
                            case 'normal':
                                if (isLowStock || isOutOfStock) return false;
                                break;
                            case 'out':
                                if (!isOutOfStock) return false;
                                break;
                        }
                    }

                    // Expiry status filter
                    if (filters.expiryStatus) {
                        const expiryTimestamp = new Date(expiryDate).getTime();
                        const currentTimestamp = new Date().getTime();
                        const daysUntilExpiry = Math.ceil((expiryTimestamp - currentTimestamp) / (1000 * 60 * 60 * 24));

                        switch (filters.expiryStatus) {
                            case 'expired':
                                if (daysUntilExpiry > 0) return false;
                                break;
                            case 'expiring':
                                if (daysUntilExpiry <= 0 || daysUntilExpiry > 7) return false;
                                break;
                            case 'fresh':
                                if (daysUntilExpiry <= 7) return false;
                                break;
                        }
                    }

                    return true;
                });

                // Apply sorting by name
                if (filters.sortOrder) {
                    this.filteredRows.sort((a, b) => {
                        const nameA = a.querySelector('.item-name').textContent.toLowerCase();
                        const nameB = b.querySelector('.item-name').textContent.toLowerCase();

                        if (filters.sortOrder === 'asc') {
                            return nameA.localeCompare(nameB);
                        } else {
                            return nameB.localeCompare(nameA);
                        }
                    });
                }

                this.renderTable();
                this.updateActiveFiltersDisplay();
                this.updateFilterCount();
                this.updateStats();

                showToast(`Filters applied. Showing ${this.filteredRows.length} of ${this.originalRows.length} items.`);
            }

            // Remove individual filter
            removeFilter(filterType) {
                switch (filterType) {
                    case 'search':
                        document.getElementById('searchInput').value = '';
                        this.activeFilters.search = '';
                        break;
                    case 'stockStatus':
                        document.getElementById('filterStockStatus').value = '';
                        this.activeFilters.stockStatus = '';
                        break;
                    case 'expiryStatus':
                        document.getElementById('filterExpiryStatus').value = '';
                        this.activeFilters.expiryStatus = '';
                        break;
                    case 'sortOrder':
                        document.getElementById('filterSortOrder').value = 'asc';
                        this.activeFilters.sortOrder = 'asc';
                        break;
                }

                // Re-apply filters
                this.applyFilters();

                showToast(`${this.getFilterDisplayName(filterType)} filter removed.`, 'success');
            }

            // Get display name for filter types
            getFilterDisplayName(filterType) {
                const displayNames = {
                    'search': 'Search',
                    'stockStatus': 'Stock Status',
                    'expiryStatus': 'Expiry Status',
                    'sortOrder': 'Sort Order'
                };
                return displayNames[filterType] || filterType;
            }

            // Get display value for filter
            getFilterDisplayValue(filterType, value) {
                switch (filterType) {
                    case 'stockStatus':
                        const stockDisplays = {
                            'low': 'Low Stock',
                            'normal': 'Normal Stock',
                            'out': 'Out of Stock'
                        };
                        return stockDisplays[value] || value;

                    case 'expiryStatus':
                        const expiryDisplays = {
                            'expired': 'Expired',
                            'expiring': 'Expiring Soon',
                            'fresh': 'Fresh'
                        };
                        return expiryDisplays[value] || value;

                    case 'sortOrder':
                        return value === 'asc' ? 'A to Z' : 'Z to A';

                    case 'search':
                        return `"${value}"`;

                    default:
                        return value;
                }
            }

            clearAllFilters() {
                // Clear all filter inputs
                document.getElementById('filterStockStatus').value = '';
                document.getElementById('filterExpiryStatus').value = '';
                document.getElementById('filterSortOrder').value = 'asc';
                document.getElementById('searchInput').value = '';

                // Reset state
                this.activeFilters = {};
                this.filteredRows = [...this.originalRows];

                this.renderTable();
                this.hideActiveFilters();
                this.updateFilterCount();
                this.updateStats();

                showToast('All filters cleared.');
            }

            updateFilterCount() {
                const filterCount = document.getElementById('filterCount');
                const activeCount = this.getActiveFilterCount();

                if (activeCount > 0) {
                    filterCount.textContent = activeCount;
                    filterCount.classList.remove('d-none');
                } else {
                    filterCount.classList.add('d-none');
                }
            }

            getActiveFilterCount() {
                let count = 0;

                // Count non-empty filters
                if (this.activeFilters.search) count++;
                if (this.activeFilters.stockStatus) count++;
                if (this.activeFilters.expiryStatus) count++;
                if (this.activeFilters.sortOrder && this.activeFilters.sortOrder !== 'asc') count++;

                return count;
            }

            // Enhanced active filters display with individual remove buttons
            updateActiveFiltersDisplay() {
                const activeFiltersDiv = document.getElementById('activeFilters');
                const activeFilterTags = document.getElementById('activeFilterTags');
                const tags = [];

                // Build filter tags with remove buttons
                if (this.activeFilters.search) {
                    tags.push({
                        type: 'search',
                        label: 'Search',
                        value: this.getFilterDisplayValue('search', this.activeFilters.search)
                    });
                }

                if (this.activeFilters.stockStatus) {
                    tags.push({
                        type: 'stockStatus',
                        label: 'Stock Status',
                        value: this.getFilterDisplayValue('stockStatus', this.activeFilters.stockStatus)
                    });
                }

                if (this.activeFilters.expiryStatus) {
                    tags.push({
                        type: 'expiryStatus',
                        label: 'Expiry Status',
                        value: this.getFilterDisplayValue('expiryStatus', this.activeFilters.expiryStatus)
                    });
                }

                if (this.activeFilters.sortOrder && this.activeFilters.sortOrder !== 'asc') {
                    tags.push({
                        type: 'sortOrder',
                        label: 'Sort',
                        value: this.getFilterDisplayValue('sortOrder', this.activeFilters.sortOrder)
                    });
                }

                if (tags.length > 0) {
                    activeFilterTags.innerHTML = tags.map(tag =>
                        `<span class="filter-tag" data-filter-type="${tag.type}">
                    ${tag.label}: ${tag.value}
                    <button class="filter-tag-remove" onclick="window.inventoryManager.removeFilter('${tag.type}')" title="Remove this filter">
                        <i class="bi bi-x"></i>
                    </button>
                </span>`
                    ).join('');
                    activeFiltersDiv.classList.remove('d-none');
                } else {
                    this.hideActiveFilters();
                }
            }

            hideActiveFilters() {
                document.getElementById('activeFilters').classList.add('d-none');
            }

            renderTable() {
                const tbody = document.getElementById('inventoryTableBody');
                const noRecordsRow = document.getElementById('noRecordsRow');

                // Hide all original rows
                this.originalRows.forEach(row => row.style.display = 'none');

                if (this.filteredRows.length === 0) {
                    if (noRecordsRow) {
                        noRecordsRow.style.display = 'table-row';
                        noRecordsRow.querySelector('.no-records-content p').textContent = 'No items match the current filters';
                    } else {
                        tbody.innerHTML = `
                <tr id="noRecordsRow">
                    <td colspan="6" class="no-records">
                        <div class="no-records-content">
                            <i class="bi bi-search"></i>
                            <p>No items match the current filters</p>
                            <button class="btn btn-clear" onclick="window.inventoryManager.clearAllFilters()">
                                <i class="bi bi-x-circle"></i> Clear Filters
                            </button>
                        </div>
                    </td>
                </tr>
            `;
                    }
                } else {
                    if (noRecordsRow) {
                        noRecordsRow.style.display = 'none';
                    }

                    // Re-append filtered rows in the correct order
                    this.filteredRows.forEach(row => {
                        tbody.appendChild(row);
                        row.style.display = 'table-row';
                    });
                }
            }

            updateStats() {
                const showingCount = document.getElementById('showingCount');
                const totalCount = document.getElementById('totalCount');

                if (showingCount) showingCount.textContent = this.filteredRows.length;
                if (totalCount) totalCount.textContent = this.originalRows.length;

                // Update header stats based on filtered results
                let lowStockCount = 0;
                let outOfStockCount = 0;
                let expiredCount = 0;

                this.filteredRows.forEach(row => {
                    const quantity = parseFloat(row.dataset.quantity);
                    const threshold = parseFloat(row.dataset.threshold);
                    const expiryDate = new Date(row.dataset.expiryDate);
                    const currentDate = new Date();

                    // FIXED: Count out of stock only when quantity is exactly 0
                    if (quantity === 0) {
                        outOfStockCount++;
                    }
                    // Count low stock only when quantity > 0 but <= threshold
                    else if (quantity > 0 && quantity <= threshold) {
                        lowStockCount++;
                    }

                    if (expiryDate < currentDate) {
                        expiredCount++;
                    }
                });

                // Update all stat displays
                const totalElements = document.querySelectorAll('#totalItems, #totalItemsMobile');
                const lowStockElements = document.querySelectorAll('#lowStockItems, #lowStockMobile');
                const outOfStockElements = document.querySelectorAll('#outOfStockItems, #outOfStockMobile');
                const expiredElements = document.querySelectorAll('#expiredItems, #expiredMobile');

                totalElements.forEach(el => el.textContent = this.filteredRows.length);
                lowStockElements.forEach(el => el.textContent = lowStockCount);
                outOfStockElements.forEach(el => el.textContent = outOfStockCount);
                expiredElements.forEach(el => el.textContent = expiredCount);
            }

            showResultsMessage(message) {
                console.log(message);
            }
        }

        // Confirm delete function
        function confirmDelete(inventoryID, itemName) {
            document.getElementById('deleteInventoryID').value = inventoryID;
            document.getElementById('deleteItemName').textContent = itemName;
            new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
        }

        // Initialize the inventory manager when page loads
        document.addEventListener('DOMContentLoaded', function () {
            window.inventoryManager = new InventoryManager();

            // Initialize WOW animations
            if (typeof WOW !== 'undefined') {
                new WOW().init();
            }

            // Handle form submissions with loading states
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        submitBtn.disabled = true;
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Processing...';

                        // Re-enable after 3 seconds as fallback
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }, 3000);
                    }
                });
            });
        });
    </script>

</body>

</html>