<?php
include '../assets/connect.php';
session_start();

// Check if user is logged in and is an admin 
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Initialize cart session if it doesn't exist
if (!isset($_SESSION['pos_cart'])) {
    $_SESSION['pos_cart'] = [];
}

$categoriesWithSugarIce = ["Milktea", "Frappe", "Iced Coffee", "Fruit Tea", "Non-Coffee"];

// CANCEL ORDER - clear cart session
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $_SESSION['pos_cart'] = [];
    exit(); // Optional: stop further processing
}


// ADD TO CART
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_order'])) {
    $userID = $_SESSION['userID'];
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $notes = $_POST['notes'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);

    $sugar = in_array($category, $categoriesWithSugarIce) ? ($_POST['sugar'] ?? '') : null;
    $ice = in_array($category, $categoriesWithSugarIce) ? ($_POST['ice'] ?? '') : null;

    // Merge if same item already exists
    $itemExists = false;
    foreach ($_SESSION['pos_cart'] as $index => $item) {
        if (
            $item['product_id'] == $productId &&
            $item['sugar'] == $sugar &&
            $item['ice'] == $ice &&
            $item['notes'] == $notes
        ) {
            $_SESSION['pos_cart'][$index]['quantity'] += $quantity;
            $itemExists = true;
            break;
        }
    }

    if (!$itemExists) {
        $_SESSION['pos_cart'][] = [
            'product_id' => $productId,
            'product_name' => $productName,
            'price' => $price,
            'category' => $category,
            'sugar' => $sugar,
            'ice' => $ice,
            'notes' => $notes,
            'quantity' => $quantity
        ];
    }
}

// REMOVE ITEM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $index = intval($_POST['remove_item']);
    if (isset($_SESSION['pos_cart'][$index])) {
        unset($_SESSION['pos_cart'][$index]);
        $_SESSION['pos_cart'] = array_values($_SESSION['pos_cart']); // Reindex
    }
}


// CONFIRM ORDER
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $userID = $_SESSION['userID'];
    $orderType = $_POST['orderType'];
    $paymentMode = $_POST['paymentMode'];
    $refNumber = isset($_POST['refNumber']) ? mysqli_real_escape_string($conn, $_POST['refNumber']) : null;
    $today = date("Y-m-d H:i:s");

    // Calculate total
    $totalAmount = 0;
    foreach ($_SESSION['pos_cart'] as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }

    // -----------------------------
    // Generate orderNumber: MMDD + daily increment
    // -----------------------------
    $prefix = date('md'); // MMDD
    $todayDate = date('Y-m-d');

    $query = "SELECT orderNumber 
              FROM orders 
              WHERE DATE(orderDate) = '$todayDate' 
              ORDER BY orderNumber DESC 
              LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastNumber = intval(substr($row['orderNumber'], 4)); // last 2 digits
        $increment = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
    } else {
        $increment = '01';
    }

    $orderNumber = $prefix . $increment; // MMDDXX

    // -----------------------------
    // Insert into orders
    // -----------------------------
    $insertOrder = "INSERT INTO orders 
        (orderDate, status, totalAmount, orderType, userID, orderNumber)
        VALUES ('$today', 'pending', '$totalAmount', '$orderType', '$userID', '$orderNumber')";
    executeQuery($insertOrder);

    // Get new orderID
    $newOrder = executeQuery("SELECT orderID FROM orders ORDER BY orderID DESC LIMIT 1");
    $orderRow = mysqli_fetch_assoc($newOrder);
    $orderID = $orderRow['orderID'];

    // Insert order items
    foreach ($_SESSION['pos_cart'] as $item) {
        $sugarValue = ($item['sugar'] !== null) ? "'" . mysqli_real_escape_string($conn, $item['sugar']) . "'" : "NULL";
        $iceValue = ($item['ice'] !== null) ? "'" . mysqli_real_escape_string($conn, $item['ice']) . "'" : "NULL";
        $notesValue = "'" . mysqli_real_escape_string($conn, $item['notes']) . "'";

        $insertItem = "
            INSERT INTO orderitems (orderID, productID, quantity, sugar, ice, notes) 
            VALUES (
                '$orderID',
                '{$item['product_id']}',
                '{$item['quantity']}',
                $sugarValue,
                $iceValue,
                $notesValue
            )
        ";
        executeQuery($insertItem);
    }

    // Insert payment info
    $paymentRef = $refNumber ? "'$refNumber'" : "NULL";
    $insertPayment = "
        INSERT INTO payments (orderID, paymentMethod, referenceNumber, paymentStatus, userID)
        VALUES ('$orderID', '$paymentMode', $paymentRef, 'completed', '$userID')
    ";
    executeQuery($insertPayment);

    // Clear cart
    $_SESSION['pos_cart'] = [];
    $_SESSION['cart_message'] = "Order placed successfully!";
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    exit();
}


