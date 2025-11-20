<?php
include('auth_check.php');
include '../assets/connect.php';

// Check if user is logged in and is an admin 
if (!isset($_SESSION['userID']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: login.php");
    exit();
}

// Handle AJAX requests for polling
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'fetchOrders') {
        $status = isset($_GET['status']) ? $_GET['status'] : 'pending';
        echo getOrdersHTML($status);
        exit;
    } elseif ($_GET['action'] === 'getStatusCounts') {
        echo getStatusCountsHTML();
        exit;
    }
}

function showAlert($type, $message) {
    $icon = '';
    if ($type === 'success') {
        $icon = '<i class="bi bi-check-circle-fill" style="margin-right:8px;"></i>';
    } elseif ($type === 'danger') {
        $icon = '<i class="bi bi-exclamation-triangle-fill" style="margin-right:8px;"></i>';
    }

    echo '<div id="alertContainer">
            <div class="alert alert-' . $type . ' d-flex align-items-center">' 
                . $icon . $message . 
            '</div>
          </div>';
}

// Handle status updates
if (isset($_POST['update_status'])) {
    $orderID = mysqli_real_escape_string($conn, $_POST['orderID']);
    $newStatus = mysqli_real_escape_string($conn, $_POST['status']);
    
    $sql = "UPDATE orders SET status = '$newStatus' WHERE orderID = '$orderID'";
    
    if (executeQuery($sql)) {
        showAlert('success', 'Order status updated successfully!');
    } else {
        showAlert('danger', 'Failed to update order status.');
    }
    exit;
}

// Handle archive/delete completed orders
if (isset($_POST['archive_order'])) {
    $orderID = mysqli_real_escape_string($conn, $_POST['orderID']);
    
    $sql = "UPDATE orders SET isDone = 1 WHERE orderID = '$orderID'";
    
    if (executeQuery($sql)) {
        showAlert('success', 'Order archived successfully!');
    } else {
        showAlert('danger', 'Failed to archive order.');
    }
    exit;
}

// Function to get orders - only checked-out orders
function getOrdersData($status = 'pending') {
    global $conn;
    
    // Show only orders that are checked out (have payment and proper data)
    $whereClause = "WHERE o.isDone = 0 AND o.totalAmount > 0 AND o.status IS NOT NULL AND o.status != ''";
    $whereClause .= " AND o.status = '$status'";
    
    $sql = "SELECT 
                o.orderID, o.orderDate, o.customerName, o.orderContactNumber, 
                o.totalAmount, o.orderType, o.orderNumber, o.status,
                p.paymentMethod, p.paymentStatus, p.referenceNumber
            FROM orders o
            LEFT JOIN payments p ON o.orderID = p.orderID
            $whereClause
            ORDER BY o.orderDate DESC";
    
    return executeQuery($sql);
}

// Function to get order items
function getOrderItemsData($orderID) {
    global $conn;
    
    $orderID = mysqli_real_escape_string($conn, $orderID);
    $sql = "SELECT oi.*, pr.productName, pr.price 
            FROM orderitems oi
            LEFT JOIN products pr ON oi.productID = pr.productID
            WHERE oi.orderID = '$orderID'";
    
    return executeQuery($sql);
}

function getStatusIcon($status) {
    switch ($status) {
        case 'pending': return '<i class="bi bi-clock-history"></i>';
        case 'preparing': return '<i class="bi bi-gear"></i>';
        case 'ready': return '<i class="bi bi-box-seam"></i>';
        case 'completed': return '<i class="bi bi-check2-circle"></i>';
        case 'cancelled': return '<i class="bi bi-x-circle"></i>';
        default: return '';
    }
}

// Function to get status counts
function getStatusCountsData() {
    global $conn;
    
    $sql = "SELECT 
                SUM(status='pending') AS pending,
                SUM(status='preparing') AS preparing,
                SUM(status='ready') AS ready,
                SUM(status='completed') AS completed
            FROM orders
            WHERE isDone = 0 AND totalAmount > 0 AND status IS NOT NULL AND status != ''";
    
    $result = executeQuery($sql);
    return mysqli_fetch_assoc($result);
}

