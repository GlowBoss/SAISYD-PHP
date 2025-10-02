<?php
include 'assets/connect.php';
include 'assets/track_visits.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_quantity'])) {
        $index = $_POST['cart_index'];
        $newQuantity = intval($_POST['new_quantity']);

        if (isset($_SESSION['cart'][$index])) {
            if ($newQuantity > 0) {
                $_SESSION['cart'][$index]['quantity'] = $newQuantity;
            } else {
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
        }
    }

    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        $_SESSION['cart_message'] = 'Cart cleared successfully!';
    }

    // Checkout processing - ONLY process if confirmed_checkout is set
    if (isset($_POST['checkout']) && isset($_POST['confirmed_checkout'])) {
        // Get form data
        $orderType = $_POST['order_type'] ?? '';
        $paymentMethod = $_POST['payment_method'] ?? '';
        $refNumber = $_POST['ref_number'] ?? '';
        $customerName = $_POST['customer_name'] ?? '';
        $customerPhone = $_POST['customer_phone'] ?? '';

        // Validation
        if (empty($orderType) || empty($paymentMethod)) {
            $_SESSION['cart_error'] = 'Please select both order method and payment method.';
        } elseif ($paymentMethod === 'gcash' && (!$refNumber || !preg_match('/^\d{13}$/', $refNumber))) {
            $_SESSION['cart_error'] = 'GCash reference number must be exactly 13 digits.';
        } elseif (empty($_SESSION['cart'])) {
            $_SESSION['cart_error'] = 'Your cart is empty.';
        } elseif ($orderType === 'pickup' && (empty($customerName) || empty($customerPhone))) {
            $_SESSION['cart_error'] = 'Please enter your name and phone number for pickup orders.';
        } else {
            // Calculate total
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Generate new order number
            $result = executeQuery("SELECT orderNumber FROM orders ORDER BY orderID DESC LIMIT 1");
            $orderNumber = 1;

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $lastOrderNumber = $row['orderNumber'];
                $orderNumber = $lastOrderNumber + 1;
            }

            // Handle customer info based on order type
            if ($orderType === 'pickup') {
                $customerName = mysqli_real_escape_string($conn, $customerName);
                $customerPhone = mysqli_real_escape_string($conn, $customerPhone);
            } else {
                $customerName = 'Walk-in Customer';
                $customerPhone = null;
            }

            // Insert order
            $insertOrderQuery = "INSERT INTO orders (orderDate, customerName, orderContactNumber, totalAmount, orderType, orderNumber, status, isDone, userID) 
                                VALUES (NOW(), '$customerName', " .
                ($customerPhone ? "'$customerPhone'" : "NULL") . ", 
                                '$total', '$orderType', '$orderNumber', 'pending', 0, 1)";

            $orderResult = executeQuery($insertOrderQuery);

            if ($orderResult) {
                $orderID = mysqli_insert_id($conn);

                // Insert order items
                foreach ($_SESSION['cart'] as $item) {
                    $productID = $item['product_id'];
                    $quantity = $item['quantity'];

                    $sugar = (isset($item['sugar']) && $item['sugar'] !== '' && trim($item['sugar']) !== '' && $item['sugar'] !== '0')
                        ? "'" . mysqli_real_escape_string($conn, $item['sugar']) . "'"
                        : 'NULL';

                    $ice = (isset($item['ice']) && $item['ice'] !== '' && trim($item['ice']) !== '')
                        ? "'" . mysqli_real_escape_string($conn, $item['ice']) . "'"
                        : 'NULL';

                    $notes = (isset($item['notes']) && $item['notes'] !== '' && trim($item['notes']) !== '')
                        ? "'" . mysqli_real_escape_string($conn, $item['notes']) . "'"
                        : 'NULL';

                    $insertItemQuery = "INSERT INTO orderitems (orderID, productID, quantity, sugar, ice, notes) 
                                       VALUES ('$orderID', '$productID', '$quantity', $sugar, $ice, $notes)";
                    executeQuery($insertItemQuery);
                }

                // Insert payment
                $paymentStatus = 'paid';
                $refNumberEscaped = $refNumber ? "'" . mysqli_real_escape_string($conn, $refNumber) . "'" : "NULL";
                $paymentMethodEscaped = mysqli_real_escape_string($conn, $paymentMethod);

                $insertPaymentQuery = "INSERT INTO payments (orderID, paymentMethod, paymentStatus, referenceNumber) 
                                      VALUES ('$orderID', '$paymentMethodEscaped', '$paymentStatus', $refNumberEscaped)";
                executeQuery($insertPaymentQuery);

                $_SESSION['cart'] = [];
                $_SESSION['cart_message'] = 'Order placed successfully! Order No. ' . $orderNumber;

                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $_SESSION['cart_error'] = 'Failed to place order. Please try again.';
            }
        }
    }
}

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

