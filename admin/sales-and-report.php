<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales and Report</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/sales-and-report.css">
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
        <h4 class="mobile-header-title">Sales and Report</h4>
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
                <a href="menu-management.php" class="admin-nav-link">
                    <i class="bi bi-menu-button-wide"></i>
                    <span>Menu Management</span>
                </a>
            </div>

            <!-- FINANCIAL Section -->
            <div class="section-header">Financial</div>
            <div class="mb-3">
                <a href="sales-and-report.php" class="admin-nav-link active">
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
            <a href="notification.php" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.15s">
                <i class="bi bi-bell"></i>
                <span>Notifications</span>
            </a>
            <a href="point-of-sales.php" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.2s">
                <i class="bi bi-shop-window"></i>
                <span>Point of Sales</span>
            </a>
            <a href="inventory-management.php" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.25s">
                <i class="bi bi-boxes"></i>
                <span>Inventory Management</span>
            </a>
            <a href="menu-management.php" class="admin-nav-link wow animate__animated animate__fadeInLeft" data-wow-delay="0.3s">
                <i class="bi bi-menu-button-wide"></i>
                <span>Menu Management</span>
            </a>

            <!-- FINANCIAL Section -->
            <div class="section-header">Financial</div>
            <a href="sales-and-report.php" class="admin-nav-link active wow animate__animated animate__fadeInLeft" data-wow-delay="0.35s">
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
    <div class="main-content">
        <div class="container-fluid">
            <div class="card rounded-4">
                <!-- Header Row  -->
                <div class="d-none d-md-block align-items-center py-4 px-lg-3 px-2">
                    <h4 class="subheading fw-bold m-1 d-flex align-items-center">
                        <span>Sales and Report</span>
                    </h4>
                </div>

                <div id="modal-placeholder"></div>
                
                <div class="container-fluid">
                    <div class="row align-items-start">
                        <div class="col-12 col-lg-4">
                            <div id="smallCardCarousel" class="carousel slide" data-bs-ride="false">
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#smallCardCarousel"
                                        data-bs-slide-to="0" class="active"></button>
                                    <button type="button" data-bs-target="#smallCardCarousel"
                                        data-bs-slide-to="1"></button>
                                </div>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <div class="d-flex flex-wrap justify-content-center my-3">
                                            <div class="card cardSmall m-2 fw-bolder p-3"
                                                style="background-color:#C4A277; color:aliceblue">
                                                <div
                                                    class="d-flex justify-content-center align-items-center">
                                                    <span class=>Total Sales</span>
                                                </div>
                                                <div class="sales-amount mt-3"
                                                    style="font-size: var(--lead);">₱10,000
                                                    <span class="sales-subtext">this month</span>
                                                </div>
                                            </div>
                                            <div class="card cardSmall m-2 fw-bolder p-3">
                                                <div
                                                    class="d-flex justify-content-center align-items-center">
                                                    <span>Total Order</span>
                                                </div>
                                                <div class="sales-amount mt-3"
                                                    style="font-size: var(--lead);"> 3,000
                                                    <span class="sales-subtext">this month</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="carousel-item">
                                        <div class="d-flex flex-wrap justify-content-center my-3">
                                            <div class="card cardSmall m-2 fw-bolder p-3">
                                                <div
                                                    class="d-flex justify-content-center align-items-center">
                                                    <span>Visit Website</span>
                                                </div>
                                                <div class="sales-amount mt-3"
                                                    style="font-size: var(--lead);">
                                                    3,000
                                                    <span class="sales-subtext">this month</span>
                                                </div>
                                            </div>
                                            <div class="card cardSmall m-2 fw-bolder p-3">
                                                <div
                                                    class="d-flex justify-content-center align-items-center">
                                                    <span></span>
                                                </div>
                                                <div class="sales-amount mt-3"
                                                    style="font-size: var(--lead);">
                                                    <span class="sales-subtext"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button"
                                    data-bs-target="#smallCardCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button"
                                    data-bs-target="#smallCardCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-lg-8">
                            <div class="row d-flex flex-wrap justify-content-center">
                                <div class="card cardBig flex-grow-1 m-2">
                                    <div class="card-body text-start ms-2">
                                        <div class="subheading">Product Statistics</div>
                                        <span class="lead text-muted">Track product sales</span>
                                        <div class="card mt-3 w-100">
                                            <div class="card-body">
                                                <p class="mb-0">Sales Data</p>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm w-100">
                                                        <thead>
                                                            <tr>
                                                                <th>Product</th>
                                                                <th>Sales</th>
                                                                <th>Revenue</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Item 1</td>
                                                                <td>150</td>
                                                                <td>$3,000</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Item 2</td>
                                                                <td>230</td>
                                                                <td>$4,500</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-12">
                            <div class="card cardContainer m-2">
                                <div class="card-body text-center">
                                    <p class="fw-bold">Table</p>
                                    <div class="row mt-3 g-3 justify-content-center">

                                        <!-- Search Bar -->
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <input class="form-control" type="text" placeholder="Search"
                                                aria-label="search-bar">
                                        </div>

                                        <!-- Dropdown 1 -->
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <div class="dropdown-center">
                                                <button
                                                    class="btn btn-dropdown dropdown-toggle w-100 fw-semibold"
                                                    type="button" data-bs-toggle="dropdown">
                                                    Name
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">Ascending (A-Z)</a>
                                                    </li>
                                                    <li><a class="dropdown-item" href="#">Descending (Z-A)</a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Dropdown 2 -->
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <div class="dropdown-center">
                                                <button
                                                    class="btn btn-dropdown dropdown-toggle w-100 fw-semibold"
                                                    type="button" data-bs-toggle="dropdown">
                                                    Price
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">Price <i
                                                                class="bi bi-arrow-up text-dark"></i></a></li>
                                                    <li><a class="dropdown-item" href="#">Price <i
                                                                class="bi bi-arrow-down text-dark"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Dropdown 3 -->
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <div class="dropdown-center">
                                                <button
                                                    class="btn btn-dropdown dropdown-toggle w-100 fw-semibold"
                                                    type="button" data-bs-toggle="dropdown">
                                                    Status
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#">Available</a></li>
                                                    <li><a class="dropdown-item" href="#">Out of Stock</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Export Button -->
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                                            <button type="button" class="btn btn-success w-100"
                                                style="border-radius: 10px;" onclick="openPopup()">Export
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-12">
                                        <div class="card cardTable rounded-3 m-3" style="min-height: 30vh;">
                                            <div class="card-body">
                                                <h5 class="card-title mb-3">Order List</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Item Name</th>
                                                                <th>Category</th>
                                                                <th>Price</th>
                                                                <th>Status</th>
                                                                <th>Product ID:</th>
                                                                <th>Code</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>1</td>
                                                                <td>Benguet Blend</td>
                                                                <td>Iced Coffee/Coffee</td>
                                                                <td>₱99</td>
                                                                <td><span
                                                                        class="badge bg-success">Available</span>
                                                                </td>
                                                                <td>00001</td>
                                                                <td>ICB</td>
                                                            </tr>
                                                            <tr>
                                                                <td>2</td>
                                                                <td>Matcha Latte</td>
                                                                <td>Iced Coffee/Coffee</td>
                                                                <td>₱129</td>
                                                                <td><span class="badge bg-danger">Out of
                                                                        Stock</span></td>
                                                                <td>00002</td>
                                                                <td>ICM</td>
                                                            </tr>
                                                            <!-- Items na iloloop-->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        fetch("../modal/sales-and-report-modal.php")
            .then(res => res.text())
            .then(data => {
                document.getElementById("modal-placeholder").innerHTML = data;
                document.querySelector('.addbtn').addEventListener('click', function (e) {
                    e.preventDefault();
                    confirmOrder();
                    downloadEmptyExcel();
                });
            });

        function openPopup() {
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }

        function confirmOrder() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();

            const toastElement = document.getElementById('orderToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }

        function downloadEmptyExcel() {
            // Create a new workbook and a worksheet with no data
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet([
                []
            ]); // Empty sheet
            XLSX.utils.book_append_sheet(wb, ws, "Order");

            // Trigger download
            XLSX.writeFile(wb, "empty_order.xlsx");
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>
</body>

</html>