function generateOrderCard($order) {
    $items = getOrderItemsData($order['orderID']);
    $orderNumber = $order['orderNumber'] ?: str_pad($order['orderID'], 3, '0', STR_PAD_LEFT);
    
    $html = '<div class="modern-order-card" data-order-id="' . $order['orderID'] . '">
                <!-- Card Header -->
                <div class="card-header-gradient">
                    <div class="order-title-section">
                        <h3 class="order-number-modern">
                            Order #' . $orderNumber . '
                        </h3>
                        <div class="order-datetime">
                            <i class="bi bi-clock"></i>
                            <span>' . date('M j, Y g:i A', strtotime($order['orderDate'])) . '</span>
                        </div>
                    </div>
                    <div class="status-badge-modern">
                        <div class="status-indicator"></div>
                        <span>' . ucfirst($order['status']) . '</span>
                    </div>
                </div>

                <!-- Card Body with Horizontal Layout -->
                <div class="card-body-content">
                    
                    <!-- Left Section: Order Info & Customer Details -->
                    <div class="order-info-section">
                        <!-- Order Type & Payment Info -->
                        <div class="order-info-bar">
                            <div class="info-pill">
                                <i class="bi bi-shop"></i>
                                <span class="info-label">Type:</span>
                                <strong>' . ucfirst($order['orderType']) . '</strong>
                            </div>';
    
    if (!empty($order['paymentMethod'])) {
        $html .= '<div class="info-pill">
                    <i class="bi bi-wallet2"></i>
                    <span class="info-label">Payment:</span>
                    <strong>' . ucfirst($order['paymentMethod']) . '</strong>
                  </div>';
    }
    
    // GCash Reference Number - Show if payment method is GCash and reference number exists
   // GCash Reference Number - Show if payment method is GCash and reference number exists
if (strtolower($order['paymentMethod']) === 'gcash' && !empty($order['referenceNumber'])) {
    // Get last 4 digits and mask the rest
    $refNumber = $order['referenceNumber'];
    $maskedRef = str_repeat('*', max(0, strlen($refNumber) - 4)) . substr($refNumber, -4);
    
    $html .= '<div class="info-pill gcash-reference">
                <i class="bi bi-receipt"></i>
                <span class="info-label">Reference No.:</span>
                <strong> *********'. htmlspecialchars($maskedRef) . '</strong>
              </div>';
}
    
    $html .= '</div>'; 
    
    // Customer Info (if available and pickup order)
    if ($order['orderType'] === 'pickup' && (!empty($order['customerName']) || !empty($order['orderContactNumber']))) {
        $html .= '<div class="customer-card">
                    <div class="customer-card-header">
                        <i class="bi bi-person-circle"></i>
                        <span>Customer Info</span>
                    </div>
                    <div class="customer-info-grid">';
        
        if (!empty($order['customerName'])) {
            $html .= '<div class="customer-detail">
                        <div class="detail-icon"><i class="bi bi-person"></i></div>
                        <span class="detail-text">' . htmlspecialchars($order['customerName']) . '</span>
                      </div>';
        }
        
        if (!empty($order['orderContactNumber'])) {
            $html .= '<div class="customer-detail">
                        <div class="detail-icon"><i class="bi bi-telephone"></i></div>
                        <span class="detail-text">' . htmlspecialchars($order['orderContactNumber']) . '</span>
                      </div>';
        }
        
        $html .= '</div></div>'; 
    }
    
    $html .= '</div>'; 
    
    // Center Section: Items List
    $html .= '<div class="items-container">
                <div class="items-header">
                    <div>
                        <i class="bi bi-bag-check-fill"></i>
                        <span>Items</span>
                    </div>
                    <div class="items-count">' . mysqli_num_rows($items) . '</div>
                </div>
                <div class="items-list-modern">';
    
    mysqli_data_seek($items, 0); 
    while ($item = mysqli_fetch_assoc($items)) {
        $html .= '<div class="modern-item-row">
                    <div class="item-info-section">
                        <div class="quantity-badge">' . $item['quantity'] . '</div>
                        <div class="item-content">
                            <div class="item-name-modern">' . htmlspecialchars($item['productName']) . '</div>';
        
        // Item customizations
        if (!empty($item['sugar']) || !empty($item['ice'])) {
            $html .= '<div class="item-customizations">';
            if (!empty($item['sugar'])) {
                $html .= '<span class="custom-tag">' . htmlspecialchars($item['sugar']) . '</span>';
            }
            if (!empty($item['ice'])) {
                $html .= '<span class="custom-tag">' . htmlspecialchars($item['ice']) . '</span>';
            }
            $html .= '</div>';
        }
        
        // Special notes
        if (!empty($item['notes'])) {
            $html .= '<div class="item-special-note">
                        <i class="bi bi-chat-square-quote"></i>
                        <span>' . htmlspecialchars($item['notes']) . '</span>
                      </div>';
        }
        
        $html .= '</div>
                    </div>
                    <div class="item-price-modern">₱' . number_format($item['price'] * $item['quantity'], 2) . '</div>
                </div>';
    }
    
    $html .= '</div>'; 
    $html .= '</div>'; 
    
    // Right Section: Total & Actions
    $html .= '<div class="total-actions-section">
                <!-- Total Section -->
                <div class="total-section-modern">
                    <div class="total-text">Total Amount</div>
                    <div class="total-price">₱' . number_format($order['totalAmount'], 2) . '</div>
                </div>
                
                <!-- Action Controls -->
                <div class="card-actions">';
    
    if ($order['status'] === 'completed') {
        // Completed orders - Archive button only
        $html .= '<button class="action-btn archive-action" onclick="showArchiveModal(' . $order['orderID'] . ', \'' . $orderNumber . '\')">
                    <i class="bi bi-archive"></i>
                    <span>Archive</span>
                  </button>';
    } else {
        // Status dropdown with conditional options based on current status
        $html .= '
            <div class="dropdown w-100">
                <button class="btn status-selector-modern dropdown-toggle w-100" type="button" id="statusDropdown' . $order['orderID'] . '" data-bs-toggle="dropdown" aria-expanded="false">
                    ' . getStatusIcon($order['status']) . ' ' . ucfirst($order['status']) . '
                </button>
                <ul class="dropdown-menu w-100" aria-labelledby="statusDropdown' . $order['orderID'] . '">';
        
        // Status flow logic
        if ($order['status'] === 'pending') {
            // Pending: Can only go to Preparing or Cancel
            $html .= '<li><a class="dropdown-item" href="#" onclick="updateOrderStatus(' . $order['orderID'] . ', \'preparing\')"><i class="bi bi-gear"></i> Preparing</a></li>';
            $html .= '<li><a class="dropdown-item dropdown-item-danger" href="#" onclick="updateOrderStatus(' . $order['orderID'] . ', \'cancelled\')"><i class="bi bi-x-circle"></i> Cancel</a></li>';
        } 
        elseif ($order['status'] === 'preparing') {
            // Preparing: Can only go to Ready
            $html .= '<li><a class="dropdown-item" href="#" onclick="updateOrderStatus(' . $order['orderID'] . ', \'ready\')"><i class="bi bi-box-seam"></i> Ready</a></li>';
        } 
        elseif ($order['status'] === 'ready') {
            // Ready: Can go to Completed or back to Preparing
            $html .= '<li><a class="dropdown-item" href="#" onclick="updateOrderStatus(' . $order['orderID'] . ', \'preparing\')"><i class="bi bi-gear"></i> Preparing</a></li>';
            $html .= '<li><a class="dropdown-item" href="#" onclick="updateOrderStatus(' . $order['orderID'] . ', \'completed\')"><i class="bi bi-check2-circle"></i> Completed</a></li>';
        }
        
        $html .= '</ul>
            </div>';
    }
    
    $html .= '</div>'; 
    $html .= '</div>'; 
    $html .= '</div>'; 
    $html .= '</div>'; 
    
    return $html;
}

