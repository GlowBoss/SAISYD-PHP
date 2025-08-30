<?php
session_start();

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

    <!-- CART & SUMMARY SECTION -->
    <div class="container-fluid mt-5 mt-3 pt-lg-3">
        <div class="row justify-content-center">
            <!-- Cart Card -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card cart-section p-3 border-0" style="max-height: 75vh;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="icon me-2"></div>
                            <div class="subheading2 fw-bold cart-title">Cart</div>
                        </div>
                        <button class="btn btn-outline-danger btn-sm h6 mx-2" id="clearCartBtn">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>

                    <div class="overflow-auto custom-scroll" style="max-height: 49vh;">
                        <!-- Cart Item -->


                        <div class="d-flex justify-content-between align-items-center mb-2 px-2">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-sm rounded-2 btn-outline-dark me-2 fw-bold">-</button>
                                <div class="quantity subheading fw-bold mx-1">1x</div>
                                <button class="btn btn-sm rounded-2 btn-outline-dark ms-2 fw-bold">+</button>
                            </div>
                            <div class="product subheading fw-bold ms-3">Amerikano</div>
                            <div class="price subheading fw-bold ms-2">₱120</div>
                        </div>
                        <div class="text-muted small ms-5">Size: 16oz</div>
                        <div class="text-muted small ms-5">Sugar: 0%</div>
                        <div class="text-muted small ms-5">Ice: Default</div>
                        <div class="text-muted small ms-5">Notes: hello world</div>
                        <hr>




                    </div>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="col-12 col-lg-4 mb-4">
                <div class="card p-3" style="border-radius: 16px; max-height: 75vh;">
                    <h3 class="subheading2 text-start ms-2 pt-2 pb-3">Summary</h3>
                    <div class="d-flex justify-content-evenly mb-2">
                        <p class="lead mb-0">Items: <span id="item-count">0</span></p>
                        <p class="lead mb-0">Total: <span id="order-total">₱0.00</span></p>
                    </div>
                    <hr>

                    <!-- Order Method -->
                    <div class="mx-3 mb-3">
                        <p class="lead">Choose your order method:</p>
                        <div class="row row-cols-2 gx-2">
                            <div class="col">
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input me-2" onclick="updateOrderDataWithSelections()"
                                        type="radio" name="orderType" id="dinein">
                                    <label class="form-check-label" for="dinein">Dine-in</label>
                                </div>
                            </div>
                            <div class="col">
                                <!-- Placeholder for possible future method -->
                            </div>
                            <div class="col">
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input me-2" onclick="updateOrderDataWithSelections()"
                                        type="radio" name="orderType" id="takeout">
                                    <label class="form-check-label" for="takeout">Takeout</label>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-check d-flex align-items-center">
                                    <input class="form-check-input me-2" onclick="updateOrderDataWithSelections()"
                                        type="radio" name="orderType" id="pickup">
                                    <label class="form-check-label" for="pickup">Pickup</label>
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
                                <input class="form-check-input me-2" onclick="updateOrderDataWithSelections()"
                                    type="radio" name="modePayment" id="cash">
                                <label class="form-check-label" onclick="updateOrderDataWithSelections()"
                                    for="cash">Cash</label>
                            </div>
                            <div class="form-check my-2">
                                <input class="form-check-input me-2" onclick="updateOrderDataWithSelections()"
                                    type="radio" name="modePayment" id="gcash">
                                <label class="form-check-label" onclick="updateOrderDataWithSelections()"
                                    for="gcash">GCash</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="refNumber" class="form-label">Enter Reference Number</label>
                                <input type="text" class="form-control" id="refNumber"
                                    placeholder="1234-5678-910 (Required)" required>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="text-end px-3 mb-3">
                        <h3 class="subheading3">TOTAL: <span id="grand-total">₱0.00</span></h3>
                    </div>
                    <button class="btn buy-btn rounded-5 mx-auto mb-2">Checkout</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Placeholder for modal -->
    <div id="modal-placeholder"></div>

    <!-- Scripts -->
    <script src="assets/js/swiper-bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Load modal content and attach cart logic
        // fetch("modal/cart-modal.php")
        //     .then(res => res.text())
        //     .then(data => {
        //         document.getElementById("modal-placeholder").innerHTML = data;

        //         const rawData = localStorage.getItem("orders");
        //         let orderData = rawData ? JSON.parse(rawData) : [];

        //         const cartContainer = document.querySelector('.overflow-auto');
        //         if (!cartContainer) return;

        //         // Clear container
        //         cartContainer.innerHTML = '';

        //         // Check if cart is empty
        //         if (orderData.length === 0) {
        //             cartContainer.innerHTML = '<div class="text-muted text-center py-4">Your cart is empty.</div>';
        //             updateOrderSummary(); // Update summary for empty cart
        //             return;
        //         }

        // Render cart items
        orderData.forEach((item, index) => {
            const itemHTML = document.createElement('div');
            itemHTML.classList.add('cart-item-wrapper');

            const displayPrice = typeof item.price === 'string' ? item.price : `₱${item.price}`;
            const displayName = item.displayName || item.name.split('_')[0] || item.name;
            const isDrink = !displayName.toLowerCase().includes('sandwich');

            itemHTML.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary me-2 minus-btn" data-index="${index}">-</button>
                            <div class="quantity subheading fw-bold mx-1" id="qty-${index}">${item.quantity}x</div>
                            <button class="btn btn-sm btn-outline-secondary ms-2 plus-btn" data-index="${index}">+</button>
                        </div>
                        <div class="product subheading fw-bold ms-3">${displayName}</div>
                        <div class="price subheading fw-bold ms-2">${displayPrice}</div>
                    </div>
                    ${item.size ? `<div class="text-muted small ms-5">Size: ${item.size}</div>` : ''}
                    ${item.sugar ? `<div class="text-muted small ms-5">Sugar: ${item.sugar}%</div>` : ''}
                    ${(item.ice && isDrink) ? `<div class="text-muted small ms-5">Ice: ${item.ice}</div>` : ''}
                    ${item.notes ? `<div class="text-muted small ms-5">Notes: ${item.notes}</div>` : ''}
                    <hr>
                `;
            cartContainer.appendChild(itemHTML);
        });

        // Update cart totals
        updateCartTotal(orderData);
        updateOrderSummary();

        // Update cart UI
        function updateCartUI(index) {
            document.getElementById(`qty-${index}`).textContent = `${orderData[index].quantity}x`;
            updateCartTotal(orderData);
            updateOrderSummary();
        }

        // Save cart to localStorage
        function saveCart() {
            localStorage.setItem("orders", JSON.stringify(orderData));
            updateOrderSummary();
        }

        // Plus buttons
        document.querySelectorAll('.plus-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                const index = parseInt(e.target.getAttribute('data-index'));
                orderData[index].quantity++;
                updateCartUI(index);
                saveCart();
            });
        });

        // Minus buttons
        document.querySelectorAll('.minus-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                const index = parseInt(e.target.getAttribute('data-index'));
                if (orderData[index].quantity > 1) {
                    orderData[index].quantity--;
                    updateCartUI(index);
                    saveCart();
                } else {
                    if (confirm("Remove this item from cart?")) {
                        orderData.splice(index, 1);
                        saveCart();
                        location.reload();
                    }
                }
            });
        });

        // Confirm button in modal
        const addBtn = document.querySelector('.addbtn');
        if (addBtn) {
            addBtn.addEventListener('click', function (e) {
                e.preventDefault();
                confirmOrder();
            });
        }

        // Clear cart button
        const clearCartBtn = document.getElementById('clearCartBtn');
        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', function () {
                if (confirm("Are you sure you want to clear the cart?")) {
                    localStorage.removeItem("orders");
                    cartContainer.innerHTML = '<div class="text-muted text-center py-4">Your cart is empty.</div>';
                    updateCartTotal([]);
                    updateOrderSummary();
                }
            });
        }
            })
            .catch (error => {
            console.error('Error loading cart modal:', error);
        });

        // Calculate and display total
        function updateCartTotal(orderData) {
            let total = 0;
            orderData.forEach(item => {
                const price = typeof item.price === 'string' ?
                    parseFloat(item.price.replace('₱', '')) :
                    parseFloat(item.price);
                total += price * item.quantity;
            });

            const totalElement = document.querySelector('.cart-total');
            if (totalElement) {
                totalElement.textContent = `Total: ₱${total.toFixed(2)}`;
            }
        }

        // Calculate order summary
        function updateOrderSummary() {
            const rawData = localStorage.getItem("orders");
            const orderData = rawData ? JSON.parse(rawData) : [];

            let subtotal = 0;
            let itemCount = 0;

            orderData.forEach(item => {
                const price = typeof item.price === 'string' ?
                    parseFloat(item.price.replace('₱', '')) :
                    item.price;
                subtotal += price * item.quantity;
                itemCount += item.quantity;
            });

            if (document.getElementById('item-count')) {
                document.getElementById('item-count').textContent = itemCount;
            }
            if (document.getElementById('order-total')) {
                document.getElementById('order-total').textContent = `₱${subtotal.toFixed(2)}`;
            }
            if (document.getElementById('grand-total')) {
                document.getElementById('grand-total').textContent = `₱${subtotal.toFixed(2)}`;
            }
        }

        // Open confirmation modal
        function openPopup() {
            const rawData = localStorage.getItem("orders");
            const orderData = rawData ? JSON.parse(rawData) : [];

            if (orderData.length === 0) {
                alert("Your cart is empty. Please add items before checking out.");
                return;
            }

            updateModalOrderSummary(orderData);

            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }

        // Update modal order summary
        function updateModalOrderSummary(orderData) {
            const orderSummaryList = document.getElementById('orderSummaryList');
            if (!orderSummaryList) return;

            orderSummaryList.innerHTML = '';

            orderData.forEach(item => {
                const li = document.createElement('li');
                const displayName = item.displayName || item.name.split('_')[0] || item.name;
                const isDrink = !displayName.toLowerCase().includes('sandwich');

                li.textContent = `${item.quantity}x ${displayName}`;
                orderSummaryList.appendChild(li);

                if (item.size || item.sugar || (item.ice && isDrink) || item.notes) {
                    const details = document.createElement('div');
                    details.className = 'text-muted small ms-3';

                    const detailsText = [];
                    if (item.size) detailsText.push(`Size: ${item.size}`);
                    if (item.sugar) detailsText.push(`Sugar: ${item.sugar}%`);
                    if (item.ice && isDrink) detailsText.push(`Ice: ${item.ice}`);
                    if (item.notes) detailsText.push(`Notes: ${item.notes}`);

                    details.textContent = detailsText.join(', ');
                    li.appendChild(details);
                }
            });

            const total = orderData.reduce((sum, item) => {
                const price = typeof item.price === 'string' ?
                    parseFloat(item.price.replace('₱', '')) :
                    item.price;
                return sum + (price * item.quantity);
            }, 0);

            const totalLi = document.createElement('li');
            totalLi.innerHTML = `<hr><strong>Total: ₱${total.toFixed(2)}</strong>`;
            orderSummaryList.appendChild(totalLi);
        }

        // Get selected radio button
        function getSelectedRadio(name) {
            const selected = document.querySelector(`input[name="${name}"]:checked`);
            return selected ? selected.id : null;
        }

        // Confirm order
        function confirmOrder() {
            const orderMethod = getSelectedRadio("orderType");
            const paymentMethod = getSelectedRadio("modePayment");
            const refNumber = document.getElementById("refNumber")?.value.trim();

            if (!orderMethod || !paymentMethod) {
                alert("Please select both order method and payment method.");
                return;
            }

            if (paymentMethod === "gcash" && (!refNumber || refNumber === "")) {
                alert("Please enter a GCash reference number.");
                return;
            }

            const rawData = localStorage.getItem("orders");
            const items = rawData ? JSON.parse(rawData) : [];

            // Optional: calculate total amount
            const total = items.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);

            // Create the new order object with a structure like you want
            const newOrder = {
                items: [...items],                         // all cart items
                total: total,                              // computed total
                paymentMode: paymentMethod,                // e.g. "Cash" or "Gcash"
                refNumber: paymentMethod === "gcash" ? refNumber : null,
                timestamp: new Date().toISOString()
            };

            // Append this new order to orderData list
            const completedRaw = localStorage.getItem("orderData");
            const orderData = completedRaw ? JSON.parse(completedRaw) : [];

            orderData.push(newOrder);

            // Save updated list
            localStorage.setItem("orderData", JSON.stringify(orderData));

            // Clear the current cart
            localStorage.removeItem("orders");

            // Close modal and show toast
            bootstrap.Modal.getInstance(document.getElementById('confirmModal'))?.hide();
            new bootstrap.Toast(document.getElementById('orderToast')).show();


            updateOrderSummary();

            setTimeout(() => {
                location.reload();
            }, 1500);
        }

        // Update cart badge
        function updateCartBadge() {
            const rawData = localStorage.getItem("orders");
            const orderData = rawData ? JSON.parse(rawData) : [];
            const totalItems = orderData.reduce((sum, item) => sum + item.quantity, 0);

            const badge = document.querySelector('.cart-badge');
            if (badge) {
                badge.textContent = totalItems;
                badge.style.display = totalItems > 0 ? 'block' : 'none';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            updateCartBadge();
            updateOrderSummary();
        });

        document.addEventListener('DOMContentLoaded', function () {
            updateCartBadge();
            updateOrderSummary();

            // Disable "Cash" payment when "Pickup" is selected
            const orderTypeRadios = document.querySelectorAll('input[name="orderType"]');
            const cashRadio = document.getElementById('cash');

            function toggleCashOption() {
                const selected = document.querySelector('input[name="orderType"]:checked');
                if (selected && selected.id === "pickup") {
                    cashRadio.disabled = true;
                    // Optional: uncheck it if currently selected
                    if (cashRadio.checked) {
                        cashRadio.checked = false;
                    }
                } else {
                    cashRadio.disabled = false;
                }
            }

            orderTypeRadios.forEach(radio => {
                radio.addEventListener('change', toggleCashOption);
            });

            // Initial call on page load
            toggleCashOption();
        });

    </script>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/navbar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>


</html>