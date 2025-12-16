<?php
include('auth_check.php');
include '../assets/connect.php';

// Check if user is logged in and is an admin 
if (!isset($_SESSION['userID']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: logout.php");
    exit();
}


// Daily total sales for the past 30 days
$dailySales = "
    SELECT 
        DATE(o.orderDate) AS sale_date,
        SUM(o.totalAmount) AS total_sales
    FROM orders o
    JOIN payments p ON o.orderID = p.orderID
    WHERE o.status = 'completed'
      AND p.paymentStatus = 'Paid'
      AND o.orderDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY sale_date
    ORDER BY sale_date;
";

$result = mysqli_query($conn, $dailySales);
$salesDates = [];
$salesTotals = [];

while ($row = mysqli_fetch_assoc($result)) {
    $salesDates[] = $row['sale_date'];
    $salesTotals[] = $row['total_sales'];
}

// Weekly total sales
$weeklySales = "SELECT 
    SUM(o.totalAmount) AS total_sales
FROM orders o
JOIN payments p ON o.orderID = p.orderID
WHERE p.paymentStatus = 'paid'
AND o.status = 'completed'
  AND YEARWEEK(o.orderDate, 1) = YEARWEEK(CURDATE(), 1)";

$weeklyResult = mysqli_query($conn, $weeklySales);
$weeklyRow = mysqli_fetch_assoc($weeklyResult);
$weeklyTotal = $weeklyRow['total_sales'] ?? 0;

// Monthly total sales
$monthlySales = "SELECT 
    SUM(o.totalAmount) AS total_sales
FROM orders o
JOIN payments p ON o.orderID = p.orderID
WHERE p.paymentStatus = 'paid'
AND o.status = 'completed'
  AND YEAR(o.orderDate) = YEAR(CURDATE())
  AND MONTH(o.orderDate) = MONTH(CURDATE())";
$monthlyResult = mysqli_query($conn, $monthlySales);
$monthlyRow = mysqli_fetch_assoc($monthlyResult);
$monthlyTotal = $monthlyRow['total_sales'] ?? 0;

$avgOrderValue = "
    SELECT 
    ROUND(AVG(o.totalAmount), 2) AS avg_order_value
FROM orders o
JOIN payments p ON o.orderID = p.orderID
WHERE p.paymentStatus = 'Paid'
  AND o.status = 'Completed'
  AND DATE(o.orderDate) = CURDATE();
";
$avgOrderValueResult = mysqli_query($conn, $avgOrderValue);
$avgOrderValueRow = mysqli_fetch_assoc($avgOrderValueResult);
$averageOrderValue = $avgOrderValueRow['avg_order_value'] ?? 0;


// Top selling product (most popular)
$topProducts = "SELECT 
    pr.productName,
    SUM(oi.quantity) AS total_qty_sold,
    SUM(oi.quantity * pr.price) AS total_sales
FROM orderitems oi
JOIN products pr ON oi.productID = pr.productID
JOIN orders o ON oi.orderID = o.orderID
JOIN payments p ON o.orderID = p.orderID
WHERE p.paymentStatus = 'paid'
AND o.status = 'completed'
  AND YEARWEEK(o.orderDate, 1) = YEARWEEK(CURDATE(), 1)
GROUP BY pr.productID, pr.productName
ORDER BY total_qty_sold DESC
LIMIT 5;";
$topProductsResult = mysqli_query($conn, $topProducts);
$topProductsData = [];
while ($row = mysqli_fetch_assoc($topProductsResult)) {
    $topProductsData[] = $row;
}

// Total products sold
$totalItemsSold = "
SELECT 
    SUM(oi.quantity) AS total_items_sold
FROM orderitems oi
JOIN orders o ON oi.orderID = o.orderID
JOIN payments p ON o.orderID = p.orderID
WHERE o.status = 'completed'
AND p.paymentStatus = 'paid'
  AND YEARWEEK(o.orderDate, 1) = YEARWEEK(CURDATE(), 1);
";

$totalItemsSoldResult = mysqli_query($conn, $totalItemsSold);
$totalItemsSoldRow = mysqli_fetch_assoc($totalItemsSoldResult);
$totalItemsSoldCount = $totalItemsSoldRow['total_items_sold'] ?? 0;

// Today's website visits
$dailyVisits = "
    SELECT 
        DATE(visitDate) AS visitDate,
        COUNT(DISTINCT CONCAT(ipAddress, '-', FLOOR(UNIX_TIMESTAMP(visitDate)/1800))) AS dailyVisits
    FROM visits
    WHERE DATE(visitDate) = CURDATE()
    GROUP BY DATE(visitDate)
    ORDER BY visitDate;
";

$dailyVisitsResult = mysqli_query($conn, $dailyVisits);
$dailyVisitsRow = mysqli_fetch_assoc($dailyVisitsResult);
$todayVisits = $dailyVisitsRow['dailyVisits'] ?? 0;

// Recent Transactions
$transactionHistory = "
SELECT
  o.orderID,
  o.orderNumber,
  o.orderDate,
  o.totalAmount,
  p.paymentMethod,
  p.paymentStatus,
  o.customerName,
  u.username AS userName,
  COALESCE(NULLIF(TRIM(o.customerName), ''), u.username) AS displayName,
  GROUP_CONCAT(
    CONCAT(
      pr.productName,
      ' x', oi.quantity,
      IF(oi.sugar IS NOT NULL, CONCAT(' | Sugar: ', oi.sugar), ''),
      IF(oi.ice IS NOT NULL, CONCAT(' | Ice: ', oi.ice), ''),
      IF(oi.notes <> '' AND oi.notes IS NOT NULL, CONCAT(' | Notes: ', oi.notes), '')
    )
    SEPARATOR '<br>'
  ) AS orderItems
FROM orders o
JOIN payments p    ON o.orderID = p.orderID
JOIN orderitems oi ON oi.orderID = o.orderID
LEFT JOIN products pr ON pr.productID = oi.productID
LEFT JOIN users u  ON u.userID = o.userID
WHERE o.status = 'completed' 
OR o.status = 'cancelled'
GROUP BY
  o.orderID,
  o.orderNumber,
  o.orderDate,
  o.totalAmount,
  p.paymentMethod,
  p.paymentStatus,
  o.customerName,
  u.username
ORDER BY o.orderDate DESC;
";
$transactionResult = mysqli_query($conn, $transactionHistory);

// Fetch all categories for filter dropdown
$categoriesQuery = "SELECT categoryID, categoryName FROM categories ORDER BY categoryName ASC";
$categoriesResult = mysqli_query($conn, $categoriesQuery);
$categories = [];
while ($row = mysqli_fetch_assoc($categoriesResult)) {
    $categories[] = $row;
}

// Capture filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category_filter']) ? intval($_GET['category_filter']) : '';
$price_sort = isset($_GET['price_sort']) ? strtolower($_GET['price_sort']) : '';

// Validate inputs
if (!in_array($price_sort, ['high', 'low']))
    $price_sort = '';

// Build WHERE clause
$where = [];
$params = [];
$types = '';

if ($search) {
    $where[] = "(pr.productName LIKE ? OR c.categoryName LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

// Add category filter
if ($category_filter) {
    $where[] = "c.categoryID = ?";
    $params[] = $category_filter;
    $types .= "i";
}

$whereSql = $where ? "AND " . implode(" AND ", $where) : "";

// Default ordering
$orderBy = "ORDER BY total_quantity DESC";

if ($price_sort === 'high') {
    $orderBy = "ORDER BY pr.price DESC";
} elseif ($price_sort === 'low') {
    $orderBy = "ORDER BY pr.price ASC";
}

$weekStart = date('M j', strtotime('monday this week'));
$weekEnd = date('M j, Y', strtotime('sunday this week'));

// Final SQL
$sql = "
    SELECT 
        pr.productName AS item_name,
        c.categoryName AS category,
        pr.price AS price_each,
        SUM(oi.quantity) AS total_quantity,
        SUM(oi.quantity * pr.price) AS total_sales,
        pr.productID,
        MAX(o.orderDate) AS last_order_date
    FROM orderitems oi
    JOIN products pr ON oi.productID = pr.productID
    JOIN categories c ON pr.categoryID = c.categoryID
    JOIN orders o ON oi.orderID = o.orderID
    WHERE o.status = 'completed'
      AND o.orderID IN (SELECT orderID FROM payments WHERE paymentStatus = 'paid')
    $whereSql
    GROUP BY pr.productID, pr.productName, c.categoryName, pr.price
    $orderBy
";

// Prepare & execute
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$productResult = $stmt->get_result();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales and Report</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/sales-and-report.css">
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
                <a href="inventory-management.php" class="admin-nav-link">
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
                <a href="sales-and-report.php" class="admin-nav-link active">
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
            <a href="inventory-management.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
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
            <a href="sales-and-report.php" class="admin-nav-link active wow animate__animated animate__fadeInLeft"
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
            <a href="logout.php" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.45s">
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
                <div class="row align-items-center mb-4">
                    <div class="col-12 col-lg-6 text-center text-lg-start mb-3 mb-lg-0">
                        <h1 class="page-title pt-lg-4 pt-0">Sales & Reports</h1>
                    </div>

                    <!-- Desktop & Tablet Stats Cards -->
                    <div class="col-12 col-lg-6">
                        <div class="row g-2">
                            <div class="col-12 col-md-4">
                                <div class="stat-card">
                                    <div class="stat-number">₱<?php echo number_format($weeklyTotal, 0); ?></div>
                                    <div class="stat-label">Weekly Sales</div>
                                    <div class="stat-subtext text-muted small">
                                        <?php echo $weekStart . ' – ' . $weekEnd; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="stat-card">
                                    <div class="stat-number">₱<?php echo number_format($monthlyTotal, 0); ?></div>
                                    <div class="stat-label">Monthly Sales</div>
                                    <div class="stat-subtext text-muted small"><?php echo date('F'); ?></div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="stat-card">
                                    <div class="stat-number"><?php echo $totalItemsSoldCount; ?></div>
                                    <div class="stat-label">Items Sold</div>
                                    <div class="stat-subtext text-muted small">This Week</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Overview Section -->
            <div class="sales-overview-section mb-4">
                <div class="row g-3">
                    <!-- Metrics Cards -->
                    <div class="col-12 col-lg-4">
                        <div class="metrics-grid">
                            <div class="metric-card primary">
                                <div class="metric-icon">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-label">Avg Order Value</div>
                                    <div class="metric-value">₱<?php echo number_format($averageOrderValue, 2); ?></div>
                                    <div class="metric-period">Today</div>
                                </div>
                            </div>

                            <div class="metric-card secondary">
                                <div class="metric-icon">
                                    <i class="bi bi-trophy"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-label">Top Product</div>
                                    <div class="metric-value-text">
                                        <?php
                                        if (!empty($topProductsData)) {
                                            $topProduct = $topProductsData[0];
                                            echo htmlspecialchars($topProduct['productName']);
                                        } else {
                                            echo "No sales";
                                        }
                                        ?>
                                    </div>
                                    <div class="metric-period">
                                        <?php
                                        if (!empty($topProductsData)) {
                                            echo "(" . $topProductsData[0]['total_qty_sold'] . " sold)";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="metric-card accent">
                                <div class="metric-icon">
                                    <i class="bi bi-eye"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-label">Website Visits</div>
                                    <div class="metric-value"><?php echo $todayVisits; ?></div>
                                    <div class="metric-period">Today</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Chart -->
                    <div class="col-12 col-lg-8">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h5 class="chart-title">
                                    <i class="bi bi-graph-up me-2"></i>Sales Performance
                                </h5>
                                <span class="chart-subtitle">Last 30 Days</span>
                            </div>
                            <div class="chart-body">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Section -->
            <div class="transactions-section mb-4">
                <div class="section-header-bar">
                    <h5 class="section-title">
                        <i class="bi bi-clock-history me-2"></i>Recent Transactions
                    </h5>
                    <button class="btn action-btn export-btn" type="button" data-bs-toggle="modal"
                        data-bs-target="#confirmModal">
                        <i class="bi bi-download"></i>
                        <span class="ms-1">Export</span>
                    </button>
                </div>

                <div class="table-card">
                    <div class="table-responsive custom-scroll">
                        <table class="table sales-table">
                            <thead class="table-header">
                                <tr>
                                    <th>Date</th>
                                    <th>Order No.</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Customer</th>
                                </tr>
                            </thead>
                            <tbody class="table-body">
                                <?php
                                if (mysqli_num_rows($transactionResult) > 0) {
                                    while ($row = mysqli_fetch_assoc($transactionResult)) {
                                        $statusClass = strtolower($row['paymentStatus']) === 'paid' ? 'paid' : 'unpaid';
                                        ?>
                                        <tr>
                                            <td class="date-cell">
                                                <div class="date-content">
                                                    <span
                                                        class="date-value"><?= date('M d, Y', strtotime($row['orderDate'])) ?></span>
                                                    <span
                                                        class="time-value"><?= date('H:i', strtotime($row['orderDate'])) ?></span>
                                                </div>
                                            </td>
                                            <td class="order-number"><?= htmlspecialchars($row['orderNumber']) ?></td>
                                            <td class="items-cell">
                                                <div class="items-preview"><?= $row['orderItems'] ?></div>
                                            </td>
                                            <td class="amount-cell">₱<?= number_format($row['totalAmount'], 2) ?></td>
                                            <td class="payment-method"><?= htmlspecialchars($row['paymentMethod']) ?></td>
                                            <td class="status-cell">
                                                <span class="status-badge <?= $statusClass ?>"><?= ucfirst($row['paymentStatus']) ?></span>
                                            </td>
                                            <td class="customer-name"><?= htmlspecialchars($row['displayName']) ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center py-4'>No transactions found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Product Sales Report Section -->
            <div class="product-sales-section">
                <div class="section-header-bar">
                    <h5 class="section-title">
                        <i class="bi bi-box-seam me-2"></i>Product Sales Report
                    </h5>
                </div>

                <!-- Filters -->
                <div class="filter-bar">
                    <form method="GET" action="" class="w-100">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-6">
                                <label class="filter-label">Search Product</label>
                                <input class="filter-input" type="text" name="search"
                                    placeholder="Search product or category..."
                                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                            </div>

                            <div class="col-6 col-md-2">
                                <label class="filter-label">Category</label>
                                <select class="filter-select" name="category_filter">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['categoryID'] ?>" 
                                            <?= $category_filter == $cat['categoryID'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['categoryName']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-6 col-md-2">
                                <label class="filter-label">Price</label>
                                <select class="filter-select" name="price_sort">
                                    <option value="">Default</option>
                                    <option value="high" <?= $price_sort === 'high' ? 'selected' : '' ?>>High to Low
                                    </option>
                                    <option value="low" <?= $price_sort === 'low' ? 'selected' : '' ?>>Low to High</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-2 d-flex gap-2">
                                <button type="submit" class="btn filter-apply-btn flex-fill">Apply</button>
                                <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>"
                                    class="btn btn-clear flex-fill">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Product Sales Table -->
                <div class="table-card">
                    <div class="table-responsive custom-scroll">
                        <table class="table sales-table">
                            <thead class="table-header">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Quantity Sold</th>
                                    <th>Total Sales</th>
                                    <th>Product ID</th>
                                </tr>
                            </thead>
                            <tbody class="table-body">
                                <?php
                                if (mysqli_num_rows($productResult) > 0) {
                                    while ($row = mysqli_fetch_assoc($productResult)) {
                                        ?>
                                        <tr>
                                            <td class="product-name"><?= htmlspecialchars($row['item_name']) ?></td>
                                            <td class="category-name"><?= htmlspecialchars($row['category']) ?></td>
                                            <td class="price-cell">₱<?= number_format($row['price_each'], 2) ?></td>
                                            <td class="quantity-cell"><?= (int) $row['total_quantity'] ?></td>
                                            <td class="amount-cell">₱<?= number_format($row['total_sales'], 2) ?></td>
                                            <td class="product-id"><?= htmlspecialchars($row['productID']) ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center py-4'>No product sales found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div id="modal-placeholder"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Load modal HTML
        fetch("../modal/sales-and-report-modal.php")
            .then(res => res.text())
            .then(data => {
                document.getElementById("modal-placeholder").innerHTML = data;

                // Attach event listener
                const exportBtn = document.querySelector('.addbtn.btnDownload');
                if (exportBtn) {
                    exportBtn.addEventListener('click', function (e) {
                        e.preventDefault();

                        // Show loading state
                        exportBtn.disabled = true;
                        const originalText = exportBtn.innerHTML;
                        exportBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Exporting...';

                        setTimeout(() => {
                            downloadReportCSV();

                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
                            if (modal) modal.hide();

                            // Show toast
                            const toast = new bootstrap.Toast(document.getElementById('orderToast'));
                            toast.show();

                            // Reset button
                            setTimeout(() => {
                                exportBtn.disabled = false;
                                exportBtn.innerHTML = originalText;
                            }, 500);
                        }, 1000);
                    });
                }
            });

        function downloadReportCSV() {
            let csvContent = "\uFEFF"; // Add UTF-8 BOM

            // Add Summary Section
            csvContent += "SALES SUMMARY\n";
            csvContent += "Metric,Value\n";
            csvContent += "Weekly Total Sales,PHP <?= number_format($weeklyTotal, 2); ?>\n";
            csvContent += "Monthly Total Sales,PHP <?= number_format($monthlyTotal, 2); ?>\n";
            csvContent += "Average Order Value,PHP <?= number_format($averageOrderValue, 2); ?>\n";
            csvContent += "Top Product,<?= !empty($topProductsData) ? htmlspecialchars($topProductsData[0]['productName']) : 'No sales'; ?>\n";
            csvContent += "Total Items Sold (This Week),<?= $totalItemsSoldCount ?>\n";
            csvContent += "Website Visits (Today),<?= $todayVisits ?>\n\n\n";

            // Add Recent Transactions
            csvContent += "RECENT TRANSACTIONS\n";
            const transactionTable = document.querySelector(".transactions-section table");
            if (transactionTable) {
                const rows = transactionTable.querySelectorAll("tr");
                rows.forEach((row) => {
                    const cells = row.querySelectorAll("th, td");
                    const rowData = [];
                    cells.forEach(cell => {
                        let text = cell.innerText.replace(/₱/g, 'PHP ').replace(/"/g, '""').replace(/\n/g, ' ');
                        rowData.push('"' + text + '"');
                    });
                    csvContent += rowData.join(",") + "\n";
                });
            }

            csvContent += "\n\n";

            // Add Sales Report
            csvContent += "PRODUCT SALES REPORT\n";
            const salesReportTable = document.querySelector(".product-sales-section table");
            if (salesReportTable) {
                const rows = salesReportTable.querySelectorAll("tr");
                rows.forEach((row) => {
                    const cells = row.querySelectorAll("th, td");
                    const rowData = [];
                    cells.forEach(cell => {
                        let text = cell.innerText.replace(/₱/g, 'PHP ').replace(/"/g, '""').replace(/\n/g, ' ');
                        rowData.push('"' + text + '"');
                    });
                    csvContent += rowData.join(",") + "\n";
                });
            }

            // Create download link
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "sales_and_report.csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Toast Notification System
        function showToast(message, type = 'success') {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();

            const toastHtml = `
                <div class="toast ${type}" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}" data-bs-autohide="true" data-bs-delay="5000">
                    <div class="toast-header">
                        <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} me-2"></i>
                        <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
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

        // Chart.js Sales Chart
        const labels = <?php echo json_encode($salesDates); ?>;
        const dataValues = <?php echo json_encode($salesTotals); ?>;

        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Daily Sales (₱)',
                    data: dataValues,
                    fill: true,
                    borderColor: "#C4A277",
                    backgroundColor: "rgba(196, 162, 119, 0.1)",
                    borderWidth: 3,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: "#C4A277",
                    pointBorderColor: "#fff",
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            font: {
                                family: 'Poppins',
                                size: 12
                            },
                            color: '#2E1A00'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(46, 26, 0, 0.9)',
                        titleFont: {
                            family: 'Poppins',
                            size: 13
                        },
                        bodyFont: {
                            family: 'ABeeZee',
                            size: 12
                        },
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: "Date",
                            font: {
                                family: 'Poppins',
                                size: 12,
                                weight: '600'
                            },
                            color: '#2E1A00'
                        },
                        ticks: {
                            font: {
                                family: 'ABeeZee',
                                size: 11
                            },
                            color: '#666'
                        },
                        grid: {
                            color: 'rgba(196, 162, 119, 0.1)'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "Sales (₱)",
                            font: {
                                family: 'Poppins',
                                size: 12,
                                weight: '600'
                            },
                            color: '#2E1A00'
                        },
                        ticks: {
                            font: {
                                family: 'ABeeZee',
                                size: 11
                            },
                            color: '#666',
                            callback: function (value) {
                                return '₱' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(196, 162, 119, 0.1)'
                        }
                    }
                }
            }
        });

        window.addEventListener("resize", () => {
            salesChart.resize();
        });


        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }
    </script>

</body>

</html>