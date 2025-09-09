

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
                        <a href="notification.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                            data-wow-delay="0.15s">
                            <i class="bi bi-bell"></i>
                            <span>Notifications</span>
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
                                    <a href="notification.php" class="btn btn-light position-relative">
                                        <i class="bi bi-bell-fill"></i>
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            <!-- 3 -->
                                            <span class="visually-hidden">unread messages</span>
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
                                <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mt-4">
                                    <button class="btn btn-dark-order w-100 w-md-auto py-2 px-3"
                                        onclick="openPopup()">Order
                                        Now</button>
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

                    // Select first category by default
                    if (products.length > 0) {
                        const firstCategory = document.querySelector('.category-pill');
                        if (firstCategory) {
                            firstCategory.classList.add('active');
                            loadProducts(0);
                        }
                    }
                }

                function selectCategory(element, index) {
                    document.querySelectorAll('.category-pill').forEach(pill => pill.classList.remove('active'));
                    element.classList.add('active');
                    loadProducts(index);
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

                            // Determine if product should have sugar/ice options (beverages)
                            const categoryName = products[categoryIndex].category.toLowerCase();
                            const hasSugarIce = categoryName.includes('coffee') || categoryName.includes('tea') ||
                                categoryName.includes('frappe') || categoryName.includes('milktea') ||
                                categoryName.includes('soda');

                            let sugarIceDropdowns = '';
                            if (hasSugarIce) {
                                const sugarOptions = content.sugarLevels.map(level =>
                                    `<li><a class="dropdown-item" data-value="${level}">${level}% Sugar Level</a></li>`
                                ).join('');

                                const iceOptions = ["No Ice", "Less Ice", "Regular Ice"].map(level =>
                                    `<li><a class="dropdown-item" data-value="${level}">${level}</a></li>`
                                ).join('');

                                sugarIceDropdowns = `
                    <div class="dropdown mb-2">
                        <button class="btn btn-outline-dark dropdown-toggle w-100" type="button" 
                                id="${sugarSelectId}" data-bs-toggle="dropdown" aria-expanded="false">
                            Sugar Level
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="${sugarSelectId}">
                            ${sugarOptions}
                        </ul>
                    </div>

                    <div class="dropdown mb-2">
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
                    <div class="menu-item border p-3 rounded shadow text-center width-auto card-hover" style="cursor: pointer;">
                        <img src="../assets/img/${content.img}" alt="${content.name}" 
                             class="img-fluid mb-2" style="max-height: 170px; min-height: 120px"
                             onerror="this.src='../assets/img/default-product.jpg'">
                        <div class="lead menu-name fw-bold">${content.name}</div>
                        <div class="d-flex justify-content-center align-items-center gap-2 my-2">
                            <span class="lead fw-bold menu-price">₱${size.price}</span>
                            <span class="lead menu-size">${size.name}</span>
                        </div>
                       
                        ${sugarIceDropdowns}

                        <button class="btn btn-dark btn-sm mt-1"
                            onclick="showQuantityModal('${content.productID}', '${content.name} ${size.name}', '${size.price}', '${size.name}', '${sugarSelectId}', '${iceSelectId}')">
                            Add to Order
                        </button>
                    </div>
                </div>`;
                        });
                    });

                    // Set up dropdown functionality after a short delay
                    setTimeout(() => {
                        document.querySelectorAll(".dropdown-menu .dropdown-item").forEach(item => {
                            item.addEventListener("click", function (e) {
                                e.preventDefault();
                                const btn = this.closest(".dropdown").querySelector("button");
                                btn.textContent = this.textContent;
                                btn.setAttribute("data-value", this.getAttribute("data-value"));
                            });
                        });
                    }, 100);
                }

                function showQuantityModal(productID, name, price, size, sugarSelectId = null, iceSelectId = null) {
                    const quantityModal = document.getElementById('quantityModal');
                    if (!quantityModal) {
                        console.error('Quantity modal not found');
                        return;
                    }

                    const addButton = document.getElementById('addToReceiptButton');
                    if (!addButton) {
                        console.error('Add button not found');
                        return;
                    }

                    // Reset quantity input
                    document.getElementById('quantityInput').value = 1;

                    addButton.setAttribute('data-productid', productID);
                    addButton.setAttribute('data-price', price);
                    addButton.setAttribute('data-name', name);
                    addButton.setAttribute('data-size', size);
                    addButton.setAttribute('data-sugarSelectId', sugarSelectId || '');
                    addButton.setAttribute('data-iceSelectId', iceSelectId || '');

                    const modal = new bootstrap.Modal(quantityModal);
                    modal.show();
                }

                function addToReceipt() {
                    const addButton = document.getElementById('addToReceiptButton');
                    if (!addButton) {
                        console.error('Add button not found');
                        return;
                    }

                    const productID = addButton.getAttribute('data-productid');
                    const price = parseFloat(addButton.getAttribute('data-price'));
                    const name = addButton.getAttribute('data-name');
                    const size = addButton.getAttribute('data-size');
                    const sugarSelectId = addButton.getAttribute('data-sugarSelectId');
                    const iceSelectId = addButton.getAttribute('data-iceSelectId');
                    const quantity = parseInt(document.getElementById('quantityInput').value) || 1;

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
                            iceLevel = iceDropdown.getAttribute('data-value') || '';
                        }
                    }

                    // Add to cart via AJAX
                    const formData = new FormData();
                    formData.append('action', 'add_to_cart');
                    formData.append('productID', productID);
                    formData.append('productName', name);
                    formData.append('price', price);
                    formData.append('quantity', quantity);
                    formData.append('sugarLevel', sugarLevel);
                    formData.append('iceLevel', iceLevel);
                    formData.append('size', size);

                    fetch('../assets/pos-order-handler.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                loadCartFromSession();
                                const quantityModal = bootstrap.Modal.getInstance(document.getElementById('quantityModal'));
                                if (quantityModal) {
                                    quantityModal.hide();
                                }
                            } else {
                                alert('Error adding item to cart: ' + (data.error || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to add item to cart');
                        });
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

                            if (data.cart && data.cart.length > 0) {
                                data.cart.forEach(item => {
                                    const sugarText = item.sugarLevel ? ` | ${item.sugarLevel}% Sugar` : '';
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
                                const sugarText = item.sugarLevel ? ` (${item.sugarLevel}% Sugar)` : '';
                                const iceText = item.iceLevel ? ` (${item.iceLevel})` : '';
                                summaryList.innerHTML += `<li>${item.productName} (${item.quantity}x)${sugarText}${iceText} - ₱${item.totalPrice.toFixed(2)}</li>`;
                            });

                            const paymentModeElement = document.getElementById('paymentMode');
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
                    const paymentMethod = document.getElementById('paymentMode')?.value || 'Cash';

                    const formData = new FormData();
                    formData.append('action', 'checkout');
                    formData.append('paymentMethod', paymentMethod);
                    formData.append('customerName', '');
                    formData.append('orderType', 'dine-in');
                    formData.append('contactNumber', '');

                    fetch('../assets/pos-order-handler.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
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
                            } else {
                                alert('Error placing order: ' + (data.error || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to place order');
                        });
                }

                // Quantity modal controls
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

                            // Add event listener to confirm button if it exists
                            const confirmBtn = document.querySelector('.btnConfirm');
                            if (confirmBtn) {
                                confirmBtn.addEventListener('click', confirmOrder);
                            }

                            // Quantity controls
                            const plusBtn = document.getElementById('plusBtn');
                            const minusBtn = document.getElementById('minusBtn');
                            const quantityInput = document.getElementById('quantityInput');

                            if (plusBtn) {
                                plusBtn.addEventListener('click', function () {
                                    quantityInput.value = parseInt(quantityInput.value) + 1;
                                });
                            }

                            if (minusBtn) {
                                minusBtn.addEventListener('click', function () {
                                    const currentValue = parseInt(quantityInput.value);
                                    if (currentValue > 1) {
                                        quantityInput.value = currentValue - 1;
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error loading modal:', error);
                        });

                    // Load cart on page load
                    setTimeout(loadCartFromSession, 1000);
                });

                // Update payment mode when changed
                document.addEventListener('change', function (e) {
                    if (e.target.id === 'paymentMode') {
                        selectedPaymentMode = e.target.value;
                    }
                });
            </script>

            <script src="../assets/js/admin_sidebar.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
                crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
        </div>
    </div>
</body>

</html>