<?php
include '../assets/connect.php';
session_start();

// Prevent unauthorized access
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin') {
    // Redirect non-admin users to login page or a "no access" page


    header("Location: login.php");
    exit();
}

// Daily total sales for the past 30 days
$dailySales = "
    SELECT 
        DATE(o.orderDate) AS sale_date,
        SUM(o.totalAmount) AS total_sales
    FROM orders o
    JOIN payments p ON o.orderID = p.orderID
    WHERE p.paymentStatus = 'paid'
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
  AND YEAR(o.orderDate) = YEAR(CURDATE())
  AND MONTH(o.orderDate) = MONTH(CURDATE())";
$monthlyResult = mysqli_query($conn, $monthlySales);
$monthlyRow = mysqli_fetch_assoc($monthlyResult);
$monthlyTotal = $monthlyRow['total_sales'] ?? 0;

// Avg order value
$avgOrderValue = "SELECT 
    ROUND(AVG(o.totalAmount), 2) AS avg_order_value
FROM orders o
JOIN payments p ON o.orderID = p.orderID
WHERE p.paymentStatus = 'paid'
  AND MONTH(o.orderDate) = MONTH(CURDATE())
  AND YEAR(o.orderDate) = YEAR(CURDATE());";
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
WHERE p.paymentStatus = 'paid'
  AND YEARWEEK(o.orderDate, 1) = YEARWEEK(CURDATE(), 1);
";

$totalItemsSoldResult = mysqli_query($conn, $totalItemsSold);
$totalItemsSoldRow = mysqli_fetch_assoc($totalItemsSoldResult);
$totalItemsSoldCount = $totalItemsSoldRow['total_items_sold'] ?? 0;

// Today's website visits

$dailyVisits = "SELECT 
    visitDate,
    COUNT(*) AS dailyVisits
FROM visits
WHERE visitDate = CURDATE()
GROUP BY visitDate
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
WHERE p.paymentStatus = 'paid'
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

// Product Sales Data
$productSales = "
SELECT 
    pr.productID,
    pr.productName AS item_name,
    c.categoryName AS category,
    pr.price AS price_each,
    SUM(oi.quantity) AS total_quantity,
    (SUM(oi.quantity) * pr.price) AS total_sales
FROM orderitems oi
JOIN products pr ON oi.productID = pr.productID
LEFT JOIN categories c ON pr.categoryID = c.categoryID
JOIN orders o ON oi.orderID = o.orderID
JOIN payments p ON o.orderID = p.orderID
WHERE p.paymentStatus = 'paid'
GROUP BY pr.productID, pr.productName, c.categoryName, pr.price
ORDER BY total_quantity DESC;
";
$productResult = mysqli_query($conn, $productSales);

// Capture filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_sort = isset($_GET['category_sort']) ? strtolower($_GET['category_sort']) : '';
$price_sort = isset($_GET['price_sort']) ? strtolower($_GET['price_sort']) : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';

// Validate inputs
$allowed = ['asc', 'desc', 'high', 'low'];
if (!in_array($category_sort, ['asc', 'desc']))
    $category_sort = '';
if (!in_array($price_sort, ['high', 'low']))
    $price_sort = '';
if ($date_filter && !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date_filter))
    $date_filter = '';

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

if ($date_filter) {
    $where[] = "DATE(o.orderDate) = ?";
    $params[] = $date_filter;
    $types .= "s";
}

$whereSql = $where ? "AND " . implode(" AND ", $where) : "";

// Default ordering
$orderBy = "ORDER BY total_quantity DESC";