// FETCH PRODUCTS

$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'ALL';
$sortOption = isset($_GET['sort']) ? $_GET['sort'] : 'name-asc';

// Decide ORDER BY clause
switch ($sortOption) {
    case 'name-asc':
        $orderBy = "p.productName ASC";
        break;
    case 'name-desc':
        $orderBy = "p.productName DESC";
        break;
    case 'price-low-high':
        $orderBy = "p.price ASC";
        break;
    case 'price-high-low':
        $orderBy = "p.price DESC";
        break;
    default:
        $orderBy = "p.productName ASC";
}

// Build product query with filter + sort
if ($categoryFilter === 'ALL') {
    $productQuery = "
        SELECT p.*, c.categoryName 
        FROM products p 
        LEFT JOIN categories c ON p.categoryID = c.categoryID 
        WHERE p.isAvailable = 'Yes'
        ORDER BY $orderBy
    ";
} else {
    $safeCategory = mysqli_real_escape_string($conn, $categoryFilter);
    $productQuery = "
        SELECT p.*, c.categoryName 
        FROM products p 
        LEFT JOIN categories c ON p.categoryID = c.categoryID 
        WHERE p.isAvailable = 'Yes' AND c.categoryName = '$safeCategory'
        ORDER BY $orderBy
    ";
}

$products = executeQuery($productQuery);

// Fetch categories
$categoryQuery = "SELECT * FROM categories ORDER BY categoryName";
$categories = executeQuery($categoryQuery);

