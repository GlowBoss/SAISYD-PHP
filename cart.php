<?php
include 'assets/connect.php';
session_start();

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

                $orderResult = executeQuery("SELECT * FROM orders WHERE status='pending' LIMIT 1");
                if (mysqli_num_rows($orderResult) > 0) {
                    $order = mysqli_fetch_assoc($orderResult);
                    $orderID = $order['orderID'];

                    // update quantity in database
                    $productId = $_SESSION['cart'][$index]['product_id'];
                    $sugar = $_SESSION['cart'][$index]['sugar'];
                    $ice = $_SESSION['cart'][$index]['ice'];
                    $notes = $_SESSION['cart'][$index]['notes'];

                    $updateQuery = "UPDATE orderitems SET quantity = '$newQuantity' 
                                   WHERE orderID = '$orderID' AND productID = '$productId' 
                                   AND sugar = '" . mysqli_real_escape_string($conn, $sugar) . "' 
                                   AND ice = '" . mysqli_real_escape_string($conn, $ice) . "' 
                                   AND notes = '" . mysqli_real_escape_string($conn, $notes) . "'";
                    executeQuery($updateQuery);
                }
            } else {
                // remove item if quantity is 0
                $productId = $_SESSION['cart'][$index]['product_id'];
                $sugar = $_SESSION['cart'][$index]['sugar'];
                $ice = $_SESSION['cart'][$index]['ice'];
                $notes = $_SESSION['cart'][$index]['notes'];

                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);

                // remove from database
                $orderResult = executeQuery("SELECT * FROM orders WHERE status='pending' LIMIT 1");
                if (mysqli_num_rows($orderResult) > 0) {
                    $order = mysqli_fetch_assoc($orderResult);
                    $orderID = $order['orderID'];

                    $deleteQuery = "DELETE FROM orderitems 
                                   WHERE orderID = '$orderID' AND productID = '$productId' 
                                   AND sugar = '" . mysqli_real_escape_string($conn, $sugar) . "' 
                                   AND ice = '" . mysqli_real_escape_string($conn, $ice) . "' 
                                   AND notes = '" . mysqli_real_escape_string($conn, $notes) . "'";
                    executeQuery($deleteQuery);
                }
            }
        }
    }

    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];

        // Clear database
        $orderResult = executeQuery("SELECT * FROM orders WHERE status='pending' LIMIT 1");
        if (mysqli_num_rows($orderResult) > 0) {
            $order = mysqli_fetch_assoc($orderResult);
            $orderID = $order['orderID'];
            executeQuery("DELETE FROM orderitems WHERE orderID = '$orderID'");
            executeQuery("DELETE FROM orders WHERE orderID = '$orderID'");
        }
    }

    if (isset($_POST['checkout'])) {
        $orderType = $_POST['order_type'] ?? '';
        $paymentMethod = $_POST['payment_method'] ?? '';
        $refNumber = $_POST['ref_number'] ?? '';
        $customerName = $_POST['customer_name'] ?? '';

        if (empty($orderType) || empty($paymentMethod)) {
            $_SESSION['cart_error'] = 'Please select both order method and payment method.';
        } elseif ($paymentMethod === 'gcash' && empty($refNumber)) {
            $_SESSION['cart_error'] = 'Please enter a GCash reference number.';
        } elseif (empty($_SESSION['cart'])) {
            $_SESSION['cart_error'] = 'Your cart is empty.';
        } elseif ($orderType === 'pickup' && empty($customerName)) {
            $_SESSION['cart_error'] = 'Please enter your name for pickup orders.';
        } else {
            // Calculate total
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            

            // ✅ Generate new order number
            $result = executeQuery("SELECT orderNumber FROM orders ORDER BY orderID DESC LIMIT 1");
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $lastOrderNumber = $row['orderNumber'];

                $lastNumber = intval(substr($lastOrderNumber, 4));
                $newNumber = str_pad($lastNumber + 1, 4, "0", STR_PAD_LEFT);
                $orderNumber = "ORD-" . $newNumber;
            } else {
                $orderNumber = "ORD-0001";
            }

            $customerName = $_POST['customer_name'] ?? '';
            $customerPhone = $_POST['customer_phone'] ?? '';

            if ($orderType === 'pickup' && empty($customerName)) {
                $_SESSION['cart_error'] = 'Please enter your name for pickup orders.';
            } elseif ($orderType === 'pickup' && empty($customerPhone)) {
                $_SESSION['cart_error'] = 'Please enter your phone number for pickup orders.';
            }

            // ...

            if ($orderType === 'pickup') {
                $customerName = mysqli_real_escape_string($conn, $customerName);
                $customerPhone = mysqli_real_escape_string($conn, $customerPhone);
            } else {
                $customerName = null;
                $customerPhone = null;
            }

            // Insert order with phone
            $insertOrderQuery = "INSERT INTO orders (orderDate, orderNumber, customerName, orderContactNumber, address, totalAmount, status, orderType) 
                     VALUES (NOW(), '$orderNumber', " .
                ($customerName ? "'$customerName'" : "NULL") . ", " .
                ($customerPhone ? "'$customerPhone'" : "NULL") . ",
                     '', '$total', 'pending', '" . mysqli_real_escape_string($conn, $orderType) . "')";
            executeQuery($insertOrderQuery);

            // Get the new order ID
            $orderID = mysqli_insert_id($conn);

            // Insert payment record
            $insertPayment = "INSERT INTO payments (paymentMethod, paymentStatus, referenceNumber, orderID) 
                              VALUES (
                                  '" . mysqli_real_escape_string($conn, $paymentMethod) . "',
                                  'pending',
                                  '" . mysqli_real_escape_string($conn, $refNumber) . "',
                                  '$orderID'
                              )";
            executeQuery($insertPayment);

            // Clear session cart
            $_SESSION['cart'] = [];
            $_SESSION['cart_message'] = 'Order placed successfully! Order ID: ' . $orderID;

            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
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
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="icon" href="../assets/img/round_logo.png" type="image/png">
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

        <button class="btn menu-btn" onclick="location.href='menu.php'">
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
                        <a class="nav-link" href="#about">
                            <i class="bi bi-info-circle"></i> <span>About</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#location">
                            <i class="bi bi-geo-alt"></i> <span>Location</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">
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

    <div class="container-fluid mt-5 mt-3 pt-lg-3">
        <div class="row justify-content-center">

            <!-- CART -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card cart-section p-3 border-0" style="max-height: 75vh;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="subheading2 fw-bold">Cart</div>
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <form method="POST">
                                <button type="submit" name="clear_cart" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div class="overflow-auto" style="max-height: 49vh;">
                        <?php if (empty($_SESSION['cart'])): ?>
                            <p class="text-muted text-center">Your cart is empty.</p>
                        <?php else: ?>
                            <?php foreach ($_SESSION['cart'] as $i => $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                                    <div class="d-flex align-items-center">
                                        <form method="POST">
                                            <input type="hidden" name="cart_index" value="<?= $i ?>">
                                            <input type="hidden" name="new_quantity"
                                                value="<?= max(0, $item['quantity'] - 1) ?>">
                                            <button type="submit" name="update_quantity"
                                                class="btn btn-sm btn-outline-dark fw-bold">-</button>
                                        </form>
                                        <div class="mx-2 fw-bold"><?= $item['quantity'] ?>x</div>
                                        <form method="POST">
                                            <input type="hidden" name="cart_index" value="<?= $i ?>">
                                            <input type="hidden" name="new_quantity" value="<?= $item['quantity'] + 1 ?>">
                                            <button type="submit" name="update_quantity"
                                                class="btn btn-sm btn-outline-dark fw-bold">+</button>
                                        </form>
                                    </div>
                                    <div class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></div>
                                    <div class="fw-bold">₱<?= number_format($item['price'], 2) ?></div>
                                </div>
                                <?php if ($item['sugar']): ?>
                                    <div class="small text-muted ms-5">Sugar: <?= $item['sugar'] ?></div><?php endif; ?>
                                <?php if ($item['ice']): ?>
                                    <div class="small text-muted ms-5">Ice: <?= $item['ice'] ?></div><?php endif; ?>
                                <?php if ($item['notes']): ?>
                                    <div class="small text-muted ms-5">Notes: <?= $item['notes'] ?></div><?php endif; ?>
                                <hr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- SUMMARY -->
            <div class="col-12 col-lg-4 mb-4">
                <div class="card p-3" style="border-radius: 16px; max-height: 75vh;">
                    <h3 class="subheading2 text-start ms-2 pt-2 pb-3">Summary</h3>
                    <div class="d-flex justify-content-evenly mb-2">
                        <p class="lead mb-0">Items: <?= getCartItemCount() ?></p>
                        <p class="lead mb-0">Total: ₱<?= number_format(getCartTotal(), 2) ?></p>
                    </div>
                    <hr>

                    <?php if (!empty($_SESSION['cart'])): ?>
                        <form method="POST">
                            <!-- Order Method -->
                            <div class="mx-3 mb-3">
                                <p class="lead">Choose your order method:</p>
                                <div class="row row-cols-2 gx-2">
                                    <div class="col">
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-2" type="radio" name="order_type"
                                                value="dine-in" required>
                                            <label class="form-check-label">Dine-in</label>
                                        </div>
                                    </div>
                                    <div class="col"></div>
                                    <div class="col">
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-2" type="radio" name="order_type"
                                                value="takeout" required>
                                            <label class="form-check-label">Takeout</label>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-2" type="radio" name="order_type"
                                                value="pickup" required>
                                            <label class="form-check-label">Pickup</label>
                                            <input type="hidden" name="customer_name" id="hiddenCustomerName">
                                            <input type="hidden" name="customer_phone" id="hiddenCustomerPhone">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Payment Method -->
                            <div class="row row-cols-1 row-cols-md-2 g-4 mx-2 mb-3">
                                <div class="col">
                                    <p class="lead">Mode of Payment:</p>
                                    <div class="form-check my-2">
                                        <input class="form-check-input me-2" type="radio" name="payment_method" value="cash"
                                            required>
                                        <label class="form-check-label">Cash</label>
                                    </div>
                                    <div class="form-check my-2">
                                        <input class="form-check-input me-2" type="radio" name="payment_method"
                                            value="gcash" required>
                                        <label class="form-check-label">GCash</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3" id="gcashField" style="display:none;">
                                        <input type="text" class="form-control" name="ref_number" id="refNumber"
                                            placeholder="Enter 13-digit GCash Ref No." pattern="\d{13}" maxlength="13"
                                            inputmode="numeric"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,13);">
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="text-end px-3 mb-3">
                                <h3 class="subheading3">TOTAL: ₱<?= number_format(getCartTotal(), 2) ?></h3>
                            </div>
                            <button type="submit" name="checkout"
                                class="btn buy-btn rounded-5 mx-auto mb-2">Checkout</button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted">Add items to your cart to proceed.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const pickupRadio = document.querySelector('input[name="order_type"][value="pickup"]');
            const modalEl = document.getElementById('confirmModal');
            if (!modalEl) return;

            const confirmModal = new bootstrap.Modal(modalEl);
            const orderSummaryList = document.getElementById("orderSummaryList");
            const confirmBtn = modalEl.querySelector(".confirm-order-btn");
            const checkoutForm = document.querySelector("form"); // Main checkout form
            const toastEl = document.getElementById('orderToast');
            const orderToast = new bootstrap.Toast(toastEl);

            if (pickupRadio) {
                pickupRadio.addEventListener("change", () => {
                    if (pickupRadio.checked) {
                        // Populate order summary
                        orderSummaryList.innerHTML = "";
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                orderSummaryList.innerHTML += `<li><?= $item['quantity'] ?>x <?= htmlspecialchars($item['product_name']) ?></li>`;
                            <?php endforeach; ?>
                        <?php endif; ?>

                        // Show modal
                        confirmModal.show();
                    }
                });
            }

            if (confirmBtn && checkoutForm) {
                confirmBtn.addEventListener("click", () => {
                    const customerNameInput = document.getElementById('pickupCustomerName');
                    let hiddenInput = checkoutForm.querySelector('input[name="customer_name"]');

                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'customer_name';
                        checkoutForm.appendChild(hiddenInput);
                    }

                    hiddenInput.value = customerNameInput.value.trim();

                    confirmModal.hide();
                    checkoutForm.querySelector("button[name='checkout']").click();
                    orderToast.show();
                });
            }
        });
    </script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/navbar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
    <?php include 'modal/cart-modal.php'; ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const pickupRadio = document.querySelector('input[name="order_type"][value="pickup"]');
            const modalEl = document.getElementById('confirmModal');
            if (!modalEl) return;

            const confirmModal = new bootstrap.Modal(modalEl);
            const orderSummaryList = document.getElementById("orderSummaryList");
            const confirmBtn = modalEl.querySelector(".confirm-order-btn");
            const checkoutForm = document.querySelector("form"); // Main checkout form

            // When Pickup is selected, show modal
            if (pickupRadio) {
                pickupRadio.addEventListener("change", () => {
                    if (pickupRadio.checked) {
                        // Populate order summary
                        orderSummaryList.innerHTML = "";
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                orderSummaryList.innerHTML += `<li><?= $item['quantity'] ?>x <?= htmlspecialchars($item['product_name']) ?></li>`;
                            <?php endforeach; ?>
                        <?php endif; ?>

                        // Show modal
                        confirmModal.show();
                    }
                });
            }

            // When Confirm is clicked in modal, save customer name only
            if (confirmBtn && checkoutForm) {
                confirmBtn.addEventListener("click", () => {
                    const customerNameInput = document.getElementById('pickupCustomerName');
                    let hiddenInput = checkoutForm.querySelector('input[name="customer_name"]');

                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'customer_name';
                        checkoutForm.appendChild(hiddenInput);
                    }

                    hiddenInput.value = customerNameInput.value.trim();
                    confirmModal.hide();
                });
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const gcashRadio = document.querySelector('input[name="payment_method"][value="gcash"]');
            const cashRadio = document.querySelector('input[name="payment_method"][value="cash"]');
            const gcashField = document.getElementById("gcashField");
            const refNumberInput = document.getElementById("refNumber");

            function toggleGCashField() {
                if (gcashRadio.checked) {
                    gcashField.style.display = "block";
                    refNumberInput.setAttribute("required", "true"); // make it required only for GCash
                } else {
                    gcashField.style.display = "none";
                    refNumberInput.removeAttribute("required");
                    refNumberInput.value = ""; // clear if switching back
                }
            }

            // Run on page load
            toggleGCashField();

            // Attach listeners
            gcashRadio.addEventListener("change", toggleGCashField);
            cashRadio.addEventListener("change", toggleGCashField);
        });
    </script>
</body>

</html>