if ($category_sort) {
    $orderBy = "ORDER BY c.categoryName " . strtoupper($category_sort);
} elseif ($price_sort === 'high') {
    $orderBy = "ORDER BY pr.price DESC";
} elseif ($price_sort === 'low') {
    $orderBy = "ORDER BY pr.price ASC";
}

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
    <!-- Mobile Menu Toggle Button  -->
    <div class="d-md-none mobile-header d-flex align-items-center p-3">
        <button id="menuToggle" class="mobile-menu-toggle me-3">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="mobile-header-title">Sales and Report</h4>
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
                <!-- Header Row -->
                <div class="d-none d-md-flex align-items-center justify-content-between pt-4 px-lg-3 px-2">
                    <!-- Title -->
                    <div class="subheading fw-bold m-1">
                        <span style="color: var(--text-color-dark);">Sales and Report</span>
                    </div>

                    <!-- Export Button -->
                    <div class="ms-auto">
                        <button class="btn excelBtn" type="button" onclick="openPopup()">
                            Export
                        </button>
                    </div>
                </div>


                <div id="modal-placeholder"></div>

                <div class="container-fluid">
                    <div class="row g-3 align-items-start">
                        <div class="col-12 col-lg-4">
                            <div id="smallCardCarousel" class="carousel slide " data-bs-ride="false">
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#smallCardCarousel" data-bs-slide-to="0"
                                        class="active"></button>
                                    <button type="button" data-bs-target="#smallCardCarousel"
                                        data-bs-slide-to="1"></button>
                                    <button type="button" data-bs-target="#smallCardCarousel"
                                        data-bs-slide-to="2"></button>
                                </div>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <div class="d-flex flex-column align-items-center my-3">
                                            <div class="card cardSmall m-2 fw-bolder p-3"
                                                style="background-color:#C4A277; color:aliceblue">
                                                <div class="text-center">
                                                    <div class="sales-label fw-semibold">Total Sales:</div>
                                                    <div class="sales-amount mt-2 fs-4">
                                                        ₱<?php echo number_format($weeklyTotal, 2); ?>
                                                    </div>
                                                    <div class="sales-period mt-2">This week</div>
                                                </div>
                                            </div>

                                            <div class="card cardSmall m-2 fw-bolder p-3">
                                                <div class="text-center">
                                                    <div class="sales-label fw-semibold">Total Sales:</div>
                                                    <div class="sales-amount mt-2 fs-4">
                                                        ₱<?php echo number_format($monthlyTotal, 2); ?>

                                                    </div>
                                                    <div class="sales-period mt-2">This month</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item">
                                        <div class="d-flex flex-column align-items-center my-3">
                                            <div class="card cardSmall m-2 fw-bolder p-3"
                                                style="background-color:#C4A277; color:aliceblue">
                                                <div class="text-center">
                                                    <div class="sales-label fw-semibold">Most Popular:</div>
                                                    <div class="sales-amount mt-2 fs-5">
                                                        <?php
                                                        if (!empty($topProductsData)) {
                                                            $topProduct = $topProductsData[0];
                                                            echo "<div>" . htmlspecialchars($topProduct['productName']) . "</div>";
                                                            echo "<div>(" . $topProduct['total_qty_sold'] . " sold)</div>";
                                                        } else {
                                                            echo "No sales this week";
                                                        }

                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card cardSmall m-2 fw-bolder p-3">
                                                <div class="text-center">
                                                    <div class="sales-label fw-semibold">Avg Order Value:</div>
                                                    <div class="sales-amount mt-2 fs-4">
                                                        ₱<?php echo number_format($averageOrderValue, 2); ?>
                                                    </div>
                                                    <div class="sales-period mt-2">This month</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item">
                                        <div class="d-flex flex-column align-items-center my-3">

                                            <div class="card cardSmall m-2 fw-bolder p-3"
                                                style="background-color:#C4A277; color:aliceblue">
                                                <div class="text-center">
                                                    <div class="sales-label fw-semibold">Total Products Sold:</div>
                                                    <div class="sales-amount mt-2 fs-4">
                                                        <?php echo $totalItemsSoldCount ?>
                                                    </div>
                                                    <div class="sales-period mt-2">This week</div>
                                                </div>
                                            </div>

                                            <div class="card cardSmall m-2 fw-bolder p-3">
                                                <div class="text-center">
                                                    <div class="sales-label fw-semibold">Website Visits:</div>
                                                    <div class="sales-amount mt-2 fs-4">
                                                        <?php echo $todayVisits ?>
                                                    </div>
                                                    <div class="sales-period mt-2">Today</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Carousel controls -->
                                <button class="carousel-control-prev" type="button" data-bs-target="#smallCardCarousel"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#smallCardCarousel"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="col-12 col-lg-8">
                            <div class="row d-flex flex-wrap justify-content-center">
                                <div class="card cardBig flex-grow-1 m-2">
                                    <div class="card-body text-start">
                                        <div class="subheading">Product Statistics</div>
                                        <span class="text-muted">Track product sales</span>
                                        <div class="cardStats mt-3">
                                            <div class="card-body p-0">
                                                <!-- Chart.js Canvas -->
                                                <div class="chart-container">
                                                    <canvas id="salesChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="card cardOrders rounded-3 px-4 mt-5">
                            <div class="card-body p-0">
                                <div class="subheading fs-4 mb-3 text-center">Recent Transactions</div>
                                <div class="table-responsive custom-scroll"
                                    style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-bordered table-hover">
                                        <thead class="text-center">
                                            <tr>
                                                <th>Date</th>
                                                <th>Order No.</th>
                                                <th>Order Items</th>
                                                <th>Total (₱)</th>
                                                <th>Payment Method</th>
                                                <th>Status</th>
                                                <th>Customer Name</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center align-middle"
                                            style="font-size: 0.50rem; line-height: 1.2; padding: 0 !important;">
                                            <?php
                                            if (mysqli_num_rows($transactionResult) > 0) {
                                                while ($row = mysqli_fetch_assoc($transactionResult)) {
                                                    ?>
                                                    <tr style="font-size: 0.50rem; line-height: 1.1; padding: 0 !important;">
                                                        <td class="p-0"><?= date('M d, Y', strtotime($row['orderDate'])) ?><br>
                                                            <?= date('H:i', strtotime($row['orderDate'])) ?>
                                                        </td>
                                                        <td class="p-1"><?= htmlspecialchars($row['orderNumber']) ?></td>
                                                        <td class="p-1"><?= $row['orderItems'] ?></td>
                                                        <td class="p-1">₱<?= number_format($row['totalAmount'], 2) ?></td>
                                                        <td class="p-1"><?= htmlspecialchars($row['paymentMethod']) ?></td>
                                                        <td class="p-1"><?= ucfirst($row['paymentStatus']) ?></td>
                                                        <td class="p-1"><?= htmlspecialchars($row['displayName']) ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                echo "<tr><td colspan='7' class='text-center'>No transactions found</td></tr>";
                                            }
                                            ?>
                                        </tbody>

                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="card cardContainer m-2">
                            <div class="card-body py-0 px-3 text-center">
                                <div class="subheading fs-4 mb-3">Sales Report</div>
                                <form method="GET" action="">
                                    <div class="row g-3 justify-content-center">
                                        <!-- Search Bar -->
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <input class="form-control" type="text" name="search" placeholder="Search"
                                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                                        </div>

                                        <!-- Category Sorting -->
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <div class="dropdown-center">
                                                <button class="btn btn-dropdown dropdown-toggle fw-semibold"
                                                    type="button" data-bs-toggle="dropdown">
                                                    Category
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><button class="dropdown-item" type="submit" name="category_sort"
                                                            value="asc">A → Z</button></li>
                                                    <li><button class="dropdown-item" type="submit" name="category_sort"
                                                            value="desc">Z → A</button></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Price Sorting -->
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <div class="dropdown-center">
                                                <button class="btn btn-dropdown dropdown-toggle fw-semibold"
                                                    type="button" data-bs-toggle="dropdown">
                                                    Price
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><button class="dropdown-item" type="submit" name="price_sort"
                                                            value="high">High to Low</button></li>
                                                    <li><button class="dropdown-item" type="submit" name="price_sort"
                                                            value="low">Low to High</button></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Date Picker -->
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <input type="date" class="form-control" name="date_filter"
                                                value="<?= isset($_GET['date_filter']) ? htmlspecialchars($_GET['date_filter']) : '' ?>">
                                        </div>

                                        <!-- Apply + Clear Buttons -->
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2 d-flex gap-2">
                                            <button type="submit" class="btn excelBtn flex-fill">Apply</button>
                                            <a href="<?= strtok($_SERVER["REQUEST_URI"], '?') ?>"
                                                class="btn btn-secondary flex-fill">Clear</a>
                                        </div>
                                    </div>
                                </form>


                            </div>
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="card cardOrders rounded-3 m-3" style="min-height: 30vh;">
                                        <div class="card-body">
                                            <div class="table-responsive custom-scroll"
                                                style="max-height: 400px; overflow-y: auto;">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr></tr>
                                                        <th>Item Name</th>
                                                        <th>Category</th>
                                                        <th>Price (Each)</th>
                                                        <th>Quantity</th>
                                                        <th>Total</th>
                                                        <th>Product ID</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style="font-size: 0.50rem; line-height: 1.1; padding: 0 !important;">
                                                        <?php
                                                        if (mysqli_num_rows($productResult) > 0) {
                                                            while ($row = mysqli_fetch_assoc($productResult)) {
                                                                ?>
                                                                <tr style="font-size: 0.50rem; line-height: 1.1; padding: 0 !important;">
                                                                    <td class="p-1"><?= htmlspecialchars($row['item_name']) ?></td>
                                                                    <td class="p-1"><?= htmlspecialchars($row['category']) ?></td>
                                                                    <td class="p-1">₱<?= number_format($row['price_each'], 2) ?></td>
                                                                    <td class="p-1"><?= (int) $row['total_quantity'] ?></td>
                                                                    <td class="p-1">₱<?= number_format($row['total_sales'], 2) ?></td>
                                                                    <td class="p-1"><?= htmlspecialchars($row['productID']) ?></td>
                                                                </tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            echo "<tr><td colspan='7' class='text-center'>No product sales found</td></tr>";
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        fetch("../modal/sales-and-report-modal.php")
            .then(res => res.text())
            .then(data => {
                document.getElementById("modal-placeholder").innerHTML = data;
                document.querySelector('.addbtn').addEventListener('click', function (e) {
                    e.preventDefault();
                    confirmOrder();
                    downloadReportExcel();
                });
            });

        function openPopup() {
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }

        function confirmOrder() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();

            const toastElement = document.getElementById('orderToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }

        function downloadReportExcel() {
            const wb = XLSX.utils.book_new();

            // Export Recent Transactions Table
            const transactionTable = document.querySelector(".cardOrders table");
            if (transactionTable) {
                const ws1 = XLSX.utils.table_to_sheet(transactionTable);
                XLSX.utils.book_append_sheet(wb, ws1, "Transactions");
            }

            // Export Sales Report Table
            const salesReportTable = document.querySelector(".cardContainer table");
            if (salesReportTable) {
                const ws2 = XLSX.utils.table_to_sheet(salesReportTable);
                XLSX.utils.book_append_sheet(wb, ws2, "Sales Report");
            }

            // Export Small Card Statistics (weekly, monthly, etc.)
            const stats = [
                ["Weekly Total Sales", "₱<?= number_format($weeklyTotal, 2); ?>"],
                ["Monthly Total Sales", "₱<?= number_format($monthlyTotal, 2); ?>"],
                ["Average Order Value", "₱<?= number_format($averageOrderValue, 2); ?>"],
                ["Top Product", "<?= !empty($topProductsData) ? htmlspecialchars($topProductsData[0]['productName']) : 'No sales'; ?>"],
                ["Total Items Sold (This Week)", "<?= $totalItemsSoldCount ?>"],
                ["Website Visits (Today)", "<?= $todayVisits ?>"]
            ];
            const ws3 = XLSX.utils.aoa_to_sheet(stats);
            XLSX.utils.book_append_sheet(wb, ws3, "Summary");

            // Download Excel File
            XLSX.writeFile(wb, "sales_and_report.xlsx");
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
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
                    borderColor: "#9e6320ff",
                    backgroundColor: "#e6d1abff",
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    x: { title: { display: true, text: "Date" } },
                    y: { beginAtZero: true, title: { display: true, text: "Sales (₱)" } }
                }
            }
        });

        window.addEventListener("resize", () => {
            salesChart.resize();
        });

    </script>

</body>

</html>