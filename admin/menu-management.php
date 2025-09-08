<?php
include '../assets/connect.php';
session_start();

// Check if user is logged in and is an admin 
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Menu Items Query
$menuItemsQuery = "
    SELECT 
        p.*, 
        c.categoryName AS category_name
    FROM 
        Products p
    JOIN 
        Categories c ON p.categoryID = c.categoryID
    WHERE 
        p.isAvailable = 'Yes'
";


$menuItemsResults = executeQuery($menuItemsQuery);
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu Management</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/menu-management.css">
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
        <h4 class="mobile-header-title">Menu Management</h4>
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
    <div class="main-content">
        <div class="container-fluid">
            <div class="cardMain shadow-sm">

                <!-- Header Row  -->
                <div class="d-none d-md-block align-items-center py-4 px-lg-3 px-2">
                    <div class="subheading fw-bold m-1 d-flex align-items-center">
                        <span style="color: var(--text-color-dark);">Menu Management</span>
                    </div>
                </div>

                <div class="row g-2 align-items-center mb-3 px-2 px-lg-3 m-3">
                    <!-- search -->
                    <div class="col">
                        <input type="text" class="form-control search ms-lg-2" placeholder="Search"
                            aria-label="search-bar" id="item-input">
                    </div>
                    <!-- add button -->
                    <div class="col-auto ps-0 ps-sm-3">
                        <button class="btn btnAdd" type="button" data-bs-toggle="modal" data-bs-target="#confirmModal">
                            <i class="bi bi-plus-circle"></i>
                            <span class="d-none d-sm-inline ms-2">Add</span>
                        </button>
                    </div>

                    <!-- category part  -->
                    <div class="col-12 col-sm-auto">
                        <div class="dropdown">
                            <button class="btn btn-dropdown dropdown-toggle w-100" type="button" id="categoryDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Sort by Category
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="categoryDropdown">
                                <li><a class="dropdown-item" data-value="coffee">Coffee</a></li>
                                <li><a class="dropdown-item" data-value="tea">Tea</a></li>
                                <li><a class="dropdown-item" data-value="food">Food</a></li>
                                <li><a class="dropdown-item" data-value="beverage">Beverage</a></li>
                            </ul>
                        </div>
                    </div>
                </div>


                <!-- Menu -->

                <div id="productGrid" class="row g-2 m-3 align-items-center">
                    <?php
                    if (mysqli_num_rows($menuItemsResults) > 0) {
                        while ($row = mysqli_fetch_assoc($menuItemsResults)) {
                            $id = $row['productID'];
                            $name = $row['productName'];
                            $image = $row['image'];
                            $price = $row['price'];
                            $categoryName = $row['category_name'];

                            echo "
        <div class='col-6 col-md-4 col-lg-2'>
            <div class='menu-item border p-3 rounded shadow-sm text-center'>
                <img src='../assets/img/img-menu/" . htmlspecialchars($image) . "' 
                     alt='" . htmlspecialchars($name) . "' 
                     class='img-fluid mb-2 menu-img'>

                <div class='lead menu-name fs-6'>" . htmlspecialchars($name) . "</div>
                <div class='d-flex justify-content-center align-items-center gap-2 my-2'>
                    <span class='lead fw-bold menu-price'>₱" . number_format($price, 2) . "</span>
                </div>

                <div class='d-flex flex-wrap justify-content-center gap-2'>
                    <button class='btn btn-sm edit-btn'
                        data-bs-toggle='modal'
                        data-bs-target='#editModal'
                        data-id='$id'>
                        <i class='bi-pencil-square'></i> Edit
                    </button>
                    <button class='btn btn-sm delete-btn' data-id='$id'>
                        <i class='bi-trash'></i> Delete
                    </button>
                </div>
            </div>
        </div>
        ";
                        }
                    } else {
                        echo "<p class='text-center'>No products available.</p>";
                    }
                    ?>
                </div>
            </div>
            <!-- Toast Container -->
            <div class="position-fixed bottom-0 end-0 p-3 " style="z-index: 1100">
                <div id="updateToast" class="toast align-items-center updateToast border-0" role="alert"
                    aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            Product updated successfully!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include '../modal/menu-management-confirm-modal.php'; ?>
    <?php include '../modal/menu-management-edit-modal.php'; ?>
    <script src="../assets/js/menu-management.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>

</body>

</html>