// Function to get cart item count
function getCartItemCount()
{
    $count = 0;
    if (isset($_SESSION['pos_cart'])) {
        foreach ($_SESSION['pos_cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}

// Get current category from cookie if using JS filtering
$currentJSCategory = isset($_COOKIE['selected_category']) ? $_COOKIE['selected_category'] : 'ALL';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/pos.css">
    <link rel="stylesheet" href="../assets/css/admin_sidebar.css">

    <!-- bootstrap icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet" />

    <!-- WOW.js Animation -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Favicon -->
    <link rel="icon" href="../assets/img/round_logo.png" type="image/png">
</head>

<body>
    <div class="container-fluid mainContainer p-2">
        <div class="row p-0">
            <!-- Offcanvas Sidebar  -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar"
                aria-labelledby="offcanvasSidebarLabel">

                <div class="offcanvas-header d-flex align-items-center justify-content-between">
                    <img src="../assets/img/saisydLogo.png" alt="Saisyd Cafe Logo" class="admin-logo"
                        style="max-height: 70px; width: auto;" />
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <!-- Body with Admin Navigation Design -->
                <div class="offcanvas-body">
                    <!-- MENU Section -->
                    <div class="section-header">Menu</div>
                    <div class="mb-3">
                        <a href="index.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                            data-wow-delay="0.1s">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="orders.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                            data-wow-delay="0.15s">
                            <i class="bi bi-clipboard-check"></i>
                            <span>Order Management</span>
                        </a>
                        <a href="point-of-sales.php"
                            class="admin-nav-link active wow animate__animated animate__fadeInLeft"
                            data-wow-delay="0.2s">
                            <i class="bi bi-shop-window"></i>
                            <span>Point of Sales</span>
                        </a>
                        <a href="inventory-management.php"
                            class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.25s">
                            <i class="bi bi-boxes"></i>
                            <span>Inventory Management</span>
                        </a>
                        <a href="menu-management.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                            data-wow-delay="0.3s">
                            <i class="bi bi-menu-button-wide"></i>
                            <span>Menu Management</span>
                        </a>
                    </div>

                    <!-- FINANCIAL Section -->
                    <div class="section-header">Financial</div>
                    <div class="mb-3">
                        <a href="sales-and-report.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                            data-wow-delay="0.35s">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Sales & Reports</span>
                        </a>
                    </div>

                    <!-- TOOLS Section -->
                    <div class="section-header">Tools</div>
                    <div>
                        <a href="#" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                            data-wow-delay="0.4s">
                            <i class="bi bi-gear"></i>
                            <span>Settings</span>
                        </a>
                        <a href="login.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                            data-wow-delay="0.45s">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main content column -->
            <div class="container-fluid mb-2">
                <div class="d-flex flex-column flex-lg-row pt-3">
                    <!-- POS content -->
                    <div class="flex-fill">
                        <div class="card p-2 topcontainer mb-3">
                            <div class="container-fluid">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <button class="btn btn-sm mobile-menu-toggle pt-2" data-bs-toggle="offcanvas"
                                            data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar"
                                            aria-label="Toggle sidebar">
                                            <i class="fa fa-bars"></i>
                                        </button>
                                        <h5 class="heading fw-semibold mb-0 pt-2 text-center text-lg-start">Point of
                                            Sale System</h5>
                                    </div>
                                    <a href="orders.php" class="btn btn-light position-relative">
                                        <i class="bi bi-bell-fill"></i>
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            <span class="visually-hidden">unread messages</span>
                                        </span>
                                    </a>
                                </div>
                                <div class="subheading py-3 px-2">Category</div>


                                <?php
                                // Make sure to reset categories result for the pills
                                mysqli_data_seek($categories, 0);
                                ?>
                                <div class="category-scroll d-flex gap-3 overflow-auto pb-3" id="categories">
                                    <!-- Categories -->
                                    <a href="#" class="category-pill text-decoration-none text-center active">
                                        All
                                    </a>
                                    <?php
                                    // Generate category pills
                                    if (mysqli_num_rows($categories) > 0) {
                                        while ($category = mysqli_fetch_assoc($categories)) {
                                            $categoryName = htmlspecialchars($category['categoryName']);
                                            ?>
                                            <a href="#" class="category-pill text-decoration-none text-center">
                                                <?php echo $categoryName; ?>
                                            </a>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="card overflow-auto p-3 maincontainer" style="height: 70vh; ">
                            <div class="subheading px-2 mb-3">Items</div>
                            <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6"
                                id="maincontainer">
                                <!-- Product Loop -->
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <div class="menu-item border p-3 rounded shadow text-center width-auto card-hover 
                                            <?php echo (in_array($product['categoryName'], $categoriesWithSugarIce)) ? 'has-options' : 'no-options'; ?>"
                                                style="cursor: pointer; height: 100%;">
                                                <!-- Card content wrapper -->
                                                <div class="card-content">
                                                    <!-- Image -->
                                                    <div class="card-img">
                                                        <img src="../assets/img/img-menu/<?php echo htmlspecialchars($product['image']); ?>"
                                                            alt="<?php echo htmlspecialchars($product['productName']); ?>"
                                                            class="img-fluid" style="max-height: 170px; min-height: 120px">
                                                    </div>
                                                    <form method="post">
                                                        <!-- Product info -->
                                                        <div class="product-info">
                                                            <!-- Product name -->
                                                            <div class="lead menu-name alin"
                                                                style="font-size: clamp(0.8rem, 2vw, 1rem);">
                                                                <?php echo htmlspecialchars($product['productName']); ?>
                                                            </div>

                                                            <!-- Price + Size -->
                                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                                <span class="lead fw-bold menu-price"
                                                                    style="font-size: clamp(0.85rem, 1.5vw, 0.95rem);">
                                                                    ₱<?php echo number_format($product['price'], 2); ?>
                                                                </span>
                                                            </div>

                                                            <?php if (in_array($product['categoryName'], $categoriesWithSugarIce)): ?>
                                                                <!-- Sugar -->
                                                                <div class="dropdown mb-2">
                                                                    <button class="btn btn-outline-dark dropdown-toggle w-100"
                                                                        type="button"
                                                                        id="sugarDropdown<?php echo $product['productID']; ?>"
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        Sugar Level
                                                                    </button>
                                                                    <ul class="dropdown-menu"
                                                                        aria-labelledby="sugarDropdown<?php echo $product['productID']; ?>">
                                                                        <li><a class="dropdown-item" href="#"
                                                                                data-value="25% Sugar">25%
                                                                                Sugar Level</a></li>
                                                                        <li><a class="dropdown-item" href="#"
                                                                                data-value="50% Sugar">50%
                                                                                Sugar Level</a></li>
                                                                        <li><a class="dropdown-item" href="#"
                                                                                data-value="75% Sugar">75%
                                                                                Sugar Level</a></li>
                                                                        <li><a class="dropdown-item" href="#"
                                                                                data-value="100% Sugar">100% Sugar Level</a></li>
                                                                    </ul>
                                                                    <input type="hidden" name="sugar"
                                                                        id="sugarInput<?php echo $product['productID']; ?>"
                                                                        value="100% Sugar">
                                                                </div>

                                                                <!-- Ice -->
                                                                <div class="dropdown mb-2">
                                                                    <button class="btn btn-outline-dark dropdown-toggle w-100"
                                                                        type="button"
                                                                        id="iceDropdown<?php echo $product['productID']; ?>"
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        Ice Level
                                                                    </button>
                                                                    <ul class="dropdown-menu"
                                                                        aria-labelledby="iceDropdown<?php echo $product['productID']; ?>">
                                                                        <li><a class="dropdown-item" href="#" data-value="No Ice">No
                                                                                Ice</a></li>
                                                                        <li><a class="dropdown-item" href="#"
                                                                                data-value="Less Ice">Less
                                                                                Ice</a></li>
                                                                        <li><a class="dropdown-item" href="#"
                                                                                data-value="Regular Ice">Regular Ice</a></li>
                                                                        <li><a class="dropdown-item" href="#"
                                                                                data-value="Extra Ice">Extra Ice</a></li>
                                                                    </ul>
                                                                    <input type="hidden" name="ice"
                                                                        id="iceInput<?php echo $product['productID']; ?>"
                                                                        value="Regular Ice">
                                                                </div>
                                                            <?php endif; ?>

                                                            <!-- Hidden product info -->
                                                            <input type="hidden" name="product_id"
                                                                value="<?php echo $product['productID']; ?>">
                                                            <input type="hidden" name="product_name"
                                                                value="<?php echo htmlspecialchars($product['productName']); ?>">
                                                            <input type="hidden" name="price"
                                                                value="<?php echo $product['price']; ?>">
                                                            <input type="hidden" name="category"
                                                                value="<?php echo htmlspecialchars($product['categoryName']); ?>">
                                                        </div>
                                                    </form>
                                                </div>

                                                <!-- Add to Order button at the bottom -->
                                                <div class="add-to-order p-0">
                                                    <button type="button" class="btn btn-dark btn-sm w-100 add-to-order-btn"
                                                        data-id="<?php echo $product['productID']; ?>"
                                                        data-name="<?php echo htmlspecialchars($product['productName']); ?>"
                                                        data-price="<?php echo $product['price']; ?>"
                                                        data-category="<?php echo htmlspecialchars($product['categoryName']); ?>">
                                                        Add to Order
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-center">No products found.</p>
                                <?php endif; ?>

                            </div>
                        </div>

                    </div>

                    <!-- Receipt section -->
                    <div class="flex-lg-shrink-0 ms-0 ms-lg-3 mt-3 mt-lg-0 receipt-container">
                        <div class="card p-3 receiptCard" style="height: 100%;">
                            <div class="category-title">Receipt</div>
                            <div class="container-fluid">
                                <div class="line-divider"></div>
                            </div>

                            <!-- Scrollable receipt list -->
                            <div id="receipt" style="max-height: 600px; overflow-y: auto;">
                                <?php foreach ($_SESSION['pos_cart'] as $index => $item): ?>
                                    <div class="receipt-item d-flex justify-content-between align-items-center mb-1"
                                        data-index="<?= $index ?>">
                                        <div class="flex-grow-1 d-flex flex-column">
                                            <span class="item-qty"
                                                style="font-weight: bold;"><?= $item['quantity'] ?>x</span>
                                            <span class="item-name"
                                                style="font-weight: bold;"><?= htmlspecialchars($item['product_name']) ?></span>
                                            <?php if (!empty($item['sugar']) || !empty($item['ice'])): ?>
                                                <span
                                                    class="item-sugar text-muted"><?= $item['sugar'] ? htmlspecialchars($item['sugar']) : '' ?></span>
                                                <span
                                                    class="item-ice text-muted"><?= $item['ice'] ? htmlspecialchars($item['ice']) : '' ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="item-price">₱
                                                <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                            <button class="btn btn-sm btn-danger ms-2 remove-item-btn"
                                                data-index="<?= $index ?>">✕</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="container-fluid">
                                <!-- Notes -->
                                <!-- <div class="line-divider" style="height: 1px;"></div> -->
                                <div class="mb-3">
                                    <!-- <label for="orderNotes" class="form-label fw-bold"
                                        style="font-family: var(--primaryFont); color: var(--text-color-dark);">Customer
                                        Notes</label>

                                    <form method="POST" id="orderForm">
                                        <textarea class="form-control" name="notes" id="orderNotes" rows="3"
                                            placeholder="Add any notes..."
                                            style="resize: none; border-radius: 10px; border: 1.5px solid var(--primary-color); font-family: var(--secondaryFont);"></textarea>
                                        <input type="hidden" name="confirm_order" value="1">
                                    </form> -->

                                    <div class="line-divider" style="height: 1px;"></div>
                                        <div class="mt-4 d-flex flex-row justify-content-between">
                                            <div><b>TOTAL</b></div>
                                            <div id="totalValue">₱
                                                <?= number_format(array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $_SESSION['pos_cart'])), 2) ?>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
                                            <button id="orderNowBtn"
                                                class="btn btn-dark-order w-100 w-md-auto py-2 px-3" type="button" form="orderForm">Order
                                                Now</button>
                                            <button id="cancelOrderBtn"
                                                class="btn btn-dark-cancel w-100 w-md-auto py-2 px-3">Cancel
                                                Order</button>
                                        </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal Placeholder -->
            <div id="modal-placeholder">
                <?php include '../modal/pos-modal.php'; ?>
            </div>

            <script src="../assets/js/pos.js"></script>
            <script src="../assets/js/admin_sidebar.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
                crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
        </div>
    </div>
</body>

</html>