function getOrdersHTML($status = 'pending') {
    $orders = getOrdersData($status);
    $html = '';
    
    if (mysqli_num_rows($orders) > 0) {
        $html .= '<div class="orders-grid">';
        while ($order = mysqli_fetch_assoc($orders)) {
            $html .= generateOrderCard($order);
        }
        $html .= '</div>';
    } else {
        $statusText = 'No ' . $status . ' orders found.';
        $html = '<div class="empty-state">
                    <div class="empty-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <h4 class="empty-title">No Orders Found</h4>
                    <p class="empty-text">' . $statusText . '</p>
                </div>';
    }
    
    return $html;
}

function getStatusCountsHTML() {
    $counts = getStatusCountsData();
    
    $html = '<button class="filter-btn active" onclick="filterOrders(\'pending\')" data-status="pending">
                <i class="bi bi-clock-history"></i>
                <span class="filter-text">Pending</span>
                <span class="count-badge">' . $counts['pending'] . '</span>
             </button>
             <button class="filter-btn" onclick="filterOrders(\'preparing\')" data-status="preparing">
                <i class="bi bi-gear-fill"></i>
                <span class="filter-text">Preparing</span>
                <span class="count-badge">' . $counts['preparing'] . '</span>
             </button>
             <button class="filter-btn" onclick="filterOrders(\'ready\')" data-status="ready">
                <i class="bi bi-check-circle"></i>
                <span class="filter-text">Ready</span>
                <span class="count-badge">' . $counts['ready'] . '</span>
             </button>
             <button class="filter-btn" onclick="filterOrders(\'completed\')" data-status="completed">
                <i class="bi bi-check-all"></i>
                <span class="filter-text">Completed</span>
                <span class="count-badge">' . $counts['completed'] . '</span>
             </button>';
    
    return $html;
}

