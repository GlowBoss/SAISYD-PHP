<?php
include 'assets/connect.php';
session_start();

// Initialize cart session if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Set session timeout (30 minutes)
if (!isset($_SESSION['cart_timeout'])) {
    $_SESSION['cart_timeout'] = time() + (30 * 60);
}

// Check if cart session has expired
if (time() > $_SESSION['cart_timeout']) {
    $_SESSION['cart'] = [];
    $_SESSION['cart_timeout'] = time() + (30 * 60);
}

// Handle Add to Cart via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $productName = $_POST['product_name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $notes = $_POST['notes'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);

    // Check if this category needs sugar/ice customization
    $categoriesWithSugarIce = ["Milktea", "Frappe", "Iced Coffee", "Fruit Tea", "Non-Coffee"];

    if (in_array($category, $categoriesWithSugarIce)) {
        $sugar = $_POST['sugar'] ?? '';
        $ice = $_POST['ice'] ?? '';
    } else {
        // For food or other categories, set to NULL
        $sugar = null;
        $ice = null;
    }

    // Check if item with same customizations already exists in SESSION
    $itemExists = false;
    foreach ($_SESSION['cart'] as $index => $item) {
        if (
            $item['product_id'] == $productId &&
            $item['sugar'] == $sugar &&
            $item['ice'] == $ice &&
            $item['notes'] == $notes
        ) {
            $_SESSION['cart'][$index]['quantity'] += $quantity;
            $itemExists = true;
            break;
        }
    }

    // If item doesn't exist, add new item in SESSION
    if (!$itemExists) {
        $_SESSION['cart'][] = [
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

    // INSERT TO DATABASE

    // Check if may pending order (reuse) or create new
    // $orderResult = executeQuery("SELECT * FROM orders WHERE status='pending' LIMIT 1");

    // if (mysqli_num_rows($orderResult) > 0) {
    //     $order = mysqli_fetch_assoc($orderResult);
    //     $orderID = $order['orderID'];
    // } else {
    //     $today = date("Y-m-d");
    //     $insertOrder = "INSERT INTO orders (orderDate, status, totalAmount) VALUES ('$today','pending',0)";
    //     executeQuery($insertOrder);

    //     $newOrder = executeQuery("SELECT orderID FROM orders ORDER BY orderID DESC LIMIT 1");
    //     $orderRow = mysqli_fetch_assoc($newOrder);
    //     $orderID = $orderRow['orderID'];
    // }

    // Prepare values for database insert -  naghahandle ng NULL values 
    $sugarValue = ($sugar !== null) ? "'" . mysqli_real_escape_string($conn, $sugar) . "'" : "NULL";
    $iceValue = ($ice !== null) ? "'" . mysqli_real_escape_string($conn, $ice) . "'" : "NULL";
    $notesValue = "'" . mysqli_real_escape_string($conn, $notes) . "'";

    // Insert item with customizations
    $insertItem = "
        INSERT INTO orderitems (orderID, productID, quantity, sugar, ice, notes) 
        VALUES (
            '$orderID',
            '$productId',
            '$quantity',
            $sugarValue,
            $iceValue,
            $notesValue
        )
    ";
    executeQuery($insertItem);

    // Reset cart timeout
    $_SESSION['cart_timeout'] = time() + (30 * 60);

    // Set success message
    $_SESSION['cart_message'] = 'Item added to cart successfully!';

    // Redirect to prevent form resubmission
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
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
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
    <title>Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/coffee.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="icon" href="assets/img/round_logo.png" type="image/png">
</head>

<body>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <div id="mobileSidebar" class="sidebar">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <img src="assets/img/saisydLogo.png" style="height: 40px;" alt="SAISYD Logo" />
            <button id="closeSidebar" class="fs-3 border-0 bg-transparent">&times;</button>
        </div>

        <div id="sidebarNav">
            <a href="index.php" class="nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.15s">
                <i class="bi bi-house fs-5"></i> <span>Home</span>
            </a>
            <a href="index.php#about" class="nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.25s">
                <i class="bi bi-info-circle fs-5"></i> <span>About</span>
            </a>
            <a href="index.php#location" class="nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.35s">
                <i class="bi bi-geo-alt fs-5"></i> <span>Location</span>
            </a>
            <a href="index.php#contact" class="nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.45s">
                <i class="bi bi-envelope fs-5"></i> <span>Contact</span>
            </a>
            <a href="cart.php" class="nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.55s"
                style="display: flex; align-items: center; justify-content: space-between; gap: 6px; position: relative;">
                <i class="bi bi-cart fs-5"></i>
                <span>Cart</span>
                <?php if (getCartItemCount() > 0): ?>
                    <span class="badge bg-danger rounded-pill"
                        style="position: absolute; top: -5px; right: 70px; font-size: 0.75rem; padding: 0.25em 0.5em;">
                        <?php echo getCartItemCount(); ?>
                    </span>
                <?php endif; ?>
            </a>

        </div>

        <button class="btn menu-btn wow" onclick="location.href='menu.php'">
            <i class="fas fa-mug-hot me-2"></i> Menu
        </button>
    </div>

    <!-- Navbar -->
    <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-custom fixed-top py-2">
        <div class="container-fluid px-3">

            <!-- Mobile Layout: Burger (left) - Logo (center) - Cart (right) -->
            <div class="d-flex d-lg-none align-items-center w-100 position-relative" style="min-height: 50px;">
                <!-- Left: Burger menu -->
                <button id="openSidebarBtn" class="navbar-toggler border-0 p-1">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Center: Logo  -->
                <div class="position-absolute top-50 translate-middle" style="left: 53%;">
                    <a class="navbar-brand fw-bold mb-0">
                        <img src="assets/img/saisydLogo.png" alt="SAISYD Logo" style="height: 45px;" />
                    </a>
                </div>

                <!-- Mobile: Right Cart -->
                <div class="ms-auto d-flex align-items-center">
                    <a href="cart.php" class="d-flex align-items-center text-decoration-none position-relative">
                        <i class="bi bi-cart3 fs-5 me-2 " style="color: var(--text-color-dark);"></i>
                        <?php if (getCartItemCount() > 0): ?>
                            <span class="position-absolute badge rounded-pill bg-danger" style="
                                top: -6px;
                                right: -6px;
                                font-size: 0.65rem;
                                padding: 0.25em 0.45em;
                                line-height: 1;
                            ">
                                <?php echo getCartItemCount(); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>


            </div>

            <!-- Desktop Layout: Logo on left -->
            <a class="navbar-brand fw-bold d-none d-lg-block">
                <img src="assets/img/saisydLogo.png" alt="SAISYD Logo" style="height: 45px;" />
            </a>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse" id="saisydNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3" id="navbarNav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-house"></i> <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">
                            <i class="bi bi-info-circle"></i> <span>About</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#location">
                            <i class="bi bi-geo-alt"></i> <span>Location</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">
                            <i class="bi bi-envelope"></i> <span>Contact</span>
                        </a>
                    </li>
                </ul>

                <!-- Desktop: Cart + Menu -->
                <div class="d-none d-lg-flex align-items-center">
                    <a href="cart.php" class="nav-link position-relative me-2">
                        <i class="bi bi-cart3 fs-4"></i> <span>Cart</span>
                        <?php if (getCartItemCount() > 0): ?>
                            <span class="position-absolute badge rounded-pill bg-danger" style="
                                top: -2px;
                                right: -2px;
                                font-size: 0.75rem;
                                padding: 0.25em 0.5em;
                            ">
                                <?php echo getCartItemCount(); ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <button class="btn menu-btn" onclick="location.href='menu.php'">
                        <i class="fas fa-mug-hot me-2"></i> Menu
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Categories + Sort Section -->
    <div class="container-fluid px-sm-2 px-md-4 px-lg-5 mt-5 mt-lg-4 pt-lg-5">
        <!-- Header Row - Title and Sort -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-4 ">

            <!-- Title -->
            <div class="title-category heading pt-2 pt-lg-2">Cafe Menu</div>

            <div class="custom-select-wrapper d-flex align-items-center gap-2 gap-md-3 px-2 px-md-3 py-2 border rounded-pill mt-2 mt-md-0"
                style="font-family: var(--primaryFont); font-size: var(--lead); font-weight: 500; color: var(--text-color-dark); border-color: var(--primary-color); min-width: fit-content; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);">
                <i class="bi bi-funnel-fill" style="color: var(--text-color-dark); font-size: 1rem;"></i>
                <label class="mb-0 fw-semibold">Sort by:</label>

                <div class="custom-select">
                    <!-- Selected display -->
                    <div class="selected">
                        <span class="selected-text">Name A-Z</span>
                        <i class="bi bi-chevron-down dropdown-icon"></i>
                    </div>

                    <!-- Options - Using data-sort attributes for JavaScript -->
                    <div class="options">
                        <div><a href="#" data-sort="name-asc">Name A-Z</a></div>
                        <div><a href="#" data-sort="name-desc">Name Z-A</a></div>
                        <div><a href="#" data-sort="price-low-high">Price: Low to High</a></div>
                        <div><a href="#" data-sort="price-high-low">Price: High to Low</a></div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Make sure to reset categories result for the pills
        mysqli_data_seek($categories, 0);
        ?>

        <!-- Fixed Category Pills Row -->
        <div class="category-scroll d-flex gap-3 overflow-auto pb-3 pt-1">
            <!-- All category pill -->
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

    <!-- PRODUCT -->
    <div class="products-section border p-3 p-lg-5 mx-lg-3">
        <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6" id="productGrid">
            <?php
            if (mysqli_num_rows($products) > 0) {
                while ($product = mysqli_fetch_assoc($products)) {
                    ?>
                    <div class="col product-item"
                        data-category="<?php echo htmlspecialchars($product['categoryName'] ?? 'Uncategorized'); ?>"
                        data-name="<?php echo htmlspecialchars($product['productName']); ?>"
                        data-price="<?php echo $product['price']; ?>">
                        <div class="menu-item text-center" style="
                            height: clamp(260px, 40vw, 320px);
                            border-radius: 20px;
                            background-color: var(--bg-color);
                            border: 0.5px solid rgba(0, 0, 0, 0.2); /* very subtle border */
                            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); /* light shadow */
                            display: flex;
                            flex-direction: column;
                            justify-content: space-between;
                            padding: clamp(10px, 2vw, 15px);
                            transition: transform 0.25s ease, box-shadow 0.25s ease;
                        ">
                            <div
                                style="height: clamp(120px, 25vw, 150px); display: flex; align-items: center; justify-content: center;">
                                <img src="assets/img/img-menu/<?php echo htmlspecialchars($product['image'] ?? 'coffee.png'); ?>"
                                    alt="<?php echo htmlspecialchars($product['productName']); ?>" class="img-fluid"
                                    style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            </div>

                            <div class="subheading menu-name"
                                style="font-size: clamp(0.9rem, 2.5vw, 1.2rem); font-weight: 500; color: #4b2e2e; margin-top: 10px;">
                                <?php echo htmlspecialchars($product['productName']); ?>
                            </div>

                            <div class="d-flex justify-content-center align-items-center text-center px-2"
                                style="color: #6e4f3a; font-size: clamp(0.75rem, 2vw, 0.9rem);">
                                ₱<?php echo number_format($product['price'], 2); ?>
                            </div>

                            <button class="lead buy-btn mt-auto" data-bs-toggle="modal" data-bs-target="#item-customization"
                                data-product-id="<?php echo $product['productID']; ?>"
                                data-name="<?php echo htmlspecialchars($product['productName']); ?>"
                                data-price="<?php echo $product['price']; ?>"
                                data-category="<?php echo htmlspecialchars($product['categoryName'] ?? 'Uncategorized'); ?>"
                                onclick="openPopup(this)" style="
                                    font-size: clamp(0.8rem, 2vw, 1rem);
                                    border-radius: 12px;
                                    padding: clamp(6px, 1.5vw, 8px) clamp(10px, 2vw, 14px);
                                ">
                                Order Now
                            </button>
                        </div>

                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>

    <!-- Include External Modal -->
    <?php include 'modal/item-customization.php'; ?>

    <!-- Footer -->
    <footer class="bg-footer text-dark pt-5 pb-3">
        <div class="container">
            <div class="d-lg-none accordion" id="footerAccordion">
                <!-- SAISYD -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button bg-footer text-dark fw-bold" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseSaisyd">
                            SAISYD
                        </button>
                    </h2>
                    <div id="collapseSaisyd" class="accordion-collapse collapse show">
                        <div class="accordion-body small" style="text-align: justify;">
                            Minimalist café that serves good
                            food and coffee — perfect for slow mornings, casual catch-ups, and cozy evenings with
                            friends and family. Whether you're here to study, unwind, or simply savor the moment,
                            Saisyd Café welcomes you with warmth in every cup.
                        </div>
                    </div>
                </div>

                <!-- MarketPlace -->
                <div class="accordion-item border-0" style="text-align: justify;">
                    <h2 class="accordion-header">
                        <button class="accordion-button bg-footer text-dark fw-bold collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseMarket">
                            Market Place
                        </button>
                    </h2>
                    <div id="collapseMarket" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ul class="list-unstyled mb-0">
                                <li><a href="#" class="text-dark text-decoration-none footer-link">Services</a></li>
                                <li><a href="#" class="text-dark text-decoration-none footer-link">Products</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Company -->
                <div class="accordion-item border-0" style="text-align: justify;">
                    <button class="accordion-button bg-footer text-dark fw-bold collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapseCompany">
                        Company
                    </button>
                    </h2>
                    <div id="collapseCompany" class="accordion-collapse collapse">
                        <div class="accordion-body" style="text-align: justify;">
                            <ul class="list-unstyled mb-0">
                                <li><a href="#" class="text-dark text-decoration-none footer-link">About Serve
                                        It</a>
                                </li>
                                <li><a href="#" class="text-dark text-decoration-none footer-link">Help Center</a>
                                </li>
                                <li><a href="#" class="text-dark text-decoration-none footer-link">Contact Us</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Follow Us -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button bg-footer text-dark fw-bold collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseFollow">
                            Follow Us
                        </button>
                    </h2>
                    <div id="collapseFollow" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <p class="mb-1">
                                <a href="https://www.tiktok.com/@saisyd.cafe?is_from_webapp=1&sender_device=pc"
                                    target="_blank" class="text-dark text-decoration-none footer-link">
                                    <i class="fab fa-tiktok me-2"></i>SAISYD
                                </a>
                            </p>
                            <p class="mb-1">
                                <a href="#" class="text-dark text-decoration-none footer-link">
                                    <i class="fab fa-facebook-f me-2"></i>Facebook
                                </a>
                            </p>
                            <p class="mb-1">
                                <a href="#" class="text-dark text-decoration-none footer-link">
                                    <i class="fab fa-google me-2"></i>Google
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop View (visible on large screens and up) -->
            <div class="row gy-4 d-none d-lg-flex">
                <div class="col-lg-4" style="text-align: justify;">
                    <h5 class="fw-bold">SAISYD</h5>
                    <p class="small">
                        Minimalist café that serves good
                        food and coffee — perfect for slow mornings, casual catch-ups, and cozy evenings with
                        friends and family. Whether you're here to study, unwind, or simply savor the moment,
                        Saisyd Café welcomes you with warmth in every cup.
                    </p>
                </div>
                <div class="col-lg-2" style="text-align: justify;">
                    <h6 class="fw-bold">Market Place</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Services</a></li>
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Products</a></li>
                    </ul>
                </div>
                <div class="col-lg-2" style="text-align: justify;">
                    <h6 class="fw-bold">Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-dark text-decoration-none footer-link">About Serve It</a></li>
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Help Center</a></li>
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 offset-lg-1">
                    <h6 class="fw-bold">FOLLOW US</h6>
                    <p class="mb-1">
                        <a href="https://www.tiktok.com/@saisyd.cafe?is_from_webapp=1&sender_device=pc" target="_blank"
                            class="text-dark text-decoration-none footer-link">
                            <i class="fab fa-tiktok me-2"></i>SAISYD
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div
                class="border-top mt-4 pt-3 d-flex justify-content-between align-items-center flex-wrap flex-column flex-lg-row text-center text-lg-start">
                <p class="lead mb-0 small">
                    © 2024 Copyright:
                    <span class="fw-bold d-block d-lg-inline">SAISYD CAFE</span>
                </p>

                <div class="d-none d-lg-flex gap-3 fs-5">
                    <a href="#" class="text-dark"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-dark"><i class="fab fa-google"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap and External Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>

    <script>
        // Modal Functions
        function openPopup(button) {
            const modal = new bootstrap.Modal(document.getElementById('item-customization'));
            modal.show();

            // Get product data
            const productName = button.getAttribute('data-name');
            const productPrice = button.getAttribute('data-price');
            const productId = button.getAttribute('data-product-id');
            const category = button.getAttribute('data-category');

            // Update modal content
            document.querySelector('.itemName').textContent = productName;
            document.querySelector('.itemPrice').textContent = '₱' + parseFloat(productPrice).toFixed(2);

            // Hidden fields
            document.getElementById('modal_product_id').value = productId;
            document.getElementById('modal_product_name').value = productName;
            document.getElementById('modal_price').value = productPrice;
            document.getElementById('modal_category').value = category;

            // Show/hide sugar & ice depende sa category
            updateModalOptions(category);
        }

        function updateModalOptions(category) {
            const sugarSection = document.getElementById("sugarOption");
            const iceSection = document.getElementById("iceOption");

            //  Categories na may sugar & ice
            const categoriesWithSugarIce = ["Milktea", "Frappe", "Iced Coffee", "Fruit Tea", "Non-Coffee"];

            if (categoriesWithSugarIce.includes(category)) {
                sugarSection.style.display = "block";
                iceSection.style.display = "block";
            } else {
                sugarSection.style.display = "none";
                iceSection.style.display = "none";
            }
        }

        // Quantity logic
        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) quantityInput.value = currentValue - 1;
        }

        function increaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            let currentValue = parseInt(quantityInput.value);
            if (currentValue < 999) quantityInput.value = currentValue + 1;
        }

        // Reset form when modal hides
        document.getElementById('item-customization').addEventListener('hidden.bs.modal', function () {
            document.getElementById('quantity').value = 1;
            document.getElementById('customer_notes').value = '';

            // Reset radio buttons
            if (document.querySelector('input[name="sugar"][value="100% Sugar"]')) {
                document.querySelector('input[name="sugar"][value="100% Sugar"]').checked = true;
            }
            if (document.querySelector('input[name="ice"][value="Default Ice"]')) {
                document.querySelector('input[name="ice"][value="Default Ice"]').checked = true;
            }
        });

        // Prevent freeze bug when closing modal
        document.getElementById('item-customization').addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });

        // 🚀 ENHANCED MENU SYSTEM - Preserves category scroll position during sorting
        document.addEventListener('DOMContentLoaded', function () {

            // Get elements
            const categoryPills = document.querySelectorAll('.category-pill');
            const productGrid = document.getElementById('productGrid');
            const productItems = document.querySelectorAll('.product-item');
            const categoryScroll = document.querySelector('.category-scroll');
            const customSelect = document.querySelector('.custom-select');

            // Store original product data for sorting
            let allProductsData = [];
            productItems.forEach(item => {
                allProductsData.push({
                    element: item,
                    name: item.getAttribute('data-name'),
                    price: parseFloat(item.getAttribute('data-price')),
                    category: item.getAttribute('data-category')
                });
            });

            // CATEGORY FILTERING - with preserved scroll position
            categoryPills.forEach(pill => {
                pill.addEventListener('click', function (e) {
                    e.preventDefault();

                    const selectedCategory = this.textContent.trim();

                    // Store current scroll position
                    const currentScrollPosition = categoryScroll.scrollLeft;

                    // Update active state
                    categoryPills.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');

                    // Store selected category in sessionStorage
                    sessionStorage.setItem('selectedCategory', selectedCategory);
                    sessionStorage.setItem('categoryScrollPosition', currentScrollPosition);

                    // Filter products smoothly
                    smoothFilterProducts(selectedCategory);

                    // Restore scroll position after filtering
                    setTimeout(() => {
                        categoryScroll.scrollLeft = currentScrollPosition;
                    }, 100);
                });
            });

            // SMOOTH SORTING SYSTEM - No page reload required
            function handleSortChange(sortOption) {
                // Get current active category and scroll position
                const activeCategory = document.querySelector('.category-pill.active');
                const selectedCategory = activeCategory ? activeCategory.textContent.trim() : 'All';
                const currentScrollPosition = categoryScroll.scrollLeft;

                // Store positions
                sessionStorage.setItem('selectedCategory', selectedCategory);
                sessionStorage.setItem('categoryScrollPosition', currentScrollPosition);
                sessionStorage.setItem('currentSort', sortOption);

                // Apply smooth sorting
                smoothSortProducts(sortOption, selectedCategory);

                // Update sort dropdown display
                updateSortDropdownDisplay(sortOption);

                // Maintain scroll position
                setTimeout(() => {
                    categoryScroll.scrollLeft = currentScrollPosition;
                }, 200);
            }

            // SMOOTH SORT FUNCTION
            function smoothSortProducts(sortOption, activeCategory = 'All') {
                // Fade out effect
                productGrid.style.opacity = '0.5';
                productGrid.style.transition = 'opacity 0.3s ease';

                setTimeout(() => {
                    // Sort the products data
                    let sortedData = [...allProductsData];

                    switch (sortOption) {
                        case 'name-asc':
                            sortedData.sort((a, b) => a.name.localeCompare(b.name));
                            break;
                        case 'name-desc':
                            sortedData.sort((a, b) => b.name.localeCompare(a.name));
                            break;
                        case 'price-low-high':
                            sortedData.sort((a, b) => a.price - b.price);
                            break;
                        case 'price-high-low':
                            sortedData.sort((a, b) => b.price - a.price);
                            break;
                        default:
                            sortedData.sort((a, b) => a.name.localeCompare(b.name));
                    }

                    // Clear and re-append in sorted order
                    productGrid.innerHTML = '';

                    sortedData.forEach((productData, index) => {
                        productGrid.appendChild(productData.element);

                        // Apply category filter
                        const shouldShow = activeCategory === 'All' || productData.category === activeCategory;

                        if (shouldShow) {
                            productData.element.style.display = 'block';
                            productData.element.style.opacity = '0';
                            productData.element.style.transform = 'translateY(30px)';

                            // Staggered animation
                            setTimeout(() => {
                                productData.element.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                                productData.element.style.opacity = '1';
                                productData.element.style.transform = 'translateY(0)';
                            }, index * 30);
                        } else {
                            productData.element.style.display = 'none';
                        }
                    });

                    // Restore grid opacity
                    setTimeout(() => {
                        productGrid.style.opacity = '1';
                    }, 200);

                }, 150);
            }

            // SMOOTH FILTER FUNCTION - preserves current sort order
            function smoothFilterProducts(selectedCategory) {
                const currentScrollPosition = categoryScroll.scrollLeft;

                // Fade out effect
                productGrid.style.opacity = '0.5';
                productGrid.style.transition = 'opacity 0.3s ease';

                setTimeout(() => {
                    let visibleCount = 0;

                    // Get current sort option
                    const currentSort = sessionStorage.getItem('currentSort') || 'name-asc';

                    // Re-sort and filter
                    let sortedData = [...allProductsData];
                    switch (currentSort) {
                        case 'name-asc':
                            sortedData.sort((a, b) => a.name.localeCompare(b.name));
                            break;
                        case 'name-desc':
                            sortedData.sort((a, b) => b.name.localeCompare(a.name));
                            break;
                        case 'price-low-high':
                            sortedData.sort((a, b) => a.price - b.price);
                            break;
                        case 'price-high-low':
                            sortedData.sort((a, b) => b.price - a.price);
                            break;
                    }

                    // Clear and re-append in sorted order
                    productGrid.innerHTML = '';

                    sortedData.forEach((productData, index) => {
                        productGrid.appendChild(productData.element);

                        const shouldShow = selectedCategory === 'All' || productData.category === selectedCategory;

                        if (shouldShow) {
                            productData.element.style.display = 'block';
                            productData.element.style.opacity = '0';
                            productData.element.style.transform = 'translateY(30px)';

                            setTimeout(() => {
                                productData.element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                                productData.element.style.opacity = '1';
                                productData.element.style.transform = 'translateY(0)';
                            }, visibleCount * 50);

                            visibleCount++;
                        } else {
                            productData.element.style.display = 'none';
                        }
                    });

                    // Restore grid opacity and scroll position
                    setTimeout(() => {
                        productGrid.style.opacity = '1';
                        categoryScroll.scrollLeft = currentScrollPosition;
                    }, 200);

                }, 150);
            }

            // UPDATE SORT DROPDOWN DISPLAY
            function updateSortDropdownDisplay(sortOption) {
                const selectedText = document.querySelector('.selected-text');
                if (!selectedText) return;

                let displayText = "Name A-Z";
                switch (sortOption) {
                    case 'name-desc':
                        displayText = "Name Z-A";
                        break;
                    case 'price-low-high':
                        displayText = "Price: Low to High";
                        break;
                    case 'price-high-low':
                        displayText = "Price: High to Low";
                        break;
                }

                selectedText.textContent = displayText;
            }

            // DROPDOWN FUNCTIONALITY - Enhanced for smooth sorting
            if (customSelect) {
                const selected = customSelect.querySelector('.selected');
                const options = customSelect.querySelector('.options');

                // Toggle dropdown
                selected.addEventListener('click', () => {
                    options.classList.toggle('show');
                    customSelect.classList.toggle('open');
                });

                // Handle option clicks - NO PAGE RELOAD
                const optionLinks = options.querySelectorAll('a');
                optionLinks.forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault(); // Prevent page reload

                        // Get sort option from data attribute
                        const sortOption = this.getAttribute('data-sort') || 'name-asc';

                        // Apply smooth sorting
                        handleSortChange(sortOption);

                        // Close dropdown
                        options.classList.remove('show');
                        customSelect.classList.remove('open');
                    });
                });

                // Close when clicking outside
                document.addEventListener('click', (e) => {
                    if (!customSelect.contains(e.target)) {
                        options.classList.remove('show');
                        customSelect.classList.remove('open');
                    }
                });
            }

            // RESTORE STATE ON PAGE LOAD
            function restoreMenuState() {
                // Restore selected category
                const savedCategory = sessionStorage.getItem('selectedCategory');
                const savedScrollPosition = sessionStorage.getItem('categoryScrollPosition');
                const savedSort = sessionStorage.getItem('currentSort');

                if (savedCategory && savedCategory !== 'All') {
                    // Update active category pill
                    categoryPills.forEach(pill => {
                        pill.classList.remove('active');
                        if (pill.textContent.trim() === savedCategory) {
                            pill.classList.add('active');
                        }
                    });

                    // Apply sort and filter
                    if (savedSort) {
                        updateSortDropdownDisplay(savedSort);
                        smoothSortProducts(savedSort, savedCategory);
                    } else {
                        smoothFilterProducts(savedCategory);
                    }
                } else if (savedSort) {
                    // Just apply sorting if no category filter
                    updateSortDropdownDisplay(savedSort);
                    smoothSortProducts(savedSort, 'All');
                }

                // Restore scroll position
                if (savedScrollPosition) {
                    setTimeout(() => {
                        categoryScroll.scrollLeft = parseInt(savedScrollPosition);
                    }, 300);
                }
            }

            // ENHANCED MENU HOVER EFFECTS
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('mouseenter', function () {
                    this.style.transform = 'translateY(-8px) scale(1.03)';
                    this.style.boxShadow = '0 12px 30px rgba(0, 0, 0, 0.15)';
                    this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
                });

                item.addEventListener('mouseleave', function () {
                    this.style.transform = 'translateY(0) scale(1)';
                    this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                });
            });

            // Initialize state restoration
            setTimeout(() => {
                restoreMenuState();
            }, 100);

            // Make functions globally accessible
            window.smoothFilterProducts = smoothFilterProducts;
            window.handleSortChange = handleSortChange;
            window.smoothSortProducts = smoothSortProducts;
        });
    </script>

    <!-- External Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/navbar.js"></script>

</body>

</html>