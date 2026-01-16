<?php
include('auth_check.php');
include '../assets/connect.php';

// Check if user is logged in and is an admin 
if (!isset($_SESSION['userID']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: logout.php");
    exit();
}

//Count Pending Orders
$pendingOrdersQuery = "SELECT COUNT(*) AS pending_count FROM orders WHERE status = 'Pending'";
$result = mysqli_query($conn, $pendingOrdersQuery);

$pendingCount = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $pendingCount = $row['pending_count'];
}

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
    <link rel="stylesheet" href="../assets/css/pos-offcanvas.css">

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
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Remove number input arrows in Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<body>
    <div class="container-fluid mainContainer p-2">
        <div class="row p-0">
            <!-- Offcanvas Sidebar -->
            <div class="offcanvas offcanvas-start admin-sidebar-offcanvas" tabindex="-1" id="offcanvasSidebar"
                aria-labelledby="offcanvasSidebarLabel">

                <div class="offcanvas-header d-flex align-items-center justify-content-between">
                    <img src="../assets/img/saisydLogo.png" alt="Saisyd Cafe Logo" class="admin-logo me-2 me-md-0"
                        style="max-height: clamp(50px, 6vh, 70px); width: auto;" />
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <!-- Body with Admin Navigation Design -->
                <div class="offcanvas-body" id="sidebarNav">
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
                        <a href="settings.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                            data-wow-delay="0.4s">
                            <i class="bi bi-gear"></i>
                            <span>Settings</span>
                        </a>
                        <a href="logout.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
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
                                <div class="d-flex mt-2 justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <button class="btn btn-sm mobile-menu-toggle pt-2" data-bs-toggle="offcanvas"
                                            data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar"
                                            aria-label="Toggle sidebar">
                                            <i class="fa fa-bars"></i>
                                        </button>
                                        <h5 class="heading fw-semibold mb-0 pt-2 text-center text-lg-start">Point of
                                            Sale System</h5>
                                    </div>
                                    <a href="orders.php" class="notification-btn position-relative p-2">
                                        <i class="bi bi-bell fs-4"></i>
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            <?php echo $pendingCount; ?>
                                        </span>
                                    </a>

                                </div>
                                <div class="subheading py-3 px-2">Category</div>

                                <div class="category-scroll d-flex gap-3 overflow-auto pb-3" id="categories">
                                    <!-- Categories -->
                                </div>
                            </div>
                        </div>

                        <div class="card overflow-auto p-3 maincontainer" style="height: 70vh">
                            <div class="subheading px-2 mb-3">
                                Items
                            </div>
                            <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6"
                                id="maincontainer">
                                <!-- Menu items -->
                            </div>
                        </div>
                    </div>

                    <div class="flex-lg-shrink-0 ms-0 ms-lg-3 mt-3 mt-lg-0 receipt-container">
                        <div class="card p-3 receiptCard" style="height: 100%;">
                            <div class="category-title">Receipt</div>
                            <div class="container-fluid">
                                <div class="line-divider"></div>
                            </div>

                            <!-- Scrollable receipt list -->
                            <div id="receipt" style="max-height: 600px; overflow-y: auto;">
                                <!-- receipt items -->
                            </div>

                            <div class="container-fluid">
                                <div class="line-divider" style="height: 1px;"></div>

                                <div class="mt-4 d-flex flex-row justify-content-between">
                                    <div><b>TOTAL</b></div>
                                    <div><b id="totalValue">0</b></div>
                                </div>

                                <!-- Cash Input Section -->
                                <div class="mt-3">
                                    <label for="cashInput" class="form-label fw-bold">Cash Amount</label>
                                    <input type="number" class="form-control" id="cashInput"
                                        placeholder="Enter cash amount" min="0" step="0.01" oninput="calculateChange()">
                                </div>

                                <!-- Change Display -->
                                <div class="mt-3 d-flex flex-row justify-content-between align-items-center p-2 bg-light rounded"
                                    id="changeDisplay" style="display: none !important;">
                                    <div><b>CHANGE</b></div>
                                    <div><b class="text-success" id="changeValue">0.00</b></div>
                                </div>

                                <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
                                    <button class="btn btn-dark-order w-100 w-md-auto py-2 px-3"
                                        onclick="openPopup()">Order Now</button>
                                    <button class="btn btn-dark-cancel w-100 w-md-auto py-2 px-3"
                                        onclick="cancelOrder()">Cancel Order</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Order Confirmation Modal -->
            <div id="modal-placeholder"></div>

            <script>
                // Add this JavaScript function to calculate change
                function calculateChange() {
                    const cashInput = document.getElementById('cashInput');
                    const totalValueElement = document.getElementById('totalValue');
                    const changeValueElement = document.getElementById('changeValue');
                    const changeDisplay = document.getElementById('changeDisplay');

                    const cashAmount = parseFloat(cashInput.value) || 0;
                    const totalAmount = parseFloat(totalValueElement.textContent) || 0;

                    if (cashAmount > 0 && totalAmount > 0) {
                        const change = cashAmount - totalAmount;

                        if (change >= 0) {
                            changeValueElement.textContent = '₱' + change.toFixed(2);
                            changeValueElement.classList.remove('text-danger');
                            changeValueElement.classList.add('text-success');
                            changeDisplay.style.display = 'flex';
                            changeDisplay.style.removeProperty('display'); // Remove inline style
                            changeDisplay.style.display = 'flex'; // Set to flex
                        } else {
                            changeValueElement.textContent = '₱' + Math.abs(change).toFixed(2) + ' short';
                            changeValueElement.classList.remove('text-success');
                            changeValueElement.classList.add('text-danger');
                            changeDisplay.style.display = 'flex';
                            changeDisplay.style.removeProperty('display'); // Remove inline style
                            changeDisplay.style.display = 'flex'; // Set to flex
                        }
                    } else {
                        changeDisplay.style.display = 'none';
                    }
                }

                // Function to clear cash input and change display
                function clearCashInput() {
                    const cashInput = document.getElementById('cashInput');
                    const changeDisplay = document.getElementById('changeDisplay');
                    const changeValue = document.getElementById('changeValue');

                    if (cashInput) {
                        cashInput.value = '';
                    }
                    if (changeDisplay) {
                        changeDisplay.style.display = 'none';
                    }
                    if (changeValue) {
                        changeValue.textContent = '0.00';
                        changeValue.classList.remove('text-danger');
                        changeValue.classList.add('text-success');
                    }
                }
            </script>
            <script>
                // ==============================================
                // POS MODAL QUANTITY MANAGEMENT - COMPLETE SCRIPT
                // Add this to point-of-sales.php BEFORE the modal loads
                // ==============================================

                // Global variable to store current product's available quantity
                window.currentAvailableQuantity = 0;

                // Function to initialize quantity modal when opened
                window.initializeQuantityModal = function (availableQty) {
                    // Set the available quantity
                    window.currentAvailableQuantity = parseInt(availableQty) || 0;

                    console.log('✓ Initializing modal with available quantity:', window.currentAvailableQuantity);

                    // Update available stock display if it exists
                    const availableStockSpan = document.getElementById('availableStock');
                    if (availableStockSpan) {
                        availableStockSpan.textContent = availableQty;
                    }

                    // Reset quantity to 1
                    const quantityInput = document.getElementById('quantity');
                    const modalQuantityInput = document.getElementById('modal-quantity-input');
                    if (quantityInput) {
                        quantityInput.value = 1;
                        quantityInput.setAttribute('max', availableQty);
                    }
                    if (modalQuantityInput) modalQuantityInput.value = 1;

                    // Update button states immediately
                    window.updateButtonStates(1);
                };

                // Update button states based on current quantity
                window.updateButtonStates = function (currentQty) {
                    const decreaseBtn = document.querySelector('#quantityModal button[onclick="decreaseQuantity()"]');
                    const increaseBtn = document.querySelector('#quantityModal button[onclick="increaseQuantity()"]');

                    console.log('Updating buttons - Current:', currentQty, 'Max:', window.currentAvailableQuantity);

                    // Disable decrease button if at minimum (1)
                    if (decreaseBtn) {
                        if (currentQty <= 1) {
                            decreaseBtn.disabled = true;
                            decreaseBtn.style.opacity = '0.5';
                            decreaseBtn.style.cursor = 'not-allowed';
                        } else {
                            decreaseBtn.disabled = false;
                            decreaseBtn.style.opacity = '1';
                            decreaseBtn.style.cursor = 'pointer';
                        }
                    }

                    // Disable increase button if at maximum (available stock)
                    if (increaseBtn) {
                        if (currentQty >= window.currentAvailableQuantity) {
                            increaseBtn.disabled = true;
                            increaseBtn.style.opacity = '0.5';
                            increaseBtn.style.cursor = 'not-allowed';
                            console.log('✓ Plus button DISABLED at max stock');
                        } else {
                            increaseBtn.disabled = false;
                            increaseBtn.style.opacity = '1';
                            increaseBtn.style.cursor = 'pointer';
                            console.log('✓ Plus button enabled');
                        }
                    }
                };

                // Validate manual input - fires on every keystroke
                window.validateQuantityInput = function (event) {
                    const quantityInput = document.getElementById('quantity');
                    const modalQuantityInput = document.getElementById('modal-quantity-input');

                    if (quantityInput && modalQuantityInput) {
                        let value = quantityInput.value;

                        console.log('⌨️ Input detected:', value, '| Max:', window.currentAvailableQuantity);

                        // Remove any non-digit characters
                        value = value.replace(/\D/g, '');

                        // If empty, don't set anything yet
                        if (value === '') {
                            quantityInput.value = '';
                            return;
                        }

                        // Convert to number
                        let numValue = parseInt(value);

                        // If exceeds stock, immediately cap it
                        if (numValue > window.currentAvailableQuantity) {
                            console.log('✗ Exceeded max! Capping at:', window.currentAvailableQuantity);
                            numValue = window.currentAvailableQuantity;
                        }

                        // If less than 1, set to 1
                        if (numValue < 1) {
                            numValue = 1;
                        }

                        // Update both inputs immediately
                        quantityInput.value = numValue;
                        modalQuantityInput.value = numValue;
                        window.updateButtonStates(numValue);
                    }
                };

                // Additional validation on blur
                window.validateOnBlur = function () {
                    const quantityInput = document.getElementById('quantity');
                    const modalQuantityInput = document.getElementById('modal-quantity-input');

                    if (quantityInput && modalQuantityInput) {
                        let value = parseInt(quantityInput.value);

                        // If empty or invalid, set to 1
                        if (isNaN(value) || value < 1 || quantityInput.value === '') {
                            value = 1;
                        }

                        // If exceeds stock, cap at max
                        if (value > window.currentAvailableQuantity) {
                            value = window.currentAvailableQuantity;
                        }

                        quantityInput.value = value;
                        modalQuantityInput.value = value;
                        window.updateButtonStates(value);
                    }
                };

                // Form validation before submit
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(function () {
                        const addToOrderForm = document.getElementById('addToOrderForm');
                        if (addToOrderForm) {
                            addToOrderForm.addEventListener('submit', function (e) {
                                const qty = parseInt(document.getElementById('quantity').value);

                                if (isNaN(qty) || qty <= 0) {
                                    e.preventDefault();
                                    alert("Quantity must be at least 1.");
                                    return false;
                                }

                                if (qty > window.currentAvailableQuantity) {
                                    e.preventDefault();
                                    alert(`Only ${window.currentAvailableQuantity} pcs available. Please reduce quantity.`);
                                    return false;
                                }
                            });
                        }
                    }, 500);
                });

                // Reset modal when closed
                document.addEventListener('DOMContentLoaded', function () {
                    setTimeout(function () {
                        const quantityModal = document.getElementById('quantityModal');
                        if (quantityModal) {
                            quantityModal.addEventListener('hidden.bs.modal', function () {
                                const quantityInput = document.getElementById('quantity');
                                const modalQuantityInput = document.getElementById('modal-quantity-input');

                                if (quantityInput) quantityInput.value = 1;
                                if (modalQuantityInput) modalQuantityInput.value = 1;

                                window.currentAvailableQuantity = 0;

                                const decreaseBtn = document.querySelector('#quantityModal button[onclick="decreaseQuantity()"]');
                                const increaseBtn = document.querySelector('#quantityModal button[onclick="increaseQuantity()"]');

                                if (decreaseBtn) {
                                    decreaseBtn.disabled = false;
                                    decreaseBtn.style.opacity = '1';
                                    decreaseBtn.style.cursor = 'pointer';
                                }

                                if (increaseBtn) {
                                    increaseBtn.disabled = false;
                                    increaseBtn.style.opacity = '1';
                                    increaseBtn.style.cursor = 'pointer';
                                }

                                console.log('✓ Modal reset');
                            });
                        }
                    }, 500);
                });

                console.log('✓ POS Modal Quantity Management loaded globally');</script>
            <script>
                const quantityInput = document.getElementById('quantity');

                if (quantityInput) {
                    quantityInput.addEventListener('input', (e) => {
                        e.target.value = e.target.value.replace(/\D/g, '');

                        const min = parseInt(e.target.min) || 1;
                        const max = parseInt(e.target.max) || window.currentAvailableQuantity || 999;

                        if (e.target.value === '') return;

                        let value = parseInt(e.target.value);
                        if (value < min) value = min;
                        if (value > max) value = max;

                        e.target.value = value;
                    });

                    quantityInput.addEventListener('paste', (e) => {
                        const pasteData = e.clipboardData.getData('text');
                        if (!/^\d+$/.test(pasteData)) {
                            e.preventDefault();
                        }
                    });
                }
                // Global variables
                var products = [];
                var total = 0;
                var selectedPaymentMode = "Cash";

                // Fetch products from API
                fetch('../assets/pos-api.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Check if API returned an error
                        if (data.error) {
                            console.error('API Error:', data.error);
                            alert('Error loading products: ' + data.error);
                            return;
                        }

                        console.log('Products loaded:', data);
                        products = data;
                        loadCategories();
                        loadCartFromSession();
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                        alert('Failed to load products. Please check console for details.');
                    });

                function loadCategories() {
                    var categoriesContainer = document.getElementById("categories");

                    if (products.length === 0) {
                        categoriesContainer.innerHTML = '<div class="text-center text-muted">No categories available</div>';
                        return;
                    }

                    categoriesContainer.innerHTML = ''; // Clear previous content

                    products.forEach((product, index) => {
                        categoriesContainer.innerHTML += `
            <div onclick="selectCategory(this, ${index})" class="category-pill text-center">
                ${product.category}
            </div>`;
                    });

                    // Check if there's a saved category selection
                    const savedCategory = sessionStorage.getItem('selectedCategory');

                    if (savedCategory !== null) {
                        const categoryIndex = parseInt(savedCategory);
                        const categoryPills = document.querySelectorAll('.category-pill');
                        if (categoryPills[categoryIndex]) {
                            categoryPills[categoryIndex].classList.add('active');
                            loadProducts(categoryIndex);
                        }
                        sessionStorage.removeItem('selectedCategory'); // Clear after use
                    } else if (products.length > 0) {
                        // Select first category by default if no saved selection
                        const firstCategory = document.querySelector('.category-pill');
                        if (firstCategory) {
                            firstCategory.classList.add('active');
                            loadProducts(0);
                        }
                    }
                }

                function selectCategory(element, index) {
                    sessionStorage.setItem('selectedCategory', index);
                    location.reload();
                }

                function loadProducts(categoryIndex) {
                    var maincontainer = document.getElementById("maincontainer");
                    maincontainer.innerHTML = "";

                    // Check if category has products
                    if (!products[categoryIndex] || !products[categoryIndex].contents || products[categoryIndex].contents.length === 0) {
                        maincontainer.innerHTML = '<div class="col-12 text-center text-muted">No products available in this category</div>';
                        return;
                    }

                    products[categoryIndex].contents.forEach((content, contentIndex) => {
                        // Check if product has sizes
                        if (!content.sizes || content.sizes.length === 0) {
                            console.warn('Product has no sizes:', content);
                            return;
                        }

                        content.sizes.forEach((size, sizeIndex) => {
                            const uniqueId = `${categoryIndex}-${contentIndex}-${sizeIndex}`;
                            const sugarSelectId = `sugar-${uniqueId}`;
                            const iceSelectId = `ice-${uniqueId}`;

                            const hasSugarIce = products[categoryIndex].hasSugarIce;

                            let sugarIceDropdowns = '';
                            if (hasSugarIce) {
                                const sugarOptions = content.sugarLevels.map(level =>
                                    `<li><a class="dropdown-item" data-value="${level}">${level}% Sugar Level</a></li>`
                                ).join('');

                                // Ice options that map to database enum values
                                const iceOptions = [
                                    { display: "Less Ice", value: "Less Ice" },
                                    { display: "Default Ice", value: "Default Ice" },
                                    { display: "Extra Ice", value: "Extra Ice" }
                                ].map(ice =>
                                    `<li><a class="dropdown-item" data-value="${ice.value}">${ice.display}</a></li>`
                                ).join('');

                                sugarIceDropdowns = `
                    <div class="dropdown mb-2" onclick="event.stopPropagation();">
                        <button class="btn btn-outline-dark dropdown-toggle w-100" type="button" 
                                id="${sugarSelectId}" data-bs-toggle="dropdown" aria-expanded="false">
                            Sugar Level
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="${sugarSelectId}">
                            ${sugarOptions}
                        </ul>
                    </div>

                    <div class="dropdown mb-2" onclick="event.stopPropagation();">
                        <button class="btn btn-outline-dark dropdown-toggle w-100" type="button" 
                                id="${iceSelectId}" data-bs-toggle="dropdown" aria-expanded="false">
                            Ice Level
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="${iceSelectId}">
                            ${iceOptions}
                        </ul>
                    </div>`;
                            }

                            maincontainer.innerHTML += `
    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
        <div class="menu-item border p-3 rounded shadow text-center width-auto card-hover" 
             style="cursor: pointer; display: flex; flex-direction: column; height: 100%;"
             onclick="showQuantityModal('${content.productID}', '${content.name} ${size.name}', '${size.price}', '${size.name}', '${sugarSelectId}', '${iceSelectId}', ${content.quantity})">
            <img src="../assets/img/img-menu/${content.img}" alt="${content.name}" 
                 class="img-fluid mb-2" style="max-height: 170px; min-height: 120px; pointer-events: none;">
            <div class="lead menu-name fw-bold">${content.name}</div>
            <div class="d-flex justify-content-center align-items-center gap-2 my-2">
                <span class="lead fw-bold menu-price">₱${size.price}</span>
                <span class="lead menu-size">${size.name}</span>
            </div>
           
            <div style="margin-top: auto;">
                ${sugarIceDropdowns}
            </div>
        </div>
    </div>`;
                        });
                    });


                    setTimeout(() => {
                        document.querySelectorAll(".dropdown-menu .dropdown-item").forEach(item => {
                            item.addEventListener("click", function (e) {
                                e.preventDefault();
                                e.stopPropagation(); // Prevent card click when selecting dropdown

                                const btn = this.closest(".dropdown").querySelector("button");
                                btn.textContent = this.textContent;
                                btn.setAttribute("data-value", this.getAttribute("data-value"));

                                // Get the Bootstrap dropdown instance and hide it
                                const dropdown = bootstrap.Dropdown.getInstance(btn);
                                if (dropdown) {
                                    dropdown.hide();
                                }
                            });
                        });

                        // Initialize dropdowns and handle opening/closing
                        document.querySelectorAll(".dropdown button[data-bs-toggle='dropdown']").forEach(button => {
                            // Initialize dropdown if not already initialized
                            if (!bootstrap.Dropdown.getInstance(button)) {
                                new bootstrap.Dropdown(button);
                            }

                            button.addEventListener("show.bs.dropdown", function (event) {
                                const currentDropdown = event.target.nextElementSibling;

                                // Find all other open dropdowns and close them
                                document.querySelectorAll(".dropdown .show").forEach(openDropdown => {
                                    if (openDropdown !== currentDropdown) {
                                        const parentDropdown = openDropdown.closest(".dropdown");
                                        const dropdownButton = parentDropdown?.querySelector("button[data-bs-toggle='dropdown']");
                                        if (dropdownButton) {
                                            const bsDropdown = bootstrap.Dropdown.getInstance(dropdownButton);
                                            if (bsDropdown) {
                                                bsDropdown.hide();
                                            }
                                        }
                                    }
                                });
                            });
                        });
                    }, 100);
                }

                // Fixed function to work with your modal structure
                // Updated showQuantityModal function in point-of-sales.php
                function showQuantityModal(productID, name, price, size, sugarSelectId = null, iceSelectId = null, availableQuantity = 0) {
                    console.log('=== showQuantityModal called ===');
                    console.log('Available Quantity passed:', availableQuantity);
                    console.log('Type:', typeof availableQuantity);

                    // Check if modal exists, if not wait for it to load
                    const quantityModal = document.getElementById('quantityModal');
                    if (!quantityModal) {
                        console.log('Modal not ready, waiting...');
                        setTimeout(() => {
                            showQuantityModal(productID, name, price, size, sugarSelectId, iceSelectId, availableQuantity);
                        }, 100);
                        return;
                    }

                    // Set modal data using your modal's element IDs
                    const modalProductId = document.getElementById('modal-product-id');
                    const modalProductName = document.getElementById('modal-product-name');
                    const modalProductPrice = document.getElementById('modal-product-price');
                    const modalQuantityInput = document.getElementById('modal-quantity-input');
                    const quantityInput = document.getElementById('quantity');
                    const availableStockSpan = document.getElementById('availableStock');
                    const modalAvailableQuantity = document.getElementById('modal-available-quantity');

                    if (modalProductId) modalProductId.value = productID;
                    if (modalProductName) modalProductName.value = name;
                    if (modalProductPrice) modalProductPrice.value = price;
                    if (modalQuantityInput) modalQuantityInput.value = 1;
                    if (quantityInput) quantityInput.value = 1;
                    if (availableStockSpan) availableStockSpan.textContent = availableQuantity;
                    if (modalAvailableQuantity) modalAvailableQuantity.value = availableQuantity;

                    // **CRITICAL: Initialize the quantity validation system**
                    console.log('Calling initializeQuantityModal with:', availableQuantity);
                    if (typeof initializeQuantityModal === 'function') {
                        initializeQuantityModal(availableQuantity);
                    } else {
                        console.error('initializeQuantityModal function not found!');
                        // Fallback
                        currentAvailableQuantity = parseInt(availableQuantity) || 0;
                        console.log('Fallback - set currentAvailableQuantity to:', currentAvailableQuantity);
                    }

                    // Get sugar and ice levels if applicable
                    let sugarLevel = '';
                    if (sugarSelectId) {
                        const sugarDropdown = document.getElementById(sugarSelectId);
                        if (sugarDropdown) {
                            sugarLevel = sugarDropdown.getAttribute('data-value') || '';
                        }
                    }

                    let iceLevel = '';
                    if (iceSelectId) {
                        const iceDropdown = document.getElementById(iceSelectId);
                        if (iceDropdown) {
                            iceLevel = iceDropdown.getAttribute('data-value') || 'Default Ice';
                        }
                    }

                    const modalSugarInput = document.getElementById('modal-sugar-input');
                    const modalIceInput = document.getElementById('modal-ice-input');

                    if (modalSugarInput) modalSugarInput.value = sugarLevel;
                    if (modalIceInput) modalIceInput.value = iceLevel;

                    // Show modal
                    console.log('Opening modal...');
                    const modal = new bootstrap.Modal(quantityModal);
                    modal.show();
                }

                // Add these quantity control functions for your modal
                function increaseQuantity() {
                    const quantityInput = document.getElementById('quantity');
                    const modalQuantityInput = document.getElementById('modal-quantity-input');

                    if (quantityInput && modalQuantityInput) {
                        let currentValue = parseInt(quantityInput.value);
                        if (isNaN(currentValue)) currentValue = 1;

                        // Do not exceed max available quantity
                        if (currentValue < window.currentAvailableQuantity) {
                            const newValue = currentValue + 1;
                            quantityInput.value = newValue;
                            modalQuantityInput.value = newValue;

                            // Update button states
                            window.updateButtonStates(newValue);
                        }
                    }
                }

                function decreaseQuantity() {
                    const quantityInput = document.getElementById('quantity');
                    const modalQuantityInput = document.getElementById('modal-quantity-input');

                    if (quantityInput && modalQuantityInput) {
                        let currentValue = parseInt(quantityInput.value);
                        if (isNaN(currentValue)) currentValue = 1;

                        if (currentValue > 1) {
                            const newValue = currentValue - 1;
                            quantityInput.value = newValue;
                            modalQuantityInput.value = newValue;

                            // Update button states
                            window.updateButtonStates(newValue);
                        }
                    }
                }

                function loadCartFromSession() {
                    fetch('../assets/pos-order-handler.php', {
                        method: 'POST',
                        body: new URLSearchParams({ action: 'get_cart' })
                    })
                        .then(response => response.json())
                        .then(data => {
                            const receiptContainer = document.getElementById("receipt");
                            receiptContainer.innerHTML = '';

                            total = data.total || 0;
                            document.getElementById("totalValue").innerHTML = total.toFixed(2);

                            // Get the Order Now button
                            const orderButton = document.querySelector('.btn-dark-order');

                            if (data.cart && data.cart.length > 0) {
                                // Enable button when cart has items
                                if (orderButton) {
                                    orderButton.disabled = false;
                                    orderButton.style.opacity = '1';
                                    orderButton.style.cursor = 'pointer';
                                }

                                data.cart.forEach(item => {
                                    const sugarText = item.sugarLevel ? ` | ${item.sugarLevel} Sugar` : '';
                                    const iceText = item.iceLevel ? ` | ${item.iceLevel}` : '';

                                    receiptContainer.innerHTML += `
                    <div class="d-flex flex-row justify-content-between align-items-center mb-1 receipt-item">
                        <div class="flex-grow-1 item-name">
                            <small><span style="font-weight: bold;">${item.productName} | ${item.quantity}x</span>${sugarText}${iceText}</small>
                        </div>
                        <div class="item-price">
                            <small>₱ ${item.totalPrice.toFixed(2)}</small>
                        </div>
                    </div>`;
                                });
                            } else {
                                // Disable button when cart is empty
                                if (orderButton) {
                                    orderButton.disabled = true;
                                    orderButton.style.opacity = '0.5';
                                    orderButton.style.cursor = 'not-allowed';
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error loading cart:', error);
                        });
                }

                function cancelOrder() {
                    fetch('../assets/pos-order-handler.php', {
                        method: 'POST',
                        body: new URLSearchParams({ action: 'clear_cart' })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                loadCartFromSession();
                                clearCashInput();
                            }
                        })
                        .catch(error => {
                            console.error('Error clearing cart:', error);
                        });
                }

                function openPopup() {
                    // Check if cart has items
                    fetch('../assets/pos-order-handler.php', {
                        method: 'POST',
                        body: new URLSearchParams({ action: 'get_cart' })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.cart || data.cart.length === 0) {
                                alert("Please add items to your order first.");
                                return;
                            }

                            const summaryList = document.getElementById('orderSummaryList');
                            if (!summaryList) {
                                console.error('Order summary list not found');
                                return;
                            }

                            summaryList.innerHTML = '';

                            data.cart.forEach(item => {
                                const sugarText = item.sugarLevel ? ` (${item.sugarLevel} Sugar)` : '';
                                const iceText = item.iceLevel ? ` (${item.iceLevel})` : '';
                                summaryList.innerHTML += `<li>${item.productName} (${item.quantity}x)${sugarText}${iceText} - ₱${item.totalPrice.toFixed(2)}</li>`;
                            });

                            const paymentModeElement = document.getElementById('paymentModeInput');
                            selectedPaymentMode = paymentModeElement ? paymentModeElement.value : 'Cash';

                            summaryList.innerHTML += `<li class="fw-bold">Total: ₱${data.total.toFixed(2)}</li>`;
                            summaryList.innerHTML += `<li class="fw-bold">Payment Mode: ${selectedPaymentMode}</li>`;

                            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                            modal.show();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to load cart');
                        });
                }

                function confirmOrder() {
                    // Prevent double submission
                    const confirmBtn = document.querySelector('.btnConfirm');
                    if (confirmBtn.disabled) {
                        return;
                    }

                    // Disable button to prevent double clicks
                    confirmBtn.disabled = true;
                    confirmBtn.textContent = 'Processing...';

                    const paymentMethod = document.getElementById('paymentModeInput')?.value || 'Cash';
                    const orderType = document.getElementById('orderTypeInput')?.value || 'dine-in';

                    const formData = new FormData();
                    formData.append('action', 'checkout');
                    formData.append('paymentMethod', paymentMethod);
                    formData.append('customerName', 'Walk-in Customer');
                    formData.append('orderType', orderType);
                    formData.append('contactNumber', '');

                    fetch('../assets/pos-order-handler.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Re-enable button
                            confirmBtn.disabled = false;
                            confirmBtn.textContent = 'CONFIRM ORDER';

                            if (data.success) {
                                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
                                if (modal) {
                                    modal.hide();
                                }

                                // Update receipt number in toast
                                const receiptSpan = document.getElementById('receiptNumber');
                                if (receiptSpan) {
                                    receiptSpan.textContent = data.orderNumber;
                                }

                                const toastElement = document.getElementById('orderToast');
                                if (toastElement) {
                                    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
                                    toast.show();
                                }

                                // Clear the cart display
                                loadCartFromSession();
                                clearCashInput();
                            } else {
                                // Show error to user
                                alert('Error placing order: ' + (data.error || 'Unknown error'));
                                console.error('Order error:', data);
                            }
                        })
                        .catch(error => {
                            // Re-enable button on error
                            confirmBtn.disabled = false;
                            confirmBtn.textContent = 'CONFIRM ORDER';

                            console.error('Error:', error);
                            alert('Failed to place order. Please try again.');
                        });
                }


                // Function to set up dropdown handlers
                function setupDropdownHandlers() {
                    // Handle dropdown selections for order type and payment method
                    document.querySelectorAll('#confirmModal .dropdown-item').forEach(item => {
                        item.addEventListener('click', function (e) {
                            e.preventDefault();
                            const dropdown = this.closest('.dropdown');
                            const button = dropdown.querySelector('button');
                            const hiddenInput = dropdown.querySelector('input[type="hidden"]');

                            button.textContent = this.textContent;
                            if (hiddenInput) {
                                hiddenInput.value = this.getAttribute('data-value');
                            }
                        });
                    });
                }

                // Updated DOMContentLoaded section
                document.addEventListener("DOMContentLoaded", function () {
                    // Initialize WOW.js
                    if (typeof WOW !== 'undefined') {
                        new WOW().init();
                    }

                    // Load modal from PHP file
                    fetch("../modal/pos-modal.php")
                        .then(res => {
                            if (!res.ok) {
                                throw new Error('Failed to load modal');
                            }
                            return res.text();
                        })
                        .then(data => {
                            document.getElementById("modal-placeholder").innerHTML = data;
                            console.log('Modal loaded successfully');

                            // Set up form submission handler for your modal
                            const addToOrderForm = document.getElementById('addToOrderForm');
                            if (addToOrderForm) {
                                addToOrderForm.addEventListener('submit', function (e) {
                                    e.preventDefault(); // Prevent default form submission

                                    // Get form data
                                    const productID = document.getElementById('modal-product-id').value;
                                    const productName = document.getElementById('modal-product-name').value;
                                    const price = parseFloat(document.getElementById('modal-product-price').value);
                                    const quantity = parseInt(document.getElementById('modal-quantity-input').value);
                                    const sugarLevel = document.getElementById('modal-sugar-input').value;
                                    const iceLevel = document.getElementById('modal-ice-input').value || '';

                                    // Get the modal instance before hiding it
                                    const quantityModal = document.getElementById('quantityModal');
                                    const modalInstance = bootstrap.Modal.getInstance(quantityModal);

                                    // Hide modal first to avoid focus issues
                                    if (modalInstance) {
                                        modalInstance.hide();
                                    }

                                    // Add to cart via AJAX
                                    const formData = new FormData();
                                    formData.append('action', 'add_to_cart');
                                    formData.append('productID', productID);
                                    formData.append('productName', productName);
                                    formData.append('price', price);
                                    formData.append('quantity', quantity);
                                    formData.append('sugarLevel', sugarLevel);
                                    formData.append('iceLevel', iceLevel);
                                    formData.append('size', 'Regular');

                                    fetch('../assets/pos-order-handler.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                loadCartFromSession();
                                                console.log('Item added to cart successfully');
                                            } else {
                                                alert('Error adding item to cart: ' + (data.error || 'Unknown error'));
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('Failed to add item to cart');
                                        });
                                });
                            }

                            
                            const confirmBtn = document.querySelector('.btnConfirm');
                            if (confirmBtn) {
                                confirmBtn.addEventListener('click', confirmOrder);
                            }

                            
                            setupDropdownHandlers();
                        })
                        .catch(error => {
                            console.error('Error loading modal:', error);
                        });

                    // Load cart on page load
                    setTimeout(loadCartFromSession, 1000);
                });

                // Update payment mode when changed
                document.addEventListener('change', function (e) {
                    if (e.target.id === 'paymentModeInput') {
                        selectedPaymentMode = e.target.value;
                    }
                });
            </script>

            <!-- <script>
                document.addEventListener("DOMContentLoaded", function () {
                    // Reload Page every 30 seconds to fetch new orders
                    setInterval(function () {
                        location.reload();
                    }, 30000);
                });
            </script> -->

            <script src="../assets/js/admin_sidebar.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
                crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
        </div>
    </div>
</body>

</html>