$currentFilter = 'pending';
$orders = getOrdersData($currentFilter);
$statusCounts = getStatusCountsData();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Management</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/orders.css">
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
    <!-- Mobile Menu Toggle Button -->
    <div class="d-md-none mobile-header d-flex align-items-center pt-3 px-3">
        <button id="menuToggle" class="mobile-menu-toggle me-3">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Desktop Sidebar -->
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
                <a href="orders.php" class="admin-nav-link active">
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
            <a href="index.php" class="admin-nav-link">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="orders.php" class="admin-nav-link active">
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

            <!-- FINANCIAL Section -->
            <div class="section-header">Financial</div>
            <a href="sales-and-report.php" class="admin-nav-link">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Sales & Reports</span>
            </a>

            <!-- TOOLS Section -->
            <div class="section-header">Tools</div>
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

    <!-- Alert Container for AJAX responses -->
    <div id="alertContainer"></div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Enhanced Header -->
            <div class="enhanced-page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h1 class="page-title-text">Order Management</h1>
                    </div>
                    <div class="header-stats">
                        <div class="stat-item">
                            <span class="stat-number" id="totalOrders"><?php echo array_sum($statusCounts); ?></span>
                            <span class="stat-label">Total Orders</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Filter Section -->
            <div class="enhanced-filter-section">
                <div class="filter-container" id="filterTabs">
                    <button class="filter-btn active" onclick="filterOrders('pending')" data-status="pending">
                        <i class="bi bi-clock-history"></i>
                        <span class="filter-text">Pending</span>
                        <span class="count-badge"><?php echo $statusCounts['pending']; ?></span>
                    </button>
                    <button class="filter-btn" onclick="filterOrders('preparing')" data-status="preparing">
                        <i class="bi bi-gear-fill"></i>
                        <span class="filter-text">Preparing</span>
                        <span class="count-badge"><?php echo $statusCounts['preparing']; ?></span>
                    </button>
                    <button class="filter-btn" onclick="filterOrders('ready')" data-status="ready">
                        <i class="bi bi-check-circle"></i>
                        <span class="filter-text">Ready</span>
                        <span class="count-badge"><?php echo $statusCounts['ready']; ?></span>
                    </button>
                    <button class="filter-btn" onclick="filterOrders('completed')" data-status="completed">
                        <i class="bi bi-check-all"></i>
                        <span class="filter-text">Completed</span>
                        <span class="count-badge"><?php echo $statusCounts['completed']; ?></span>
                    </button>
                </div>
            </div>

            <!-- Enhanced Orders Container -->
            <div class="enhanced-orders-container">
                <div class="orders-content" id="ordersContainer">
                    <?php echo getOrdersHTML($currentFilter); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Archive Modal -->
    <?php include '../modal/archive-order-modal.php'; ?>

    <!-- JavaScript -->
    <script>
        let currentFilter = 'pending';
        let pollingInterval;

        // Filter orders function
        function filterOrders(status) {
            currentFilter = status;
            
            // Update active filter button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-status="${status}"]`).classList.add('active');
            
            // Show loading state
            document.getElementById('ordersContainer').innerHTML = '<div class="loading-state"><div class="spinner"></div><p>Loading orders...</p></div>';
            
            // Fetch orders for selected status
            fetchOrders(status);
        }

        // Fetch orders via AJAX
        function fetchOrders(status = 'pending') {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'orders.php?action=fetchOrders&status=' + status, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('ordersContainer').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Update status counts
        function updateStatusCounts() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'orders.php?action=getStatusCounts', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('filterTabs').innerHTML = xhr.responseText;
                    
                    // Reapply active class to current filter
                    document.querySelector(`[data-status="${currentFilter}"]`).classList.add('active');
                    
                    // Update total count
                    updateTotalCount();
                }
            };
            xhr.send();
        }

        // Update total count
        function updateTotalCount() {
            const badges = document.querySelectorAll('.count-badge');
            let total = 0;
            badges.forEach(badge => {
                total += parseInt(badge.textContent) || 0;
            });
            document.getElementById('totalOrders').textContent = total;
        }

        // Update order status
        function updateOrderStatus(orderID, newStatus) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST','../assets/inventory-deduction.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Show success/error message
                    const alertContainer = document.getElementById('alertContainer');
                    alertContainer.innerHTML = xhr.responseText;
                    
                    // Auto-hide alert after 5 seconds
                    setTimeout(() => {
                        alertContainer.innerHTML = '';
                    }, 5000);
                    
                    // Refresh current view and counts
                    fetchOrders(currentFilter);
                    updateStatusCounts();
                }
            };
            xhr.send('update_status=1&orderID=' + orderID + '&status=' + newStatus);
        }

        // Start polling every 10 seconds
        function startPolling() {
            pollingInterval = setInterval(() => {
                fetchOrders(currentFilter);
                updateStatusCounts();
            }, 10000);
        }

        // Stop polling
        function stopPolling() {
            clearInterval(pollingInterval);
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Start polling
            startPolling();
            
            // Stop polling when page is hidden (tab switch, minimize)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopPolling();
                } else {
                    startPolling();
                }
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Include your existing sidebar scripts -->
    <script src="../assets/js/admin_sidebar.js"></script>
</body>
</html>