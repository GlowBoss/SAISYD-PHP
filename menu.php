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
    $size = $_POST['size'] ?? '';
    $sugar = $_POST['sugar'] ?? '';
    $ice = $_POST['ice'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 1);

    // Check if item with same customizations already exists
    $itemExists = false;
    foreach ($_SESSION['cart'] as $index => $item) {
        if (
            $item['product_id'] == $productId &&
            $item['size'] == $size &&
            $item['sugar'] == $sugar &&
            $item['ice'] == $ice &&
            $item['notes'] == $notes
        ) {
            $_SESSION['cart'][$index]['quantity'] += $quantity;
            $itemExists = true;
            break;
        }
    }

    // If item doesn't exist, add new item
    if (!$itemExists) {
        $_SESSION['cart'][] = [
            'product_id' => $productId,
            'product_name' => $productName,
            'price' => $price,
            'category' => $category,
            'size' => $size,
            'sugar' => $sugar,
            'ice' => $ice,
            'notes' => $notes,
            'quantity' => $quantity
        ];
    }

    // Reset cart timeout
    $_SESSION['cart_timeout'] = time() + (30 * 60);

    // Set success message
    $_SESSION['cart_message'] = 'Item added to cart successfully!';

    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    exit();
}

// -------------------
// FETCH PRODUCTS
// -------------------
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
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-2">
            <div class="title-category heading align-items-sm-start">Cafe Menu</div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">

            <!-- Sort Dropdown -->
            <div class="custom-select-wrapper d-flex align-items-center gap-3 px-3 py-2 border rounded-pill shadow-sm mx-auto mx-md-0 order-0 order-md-1 mb-lg-2 mb-3"
                style="background-color: var(--card-bg-color); font-family: var(--primaryFont); font-size: var(--lead); font-weight: 500; color: var(--text-color-dark); border-color: var(--primary-color);">

                <i class="bi bi-funnel-fill" style="color: var(--text-color-dark); font-size: 1rem;"></i>
                <label class="mb-0 fw-semibold">Sort by:</label>

                <div class="custom-select">
                    <?php
                    // current label depende sa sort option
                    $currentSortLabel = "Name A-Z";
                    if ($sortOption === "name-desc")
                        $currentSortLabel = "Name Z-A";
                    elseif ($sortOption === "price-low-high")
                        $currentSortLabel = "Price: Low to High";
                    elseif ($sortOption === "price-high-low")
                        $currentSortLabel = "Price: High to Low";
                    ?>

                    <!-- Selected display -->
                    <div class="selected">
                        <span class="selected-text"><?php echo $currentSortLabel; ?></span>
                        <i class="bi bi-chevron-down dropdown-icon"></i>
                    </div>

                    <!-- Options (use links para PHP reload lang) -->
                    <div class="options">
                        <div><a
                                href="?sort=name-asc<?php echo ($categoryFilter !== 'ALL') ? '&category=' . urlencode($categoryFilter) : ''; ?>">Name
                                A-Z</a></div>
                        <div><a
                                href="?sort=name-desc<?php echo ($categoryFilter !== 'ALL') ? '&category=' . urlencode($categoryFilter) : ''; ?>">Name
                                Z-A</a></div>
                        <div><a
                                href="?sort=price-low-high<?php echo ($categoryFilter !== 'ALL') ? '&category=' . urlencode($categoryFilter) : ''; ?>">Price:
                                Low to High</a></div>
                        <div><a
                                href="?sort=price-high-low<?php echo ($categoryFilter !== 'ALL') ? '&category=' . urlencode($categoryFilter) : ''; ?>">Price:
                                High to Low</a></div>
                    </div>
                </div>
            </div>


            <!-- Category Pills Row (unchanged) -->
            <div class="category-scroll d-flex gap-3 overflow-auto pb-3 pt-1 flex-grow-1 order-1 order-md-0">
                <a href="?category=ALL&sort=<?php echo $sortOption; ?>"
                    class="category-pill text-decoration-none text-center <?php echo ($categoryFilter === 'ALL') ? 'active' : ''; ?>">
                    All
                </a>
                <?php
                if (mysqli_num_rows($categories) > 0) {
                    while ($category = mysqli_fetch_assoc($categories)) {
                        ?>
                        <a href="?category=<?php echo urlencode($category['categoryName']); ?>&sort=<?php echo $sortOption; ?>"
                            class="category-pill text-decoration-none text-center <?php echo ($categoryFilter === $category['categoryName']) ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($category['categoryName']); ?>
                        </a>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

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
                        <div class="menu-item text-center shadow-sm" style="
                        height: 320px; 
                        background-color: #fff9f2; 
                        border-radius: 20px; 
                        border: 1px solid #e0c9a6;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); 
                        display: flex; 
                        flex-direction: column; 
                        justify-content: space-between; 
                        padding: 15px;
                        transition: transform 0.2s ease;
                    ">
                            <div style="height: 150px; display: flex; align-items: center; justify-content: center;">
                                <img src="assets/img/img-menu/<?php echo htmlspecialchars($product['image'] ?? 'coffee.png'); ?>"
                                    alt="<?php echo htmlspecialchars($product['productName']); ?>" class="img-fluid"
                                    style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            </div>
                            <div class="subheading menu-name"
                                style="font-size: 1.2rem; font-weight: 500; color: #4b2e2e;margin-top: 10px;">
                                <?php echo htmlspecialchars($product['productName']); ?>
                            </div>
                            <div class="d-flex justify-content-center align-items-center text-center px-2"
                                style="color: #6e4f3a; font-size: 0.85rem;">
                                ₱<?php echo number_format($product['price'], 2); ?>
                            </div>
                            <button class="lead buy-btn mt-auto" data-bs-toggle="modal" data-bs-target="#item-customization"
                                data-product-id="<?php echo $product['productID']; ?>"
                                data-name="<?php echo htmlspecialchars($product['productName']); ?>"
                                data-price="<?php echo $product['price']; ?>"
                                data-category="<?php echo htmlspecialchars($product['categoryName'] ?? 'Uncategorized'); ?>"
                                onclick="openPopup(this)">Order Now</button>
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
                        <div class="accordion-body small">
                            Donec imperdiet sit amet sem consectetur tincidunt. In pulvinar ex ut tempus tincidunt.
                            Cras vitae orci risus. Fusce eget dictum ex, a vulputate mi. Aliquam in mattis quam,
                            vel laoreet elit. Pellentesque nulla leo, tristique at ex eget, efficitur bibendum tortor.
                        </div>
                    </div>
                </div>

                <!-- MarketPlace -->
                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button bg-footer text-dark fw-bold collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseMarket">
                            MarketPlace
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
                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button bg-footer text-dark fw-bold collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseCompany">
                            Company
                        </button>
                    </h2>
                    <div id="collapseCompany" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <ul class="list-unstyled mb-0">
                                <li><a href="#" class="text-dark text-decoration-none footer-link">About Servelt</a>
                                </li>
                                <li><a href="#" class="text-dark text-decoration-none footer-link">Help Center</a></li>
                                <li><a href="#" class="text-dark text-decoration-none footer-link">Contact Us</a></li>
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
                                <a href="https://www.tiktok.com/@saisyd" target="_blank"
                                    class="text-dark text-decoration-none footer-link">
                                    <i class="fab fa-tiktok me-2"></i>SAISYD
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop View (visible on large screens and up) -->
            <div class="row gy-4 d-none d-lg-flex">
                <div class="col-lg-4">
                    <h5 class="fw-bold">SAISYD</h5>
                    <p class="small">
                        Donec imperdiet sit amet sem consectetur tincidunt. In pulvinar ex ut tempus tincidunt.
                        Cras vitae orci risus. Fusce eget dictum ex, a vulputate mi. Aliquam in mattis quam,
                        vel laoreet elit. Pellentesque nulla leo, tristique at ex eget, efficitur bibendum tortor.
                    </p>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold">MarketPlace</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Services</a></li>
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Products</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="fw-bold">Company</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-dark text-decoration-none footer-link">About Servelt</a></li>
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Help Center</a></li>
                        <li><a href="#" class="text-dark text-decoration-none footer-link">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 offset-lg-1">
                    <h6 class="fw-bold">FOLLOW US</h6>
                    <p class="mb-1">
                        <a href="https://www.tiktok.com/@saisyd" target="_blank"
                            class="text-dark text-decoration-none footer-link">
                            <i class="fab fa-tiktok me-2"></i>SAISYD
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-top mt-4 pt-3 d-flex justify-content-between align-items-center flex-wrap">
                <p class="lead mb-0 small">© 2024 Copyright:
                    <span class="fw-bold">SAISYD CAFE</span>
                </p>
                <div class="d-flex gap-3 fs-5">
                    <a href="#" class="text-dark"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-dark"><i class="fab fa-google"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>

    <script>
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

            // Show/hide options depende sa category
            updateModalOptions(category);
        }

        function updateModalOptions(category) {
            const sizeSection = document.getElementById("sizeOption");
            const sugarSection = document.getElementById("sugarOption");
            const iceSection = document.getElementById("iceOption");

            if (category === "Coffee" || category === "Tea") {
                sizeSection.style.display = "block";
                sugarSection.style.display = "block";
                iceSection.style.display = "block";
            } else {
                sizeSection.style.display = "none";
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
            if (document.getElementById('size12oz')) document.getElementById('size12oz').checked = true;
            if (document.querySelector('input[name="sugar"][value="100% Sugar"]')) {
                document.querySelector('input[name="sugar"][value="100% Sugar"]').checked = true;
            }
            if (document.querySelector('input[name="ice"][value="Default"]')) {
                document.querySelector('input[name="ice"][value="Default"]').checked = true;
            }
        });

        // Prevent freeze bug when closing modal
        document.getElementById('item-customization').addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('modal-open');
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        });

        // DROPDOWN
        document.addEventListener('DOMContentLoaded', function () {
            const customSelect = document.querySelector('.custom-select');
            const selected = customSelect.querySelector('.selected');
            const options = customSelect.querySelector('.options');

            // Toggle open/close
            selected.addEventListener('click', () => {
                options.classList.toggle('show');
                customSelect.classList.toggle('open');
            });

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!customSelect.contains(e.target)) {
                    options.classList.remove('show');
                    customSelect.classList.remove('open');
                }
            });
        });
    </script>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/navbar.js"></script>
</body>

</html>