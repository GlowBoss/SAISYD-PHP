<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/notification.css">
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
    <!-- Mobile Menu Toggle Button  -->
    <div class="d-md-none mobile-header d-flex align-items-center p-3">
        <button id="menuToggle" class="mobile-menu-toggle me-3">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="mobile-header-title">Notifications</h4>
    </div>

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
                <a href="notification.php" class="admin-nav-link active">
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
                <a href="menu-management.php" class="admin-nav-link">
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
            <a href="notification.php" class="admin-nav-link active wow animate__animated animate__fadeInLeft"
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
            <a href="menu-management.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
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
            <a href="settings.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.4s">
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
    <div class="main-content">
        <div class="container-fluid">
            <div class="cardMain shadow-lg">

                <!-- Header Row  -->
                <div class="d-none d-md-block align-items-center py-4 px-lg-3 px-2">
                    <div class="subheading fw-bold m-1 d-flex align-items-center">
                        <span style="color: var(--text-color-dark);">Notifications</span>
                    </div>
                </div>

                <!-- Category Buttons -->
                <div class="m-3">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <button class="categorybtn-active categorybtn btn btn-outline rounded-pill px-3"
                            data-category="all">All Orders</button>
                        <button class="categorybtn btn btn-outline rounded-pill px-3"
                            data-category="standby">Standby</button>
                        <button class="categorybtn btn btn-outline rounded-pill px-3"
                            data-category="preparing">Preparing</button>
                        <button class="categorybtn btn btn-outline rounded-pill px-3"
                            data-category="complete">Completed</button>
                    </div>
                </div>


                <div class="d-flex flex-column gap-3 ms-3 me-3 mb-3" id="orderCardsContainer">

                    <!-- Card for Standby-->
                    <div class="card orderCard shadow-sm" data-category="standby">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="text-start mt-1">
                                    <h6 class="fw-semibold mb-2">Order #001</h6>
                                    <!-- Product and Quantity -->
                                    <p class="card-text small mb-0 ms-2 mt-1 d-flex flex-wrap">
                                        <span class="fw-semibold me-2">1x</span>
                                        Americanno
                                        <span class="text-muted ms-2">16oz (0% sugar)</span>
                                        <span class="fw-semibold ms-2">₱120</span>
                                    </p>

                                    <p class="card-text small mb-0 ms-2 mt-1 d-flex flex-wrap">
                                        <span class="fw-semibold me-2">1x</span>
                                        Capuccino
                                        <span class="text-muted ms-2">16oz (0% sugar)</span>
                                        <span class="fw-semibold ms-2">₱120</span>
                                    </p>
                                </div>

                                <!-- Selector -->
                                <div class="text-end dropdown">
                                    <button class="btn btn-dropdown dropdown-toggle badge" type="button"
                                        id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Standby
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item" href="#">Standby</a></li>
                                        <li><a class="dropdown-item" href="#">Preparing</a></li>
                                        <li><a class="dropdown-item" href="#">Completed</a></li>
                                    </ul>
                                    <p class="card-text small mt-2">3:30 am</p>
                                </div>

                            </div>
                            <hr>
                            <p class="fw-semibold text-end ms-2 ">Total: ₱240 </p>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <div class="d-flex gap-2">
                                    <button class="btn action-buttons btnAccept px-3 py-1 py-sm-2" onclick="">
                                        Accept
                                    </button>
                                    <button class="btn btnDecline fw-semibold px-3 py-1 py-sm-2" onclick="">
                                        Cancel
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card orderCard shadow-sm" data-category="ready">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="text-start mt-1">
                                    <h6 class="fw-semibold mb-2">Order #001</h6>
                                    <!-- Product and Quantity -->
                                    <p class="card-text small mb-0 ms-2 mt-1 d-flex flex-wrap">
                                        <span class="fw-semibold me-2">1x</span>
                                        <span class="flex-grow-1">American Latte</span>
                                        <span class="text-muted ms-2">16oz (0% sugar)</span>
                                        <span class="fw-semibold ms-2">₱100</span>
                                    </p>

                                    <p class="card-text small mb-0 ms-2 mt-1 d-flex flex-wrap">
                                        <span class="fw-semibold me-2">1x</span>
                                        <span class="flex-grow-1">Mocha</span>
                                        <span class="text-muted ms-2">16oz (0% sugar)</span>
                                        <span class="fw-semibold ms-2">₱90</span>
                                    </p>

                                </div>

                                <!-- Selector -->
                                <div class="text-end dropdown">
                                    <button class="btn btn-dropdown dropdown-toggle badge" type="button"
                                        id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ready
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item" href="#">Standby</a></li>
                                        <li><a class="dropdown-item" href="#">Ready</a></li>
                                        <li><a class="dropdown-item" href="#">Completed</a></li>
                                    </ul>
                                    <p class="card-text small mt-1">3:30 am</p>
                                </div>

                            </div>
                            <hr>
                            <p class="fw-semibold text-end ms-2 ">Total: ₱90 </p>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <div class="d-flex gap-2">
                                    <button class="btn action-buttons btnAccept px-3 py-1 py-sm-2" onclick="">
                                        Complete
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>


                    <!-- Card for Completed -->

                    <div class="card orderCard shadow-sm" data-category="ready">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="text-start mt-1">
                                    <h6 class="fw-semibold mb-2">Order #001</h6>
                                    <!-- Product and Quantity -->
                                    <p class="card-text small mb-0 ms-2 mt-1 d-flex flex-wrap">
                                        <span class="fw-semibold me-2">1x</span>
                                        Fries
                                        <span class="fw-semibold ms-2">₱100</span>
                                    </p>

                                    <p class="card-text small mb-0 ms-2 mt-1 d-flex flex-wrap">
                                        <span class="fw-semibold me-2">1x</span>
                                        Earl Grey Tea
                                        <span class="text-muted ms-2">16oz (0% sugar)</span>
                                        <span class="fw-semibold ms-2">₱120</span>
                                    </p>

                                </div>

                                <!-- Selector -->
                                <div class="text-end dropdown">
                                    <button class="btn btn-dropdown dropdown-toggle badge" type="button"
                                        id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        Completed
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                        <li><a class="dropdown-item" href="#">Standby</a></li>
                                        <li><a class="dropdown-item" href="#">Ready</a></li>
                                        <li><a class="dropdown-item" href="#">Completed</a></li>
                                    </ul>
                                    <p class="card-text small mt-1">3:30 am</p>
                                </div>

                            </div>
                            <hr>
                            <p class="fw-semibold text-end ms-2 ">Total: ₱220 </p>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <div class="d-flex gap-2">
                                    <button class="btn action-buttons btnAccept px-3 py-1 py-sm-2" onclick="">
                                        Clear
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/notif.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>
</body>

</html>