function getCartTotal()
{
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/cart.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
            <!-- Mobile Layout -->
            <div class="d-flex d-lg-none align-items-center w-100 position-relative" style="min-height: 50px;">
                <button id="openSidebarBtn" class="navbar-toggler border-0 p-1">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="position-absolute top-50 translate-middle" style="left: 53%;">
                    <a class="navbar-brand fw-bold mb-0">
                        <img src="assets/img/saisydLogo.png" alt="SAISYD Logo" style="height: 45px;" />
                    </a>
                </div>

                <div class="ms-auto d-flex align-items-center">
                    <a href="cart.php" class="d-flex align-items-center text-decoration-none position-relative">
                        <i class="bi bi-cart3 fs-5 me-2" style="color: var(--text-color-dark);"></i>
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

            <!-- Desktop Layout -->
            <a class="navbar-brand fw-bold d-none d-lg-block">
                <img src="assets/img/saisydLogo.png" alt="SAISYD Logo" style="height: 45px;" />
            </a>

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

    <!-- Main Content -->
    <div id="mainContent" class="cart-container">
        <div class="cart-main">
            <!-- Header -->
            <div class="cart-header">
                <h1 class="cart-title">Your Cart</h1>
                <p class="cart-subtitle">Review your items and checkout when ready</p>
            </div>

            <div class="cart-content">
                <!-- Cart Items Section -->
                <div class="cart-items-section">
                    <div class="cart-section-header">
                        <h2 class="section-title">
                            <i class="bi bi-bag me-2"></i>
                            Cart Items (<?= getCartItemCount() ?>)
                        </h2>
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <form method="POST" style="margin: 0;">
                                <button type="submit" name="clear_cart" class="clear-cart-btn">
                                    <i class="fas fa-trash-alt me-1"></i> Clear All
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div class="cart-items-container">
                        <?php if (empty($_SESSION['cart'])): ?>
                            <div class="empty-cart">
                                <div class="empty-cart-icon">
                                    <i class="bi bi-cart-x"></i>
                                </div>
                                <p class="empty-cart-message">Your cart is empty</p>
                                <a href="menu.php" class="browse-menu-btn">
                                    <i class="fas fa-mug-hot me-2"></i> Browse Menu
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($_SESSION['cart'] as $i => $item): ?>
                                <div class="cart-item">
                                    <div class="cart-item-main">
                                        <!-- Quantity Controls -->
                                        <div class="quantity-controls">
                                            <form method="POST" style="margin: 0;">
                                                <input type="hidden" name="cart_index" value="<?= $i ?>">
                                                <input type="hidden" name="new_quantity"
                                                    value="<?= max(0, $item['quantity'] - 1) ?>">
                                                <button type="submit" name="update_quantity" class="quantity-btn">-</button>
                                            </form>
                                            <span class="quantity-display"><?= $item['quantity'] ?></span>
                                            <form method="POST" style="margin: 0;">
                                                <input type="hidden" name="cart_index" value="<?= $i ?>">
                                                <input type="hidden" name="new_quantity" value="<?= $item['quantity'] + 1 ?>">
                                                <button type="submit" name="update_quantity" class="quantity-btn">+</button>
                                            </form>
                                        </div>

                                        <!-- Item Details -->
                                        <div class="item-details">
                                            <h4 class="item-name"><?= htmlspecialchars($item['product_name']) ?></h4>
                                        </div>

                                        <!-- Item Price -->
                                        <div class="item-price">₱<?= number_format($item['price'], 2) ?></div>
                                    </div>

                                    <!-- Customizations -->
                                    <?php if (
                                        (isset($item['sugar']) && $item['sugar'] !== '' && trim($item['sugar']) !== '' && $item['sugar'] !== '0') ||
                                        (isset($item['ice']) && $item['ice'] !== '' && trim($item['ice']) !== '') ||
                                        (isset($item['notes']) && $item['notes'] !== '' && trim($item['notes']) !== '')
                                    ): ?>
                                        <div class="item-customizations">
                                            <?php if (isset($item['sugar']) && $item['sugar'] !== '' && trim($item['sugar']) !== '' && $item['sugar'] !== '0'): ?>
                                                <div class="customization-item">
                                                    <span class="customization-label">Sugar:</span>
                                                    <?= htmlspecialchars($item['sugar']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (isset($item['ice']) && $item['ice'] !== '' && trim($item['ice']) !== ''): ?>
                                                <div class="customization-item">
                                                    <span class="customization-label">Ice:</span> <?= htmlspecialchars($item['ice']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (isset($item['notes']) && $item['notes'] !== '' && trim($item['notes']) !== ''): ?>
                                                <div class="customization-item">
                                                    <span class="customization-label">Notes:</span>
                                                    <?= htmlspecialchars($item['notes']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="summary-section">
                    <div class="summary-header">
                        <h2 class="section-title">
                            <i class="bi bi-receipt me-2"></i>
                            Order Summary
                        </h2>
                    </div>

                    <?php if (!empty($_SESSION['cart'])): ?>
                        <!-- Order Summary -->
                        <div class="order-summary">
                            <div class="summary-row">
                                <span class="summary-label">Items:</span>
                                <span class="summary-value"><?= getCartItemCount() ?></span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Subtotal:</span>
                                <span class="summary-value">₱<?= number_format(getCartTotal(), 2) ?></span>
                            </div>
                            <div class="summary-row total-row">
                                <span class="total-label">Total:</span>
                                <span class="total-value">₱<?= number_format(getCartTotal(), 2) ?></span>
                            </div>
                        </div>

                        <form method="POST" id="checkoutForm">
                            <!-- Hidden inputs for customer info (will be populated by pickup modal) -->
                            <input type="hidden" name="customer_name" id="hiddenCustomerName" value="">
                            <input type="hidden" name="customer_phone" id="hiddenCustomerPhone" value="">

                            <!-- Order Method -->
                            <div class="form-section">
                                <h3 class="form-title">
                                    <i class="bi bi-clipboard-check me-2"></i>
                                    Order Method
                                </h3>
                                <div class="radio-group">
                                    <div class="radio-option">
                                        <input class="form-check-input" type="radio" name="order_type" value="dine-in"
                                            id="dine-in" required>
                                        <label class="radio-card" for="dine-in">
                                            <i class="bi bi-shop mb-2 d-block" style="font-size: 1.5rem;"></i>
                                            <p class="radio-label">Dine-in</p>
                                        </label>
                                    </div>
                                    <div class="radio-option">
                                        <input class="form-check-input" type="radio" name="order_type" value="takeout"
                                            id="takeout" required>
                                        <label class="radio-card" for="takeout">
                                            <i class="bi bi-bag-check mb-2 d-block" style="font-size: 1.5rem;"></i>
                                            <p class="radio-label">Takeout</p>
                                        </label>
                                    </div>
                                    <div class="radio-option">
                                        <input class="form-check-input" type="radio" name="order_type" value="pickup"
                                            id="pickup" required>
                                        <label class="radio-card" for="pickup">
                                            <i class="bi bi-clock mb-2 d-block" style="font-size: 1.5rem;"></i>
                                            <p class="radio-label">Pickup</p>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="form-section">
                                <h3 class="form-title">
                                    <i class="bi bi-credit-card me-2"></i>
                                    Payment Method
                                </h3>
                                <div class="radio-group">
                                    <div class="radio-option">
                                        <input class="form-check-input" type="radio" name="payment_method" value="cash"
                                            id="cash" required>
                                        <label class="radio-card" for="cash">
                                            <i class="bi bi-cash mb-2 d-block" style="font-size: 1.5rem;"></i>
                                            <p class="radio-label">Cash</p>
                                        </label>
                                    </div>
                                    <div class="radio-option">
                                        <input class="form-check-input" type="radio" name="payment_method" value="gcash"
                                            id="gcash" required>
                                        <label class="radio-card" for="gcash">
                                            <i class="bi bi-phone mb-2 d-block" style="font-size: 1.5rem;"></i>
                                            <p class="radio-label">GCash</p>
                                        </label>
                                    </div>
                                </div>

                                <div class="gcash-field" id="gcashField" style="display:none;">
                                    <input type="text" class="form-control" name="ref_number" id="refNumber"
                                        placeholder="Enter 13-digit GCash Reference Number" pattern="\d{13}" maxlength="13"
                                        inputmode="numeric"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,13);">
                                </div>
                            </div>

                            <button type="button" name="checkout" id="checkoutBtn" class="checkout-btn">
                                <i class="bi bi-check-circle me-2"></i>
                                Place Order • ₱<?= number_format(getCartTotal(), 2) ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center p-4">
                            <p class="text-muted mb-3">Add items to your cart to proceed with checkout</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Display Success/Error Messages -->
    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="toast-container position-fixed bottom-0 end-0 p-3 z-3">
            <div id="successToast" class="toast align-items-center border-0 fade show" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="3000" data-bs-autohide="true"
                style="background-color: var(--text-color-dark); color: var(--text-color-light); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill ms-3 me-3"
                        style="font-size: 1.2rem; color: var(--secondary-color);"></i>
                    <div class="toast-body" style="font-family: var(--secondaryFont);">
                        <?= $_SESSION['cart_message'] ?>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"
                        style="filter: invert(1);"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['cart_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['cart_error'])): ?>
        <div class="toast-container position-fixed bottom-0 end-0 p-3 z-3">
            <div id="errorToast" class="toast align-items-center border-0 fade show" role="alert" aria-live="assertive"
                aria-atomic="true" data-bs-delay="4000" data-bs-autohide="true"
                style="background-color: var(--text-color-dark); color: var(--text-color-light); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
                <div class="d-flex align-items-center">
                    <i class="bi bi-x-circle-fill ms-3 me-3" style="font-size: 1.2rem; color: #dc3545;"></i>
                    <div class="toast-body" style="font-family: var(--secondaryFont);">
                        <?= $_SESSION['cart_error'] ?>
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"
                        style="filter: invert(1);"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['cart_error']); ?>
    <?php endif; ?>

    <!-- Include both modals -->
    <?php include 'modal/cart-modal.php'; ?>
    <?php include 'modal/order-confirmation-modal.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/navbar.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            
            const successToast = document.getElementById('successToast');
            const errorToast = document.getElementById('errorToast');

            if (successToast) {
                const toast = new bootstrap.Toast(successToast);
                toast.show();
            }

            if (errorToast) {
                const toast = new bootstrap.Toast(errorToast);
                toast.show();
            }

            // Radio card 
            const radioInputs = document.querySelectorAll('input[type="radio"]');

            radioInputs.forEach(radio => {
                radio.addEventListener('change', () => {
                    
                    const groupName = radio.getAttribute('name');
                    document.querySelectorAll(`input[name="${groupName}"]`).forEach(input => {
                        input.closest('.radio-option').querySelector('.radio-card').classList.remove('selected');
                    });

                    
                    if (radio.checked) {
                        radio.closest('.radio-option').querySelector('.radio-card').classList.add('selected');
                    }
                });
            });

            // GCash field toggle 
            const gcashRadio = document.querySelector('input[name="payment_method"][value="gcash"]');
            const cashRadio = document.querySelector('input[name="payment_method"][value="cash"]');
            const gcashField = document.getElementById("gcashField");
            const refNumberInput = document.getElementById("refNumber");

            function toggleGCashField() {
                if (gcashRadio && gcashRadio.checked) {
                    gcashField.style.display = "block";
                    refNumberInput.setAttribute("required", "true");
                } else {
                    gcashField.style.display = "none";
                    refNumberInput.removeAttribute("required");
                    refNumberInput.value = "";
                }
            }

            if (gcashRadio && cashRadio) {
                toggleGCashField();
                gcashRadio.addEventListener("change", toggleGCashField);
                cashRadio.addEventListener("change", toggleGCashField);
            }

            // Handle pickup radio
            const pickupRadio = document.querySelector('input[name="order_type"][value="pickup"]');
            const dineInRadio = document.querySelector('input[name="order_type"][value="dine-in"]');
            const takeoutRadio = document.querySelector('input[name="order_type"][value="takeout"]');

            function handleOrderTypeChange() {
                const cashOption = cashRadio.closest('.radio-option');
                const cashCard = cashOption.querySelector('.radio-card');

                if (pickupRadio && pickupRadio.checked) {
                  
                    cashRadio.disabled = true;
                    cashCard.style.opacity = '0.5';
                    cashCard.style.cursor = 'not-allowed';

                  
                    if (cashRadio.checked) {
                        gcashRadio.checked = true;
                        gcashRadio.closest('.radio-option').querySelector('.radio-card').classList.add('selected');
                        cashCard.classList.remove('selected');
                        toggleGCashField();
                    }
                } else {
                  
                    cashRadio.disabled = false;
                    cashCard.style.opacity = '1';
                    cashCard.style.cursor = 'pointer';
                }
            }

            if (pickupRadio) {
                pickupRadio.addEventListener('change', handleOrderTypeChange);
            }
            if (dineInRadio) {
                dineInRadio.addEventListener('change', handleOrderTypeChange);
            }
            if (takeoutRadio) {
                takeoutRadio.addEventListener('change', handleOrderTypeChange);
            }

            
            const checkoutBtn = document.getElementById('checkoutBtn');
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                });
            }
        });
    </script>
</body>

</html>