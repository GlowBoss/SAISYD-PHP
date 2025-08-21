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
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                        aria-label="Close">&times;</button>
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
                                    <button class="btn btn-dark w-100 w-md-auto py-2 px-3" onclick="openPopup()">Order
                                        Now</button>
                                    <button class="btn btn-outline-dark w-100 w-md-auto py-2 px-3"
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
                // Plus / Minus buttons
                document.addEventListener('click', (e) => {
                    if (e.target.closest('#plusBtn')) {
                        const input = document.getElementById('quantityInput');
                        input?.stepUp();
                    }
                    if (e.target.closest('#minusBtn')) {
                        const input = document.getElementById('quantityInput');
                        if (input && +input.value > 1) input.stepDown();
                    }
                });

                // Reset input value when modal closes
                const quantityModalEl = document.getElementById('quantityModal');
                if (quantityModalEl) {
                    quantityModalEl.addEventListener('hidden.bs.modal', (event) => {
                        const input = document.getElementById('quantityInput');
                        if (input) {
                            input.value = 1;
                        }
                    });
                }
            </script>





            <script>
                var products = [];
                var total = 0;
                var orderData = [];
                var selectedPaymentMode = "";

                // Fetch products from JSON file
                fetch('products.json')
                    .then(response => response.json())
                    .then(data => {
                        products = data;
                        loadCategories();
                    });

                function loadCategories() {
                    var categoriesContainer = document.getElementById("categories");
                    products.forEach((product, index) => {
                        categoriesContainer.innerHTML += `
            <div onclick="selectCategory(this, '${index}')" class="category-pill text-center">
                ${product.category}
            </div>`;
                    });
                }

                function selectCategory(element, index) {
                    document.querySelectorAll('.category-pill').forEach(pill => pill.classList.remove('active'));
                    element.classList.add('active');
                    loadProducts(index);
                }

                function loadProducts(categoryIndex) {
                    var maincontainer = document.getElementById("maincontainer");
                    maincontainer.innerHTML = "";

                    if (categoryIndex == 0 || categoryIndex == 1) {
                        products[categoryIndex].contents.forEach((content, contentIndex) => {
                            content.sizes.forEach((size, sizeIndex) => {
                                const sugarSelectId = `sugar-${categoryIndex}-${contentIndex}-${sizeIndex}`;
                                const iceSelectId = `ice-${categoryIndex}-${contentIndex}-${sizeIndex}`;

                                const sugarLevels = (content.sugarLevels && content.sugarLevels.length > 0) ? content.sugarLevels : [0, 25, 50, 75, 100];
                                const sugarOptions = sugarLevels.map(level =>

                                    `<li><a class="dropdown-item" value="${level}">${level}% Sugar Level</a></li>`
                                ).join('');

                                const iceOptions = ["No Ice", "Less Ice", "Regular Ice"].map(level => `<li><a class="dropdown-item" value="${level}">${level}</a></li>`).join('');

                                maincontainer.innerHTML += `
                    <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                        <div class="menu-item border p-3 rounded shadow text-center width-auto card-hover" style="cursor: pointer;">
                            <img src="../assets/img/${content.img}" alt="${content.name}" class="img-fluid mb-2" style="max-height: 170px; min-height: 120px">
                            <div class="lead menu-name fw-bold">${content.name}</div>
                            <div class="d-flex justify-content-center align-items-center gap-2 my-2">
                                <span class="lead fw-bold menu-price">₱${size.price}</span>
                                <span class="lead menu-size">${size.name}</span>
                            </div>
                           
                            <div class="dropdown mb-2">
                                <button class="btn btn-outline-dark dropdown-toggle w-100" type="button" id="${sugarSelectId}" data-bs-toggle="dropdown" aria-expanded="false">Sugar Level</button>
                                <ul class="dropdown-menu" aria-labelledby="${sugarSelectId}">
                                ${sugarOptions} </ul>
                             </div>

                             <div class="dropdown mb-2">
                            <button class="btn btn-outline-dark dropdown-toggle w-100" type="button" id="${iceSelectId}" data-bs-toggle="dropdown" aria-expanded="false">Ice Level</button>
                            <ul class="dropdown-menu" aria-labelledby="${iceSelectId}">${iceOptions}</ul>
                            </div>

                            <button class="btn btn-dark btn-sm mt-1"
                                onclick="showQuantityModal('${size.price}','${content.code + size.code}','${content.name} ${size.name}', '${sugarSelectId}', '${iceSelectId}')">
                                Add to Order
                            </button>
                        </div>
                    </div>`;
                            });
                        });
                        document.querySelectorAll(".dropdown-menu .dropdown-item").forEach(item => {
                            item.addEventListener("click", function () {
                                const btn = this.closest(".dropdown").querySelector("button");
                                btn.textContent = this.textContent;
                                btn.setAttribute("data-value", this.getAttribute("value"));

                            });
                        });

                    } else {
                        products[categoryIndex].contents.forEach(content => {
                            maincontainer.innerHTML += `
                <div class="col">
                    <div onclick="showQuantityModal('${content.price}','${content.code}','${content.name}')" 
                         class="menu-item border p-3 rounded shadow text-center width-auto card-hover" 
                         style="cursor: pointer; height: 230px;">
                        <img src="../assets/img/${content.img}" alt="${content.name}" class="img-fluid mb-2" style="max-height: 150px;">
                        <div class="lead menu-name fw-bold">${content.name}</div>
                        <div class="d-flex justify-content-center align-items-center gap-2 my-2">
                            <span class="lead fw-bold menu-price">₱${content.price}</span>
                        </div>
                    </div>
                </div>`;
                        });
                    }
                }

                function showQuantityModal(price, code, name, sugarSelectId = null, iceSelectId = null) {
                    const quantityModal = document.getElementById('quantityModal');
                    const addButton = document.getElementById('addToReceiptButton');

                    addButton.setAttribute('data-price', price);
                    addButton.setAttribute('data-code', code);
                    addButton.setAttribute('data-name', name);
                    addButton.setAttribute('data-sugarSelectId', sugarSelectId);
                    addButton.setAttribute('data-iceSelectId', iceSelectId);

                    const modal = new bootstrap.Modal(quantityModal);
                    modal.show();
                }

                function cancelOrder() {
                    document.getElementById("receipt").innerHTML = "";
                    total = 0;
                    document.getElementById("totalValue").innerHTML = "0.00";
                    orderData = [];
                }

                function addToReceipt() {
                    const addButton = document.getElementById('addToReceiptButton');
                    const price = addButton.getAttribute('data-price');
                    const code = addButton.getAttribute('data-code');
                    const name = addButton.getAttribute('data-name');
                    const sugarSelectId = addButton.getAttribute('data-sugarSelectId');
                    const iceSelectId = addButton.getAttribute('data-iceSelectId');
                    const quantity = parseInt(document.getElementById('quantityInput').value) || 1;

                    total += parseFloat(price) * quantity;
                    document.getElementById("totalValue").innerHTML = total.toFixed(2);
                    localStorage.setItem('orderTotal', total.toFixed(2));

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

                    const receiptContainer = document.getElementById("receipt");
                    receiptContainer.innerHTML += `
    <div class="d-flex flex-row justify-content-between align-items-center mb-1 receipt-item">
        <div class="flex-grow-1 item-name">
            <small><span style="font-weight: bold;">${name} | ${quantity}x</span> ${code} ${sugarLevel ? '| ' + sugarLevel + '% Sugar' : ''} ${iceLevel ? '| ' + iceLevel : ''}</small>
        </div>
        <div class="item-price">
            <small>₱ ${(parseFloat(price) * quantity).toFixed(2)}</small>
        </div>
    </div>`;

                    let category = '';
                    for (const product of products) {
                        const foundContent = product.contents.find(content => content.code === code);
                        if (foundContent) {
                            category = product.category;
                            break;
                        }
                    }

                    orderData.push({
                        name: name,
                        price: parseFloat(price),
                        category: category,
                        quantity: quantity,
                        sugarLevel: sugarLevel,
                        iceLevel: iceLevel,
                        totalPrice: (parseFloat(price) * quantity).toFixed(2)
                    });

                    const quantityModal = bootstrap.Modal.getInstance(document.getElementById('quantityModal'));
                    quantityModal.hide();

                }

                function openPopup() {
                    const receiptItems = document.querySelectorAll('#receipt .receipt-item');
                    const summaryList = document.getElementById('orderSummaryList');
                    summaryList.innerHTML = '';

                    const currentOrder = [];
                    let totalPrice = 0;

                    receiptItems.forEach(item => {
                        const name = item.querySelector('.item-name')?.textContent || '';
                        const price = parseFloat(item.querySelector('.item-price')?.textContent.replace('₱ ', '')) || 0;
                        if (name) {
                            summaryList.innerHTML += `<li>${name}</li>`;
                            currentOrder.push({ name, price });
                            totalPrice += price;
                        }
                    });

                    const paymentModeElement = document.getElementById('paymentMode');
                    selectedPaymentMode = paymentModeElement ? paymentModeElement.value : 'N/A';

                    summaryList.innerHTML += `<li class="fw-bold">Total: ₱${totalPrice.toFixed(2)}</li>`;
                    summaryList.innerHTML += `<li class="fw-bold">Payment Mode: ${selectedPaymentMode}</li>`;

                    document.getElementById('confirmModal').dataset.currentOrder = JSON.stringify(currentOrder);
                    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                    modal.show();
                }

                function confirmOrder() {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
                    modal.hide();

                    const toastElement = document.getElementById('orderToast');
                    const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
                    toast.show();

                    let existingOrders = JSON.parse(localStorage.getItem('orderData')) || [];
                    existingOrders.push({
                        items: [...orderData],
                        total: total.toFixed(2),
                        paymentMode: selectedPaymentMode,
                        timestamp: new Date().toISOString()
                    });
                    localStorage.setItem('orderData', JSON.stringify(existingOrders));

                    orderData = [];
                    document.getElementById("receipt").innerHTML = "";
                    total = 0;
                    document.getElementById("totalValue").innerHTML = "0.00";
                }

                document.addEventListener("DOMContentLoaded", function () {
                    // Initialize WOW.js
                    new WOW().init();

                    // Load modal from PHP file
                    fetch("../modal/pos-modal.php")
                        .then(res => res.text())
                        .then(data => {
                            document.getElementById("modal-placeholder").innerHTML = data;
                            document.querySelector('.btnConfirm').addEventListener('click', confirmOrder);
                        });
                });

            </script>

            <script src="../assets/js/admin_sidebar.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
                crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        </div>
    </div>
</body>

</html>