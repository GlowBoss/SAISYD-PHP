<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Management</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/inventory-management.css">
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
    <!-- Desktop Sidebar (visible on md+ screens) -->
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
                <a href="notification.php" class="admin-nav-link">
                    <i class="bi bi-bell"></i>
                    <span>Notifications</span>
                </a>
                <a href="point-of-sales.php" class="admin-nav-link">
                    <i class="bi bi-shop-window"></i>
                    <span>Point of Sales</span>
                </a>
                <a href="inventory-management.php" class="admin-nav-link">
                    <i class="bi bi-boxes"></i>
                    <span>Inventory Management</span>
                </a>
                <a href="menu-management.php" class="admin-nav-link active">
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
                <a href="#" class="admin-nav-link">
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
            <a href="index.php" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.1s">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="notification.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.15s">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
            </a>
            <a href="point-of-sales.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.2s">
                <i class="bi bi-shop-window"></i>
                <span>Point of Sales</span>
            </a>
            <a href="inventory-management.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.25s">
                <i class="bi bi-boxes"></i>
                <span>Inventory Management</span>
            </a>
            <a href="menu-management.php" class="admin-nav-link active wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.3s">
                <i class="bi bi-menu-button-wide"></i>
                <span>Menu Management</span>
            </a>

            <!-- FINANCIAL Section -->
            <div class="section-header">Financial</div>
            <a href="sales-and-report.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.35s">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Sales & Reports</span>
            </a>

            <!-- TOOLS Section -->
            <div class="section-header">Tools</div>
            <a href="#" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.4s">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </a>
            <a href="login.php" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.45s">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content p-3">
        <div class="row">
            <div class="col">
                <div class="card cardContent shadow-sm mt-2">
                    <!-- Mobile Menu Toggle Button  -->
                    <div class="d-md-none mobile-header d-flex align-items-center p-3">
                        <button id="menuToggle" class="mobile-menu-toggle me-3">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h4 class="mobile-header-title">Inventory Management</h4>
                    </div>
                    <!-- Header Row -->
                    <div class="d-none d-md-block align-items-center py-4 px-lg-3 px-2">
                        <h4 class="subheading fw-bold m-1 d-flex align-items-center">
                            <span>Inventory Management</span>
                        </h4>
                    </div>

                    <div class="row g-2 align-items-center mb-3 px-2 px-lg-3">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-6">
                            <h4 class="subheading fw-bold">Current Stock</h4>
                        </div>
                        <!-- search -->
                        <div class="col-12 col-sm my-0 me-1 d-flex justify-content-end">
                            <input type="text" class="form-control search w-100" placeholder="Search" aria-label="search-bar"
                                id="search-inventory">
                        </div>
                        <!-- buttons -->
                        <div class="col-12 col-sm-auto col-md-12 col-lg-auto mt-2 mt-lg-0 d-flex justify-content-end">
                            <button class="btn categorybtn w-100" type="button">
                                Search
                            </button>
                        </div>
                        <div class="col-12 col-sm-auto col-md-12 col-lg-auto mt-2 mt-lg-0 d-flex justify-content-end">
                            <button class="btn categorybtn w-100" type="button" data-bs-toggle="modal"
                                data-bs-target="#addItemModal">
                                Add
                            </button>
                        </div>
                        <div class="col-12 col-sm-auto col-md-12 col-lg-auto mt-2 mt-lg-0 d-flex justify-content-end">
                            <button class="btn btnExport w-100" type="button">
                                Export
                            </button>
                        </div>
                    </div>

                    <!-- TABLE -->
                    <div class="card tableCard mb-3">
                        <table class="table">
                            <thead class="text-center custom-thead">
                                <tr>
                                    <th scope="col">Item Code</th>
                                    <th scope="col">Item Name</th>
                                    <th scope="col">Item Group</th>
                                    <th scope="col">Last Purchase</th>
                                    <th scope="col">On Hand</th>
                                    <th scope="col">Supplier</th>
                                    <th scope="col">Unit Cost</th>
                                    <th scope="col">Total Value</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <th scope="row">INV-001</th>
                                    <td>Orange</td>
                                    <td>Fruits</td>
                                    <td>03 May 2025</td>
                                    <td>100 Kg</td>
                                    <td>Fresh Farms Co.</td>
                                    <td>₱80.00</td>
                                    <td>₱8,000.00</td>
                                    <td>
                                        <button class="btn btn-sm categorybtn"><i
                                                class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-sm btnExport"><i
                                                class="bi bi-three-dots-vertical"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">INV-002</th>
                                    <td>Apple</td>
                                    <td>Fruits</td>
                                    <td>04 May 2025</td>
                                    <td>75 Kg</td>
                                    <td>Orchard Fresh Ltd.</td>
                                    <td>₱95.00</td>
                                    <td>₱7,125.00</td>
                                    <td>
                                        <button class="btn btn-sm categorybtn"><i
                                                class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-sm btnExport"><i
                                                class="bi bi-three-dots-vertical"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">INV-003</th>
                                    <td>Fresh Milk</td>
                                    <td>Dairy</td>
                                    <td>02 May 2025</td>
                                    <td>50 L</td>
                                    <td>Dairy Delight Inc.</td>
                                    <td>₱120.00</td>
                                    <td>₱6,000.00</td>
                                    <td>
                                        <button class="btn btn-sm categorybtn"><i
                                                class="bi bi-pencil-square"></i></button>
                                        <button class="btn btn-sm btnExport"><i
                                                class="bi bi-three-dots-vertical"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Add Inventory Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Item Name</label>
                                    <input type="text" class="form-control" name="item_name"
                                        placeholder="Enter item name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Item Group</label>
                                    <div class="dropdown">
                                        <button class="form-select text-start" type="button" id="itemGroupDropdown"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Select Category
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="itemGroupDropdown">
                                            <li><a class="dropdown-item" href="#" data-value="fruits">Fruits</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="vegetables">Vegetables</a>
                                            </li>
                                            <li><a class="dropdown-item" href="#" data-value="dairy">Dairy</a></li>
                                            <li><a class="dropdown-item" href="#" data-value="beverages">Beverages</a>
                                            </li>
                                        </ul>
                                        <input type="hidden" name="item_group" id="itemGroupInput" value="">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Unit Cost</label>
                                    <input type="text" class="form-control" name="unitCost"
                                        placeholder="Enter unit cost">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">On Hand Quantity</label>
                                    <input type="text" class="form-control" name="quantity"
                                        placeholder="Enter quantity">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Unit Cost</label>
                                    <input type="text" class="form-control" name="unit" placeholder="Kg, pcs, etc.">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Supplier</label>
                                    <input type="text" class="form-control" name="supplier"
                                        placeholder="Enter supplier name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Last Purchase</label>
                                    <input type="text" class="form-control" name="lastPurchase"
                                        placeholder="Enter last purchase date">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btnCancel me-2" data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btnAdd">ADD ITEM</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownToggle = document.getElementById('itemGroupDropdown');
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            const hiddenInput = document.getElementById('itemGroupInput');

            dropdownItems.forEach(item => {
                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    const value = this.getAttribute('data-value');
                    const text = this.textContent;

                    dropdownToggle.textContent = text;
                    hiddenInput.value = value;
                });
            });
        });

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>

</body>

</html>