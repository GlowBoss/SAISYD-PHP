<?php

include('../assets/connect.php');
session_start();

// Prevent unauthorized access
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin') {
    // Redirect non-admin users to login page or a "no access" page


    header("Location: login.php");
    exit();
}


$searchProductTerm = '';
$categoryFilterId = null;

// dito kona nilagay sa taas tong pag 
$result = mysqli_query($conn, "
    SELECT i.ingredientID, ing.ingredientName, i.unit
    FROM inventory i
    JOIN ingredients ing ON i.ingredientID = ing.ingredientID
    ORDER BY ing.ingredientName ASC
");

while ($row = mysqli_fetch_assoc($result)) {
    $ingredients[] = [
        "id" => $row['ingredientID'],
        "label" => $row['ingredientName'],
        "value" => $row['ingredientName'],
        "unit" => $row['unit']
    ];
}

// ADDDDDD PRODUCT
if (isset($_POST['btnAddProduct'])) {
    $productName = mysqli_real_escape_string($conn, $_POST['productName']);
    $price = $_POST['price'];
    $availableQuantity = $_POST['availableQuantity'];
    $categoryID = $_POST['categoryID'];
    $isAvailable = 1;
    $image = NULL;
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $imageTemp = $_FILES['image']['tmp_name'];
        $targetDir = "../assets/img/img-menu/";
        $targetFile = $targetDir . $image;

        // check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
        $fileType = mime_content_type($imageTemp);

        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['alertMessage'] = "Invalid file type. Please upload only JPG, PNG, GIF, or WEBP images.";
            $_SESSION['alertType'] = "error";
            header("Location: menu-management.php");
            exit();
        }
        // don't move yet only after insert
    }
    // check duplicate 
    $checkQuery = "SELECT * FROM products 
                   WHERE productName = '$productName' ";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $_SESSION['alertMessage'] = "This product already exists.";
        $_SESSION['alertType'] = "error";
        header("Location: menu-management.php");
        exit();
    }
    // insert into products
    $insertProduct = "INSERT INTO products (productName, categoryID, price, availableQuantity, image, isAvailable) 
                      VALUES ('$productName', '$categoryID', '$price', '$availableQuantity', '$image', '$isAvailable')";
    if (mysqli_query($conn, $insertProduct)) {
        $productID = mysqli_insert_id($conn);

        // only move image the file if insert is successful
        if (!empty($_FILES['image']['name'])) {
            move_uploaded_file($imageTemp, $targetFile);
        }

        // insert into productrecipe
        if (!empty($_POST['ingredientID'])) {
            foreach ($_POST['ingredientID'] as $index => $ingredientID) {
                $ingredientID = intval($ingredientID);
                $qty = $_POST['requiredQuantity'][$index];
                $unit = mysqli_real_escape_string($conn, $_POST['measurementUnit'][$index]);

                $insertRecipe = "INSERT INTO productrecipe (ingredientID, productID, measurementUnit, requiredQuantity)
                                 VALUES ('$ingredientID', '$productID', '$unit', '$qty')";
                mysqli_query($conn, $insertRecipe);
            }
        }
    }

    $_SESSION['alertMessage'] = "Product added successfully!";
    $_SESSION['alertType'] = "success";
    header("Location: menu-management.php");
    exit();
}

// DELETEEEE
if (isset($_POST['btnDeleteProduct'])) {
    $productID = intval($_POST['productID']);

    if ($productID > 0) {
        $result = mysqli_query($conn, "SELECT image FROM products WHERE productID = '$productID'");
        $row = mysqli_fetch_assoc($result);
        if ($row && !empty($row['image'])) {
            $imagePath = "../assets/img/img-menu/" . $row['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        mysqli_query($conn, "DELETE FROM productrecipe WHERE productID = '$productID'");
        mysqli_query($conn, "DELETE FROM products WHERE productID = '$productID'");

        $_SESSION['alertMessage'] = "Product deleted successfully!";
        $_SESSION['alertType'] = "success";
    } else {
        $_SESSION['alertMessage'] = "Invalid product ID!";
        $_SESSION['alertType'] = "error";
    }
    header("Location: menu-management.php");
    exit();
}


if (!empty($_GET['searchProduct'])) {
    $searchProductTerm = mysqli_real_escape_string($conn, $_GET['searchProduct']);
}
if (!empty($_GET['categoryID'])) {
    $categoryFilterId = (int) $_GET['categoryID'];
}

// TOGGLE AVAILABILITY
if (isset($_POST['btnToggleAvailability'])) {
    $productID = intval($_POST['productID']);
    $newAvailability = intval($_POST['newAvailability']);
    $response = [];

    if ($productID > 0) {
        $updateQuery = "UPDATE products SET isAvailable = '$newAvailability' WHERE productID = '$productID'";

        if (mysqli_query($conn, $updateQuery)) {
            $statusText = $newAvailability ? 'enabled' : 'disabled';
            $response['success'] = true;
            $response['message'] = "Product availability has been $statusText successfully!";
        } else {
            $response['success'] = false;
            $response['message'] = "Failed to update product availability!";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Invalid product ID!";
    }

    // Return JSON instead of redirecting
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}



// Fetch menu items
$menuItemsQuery = "
    SELECT 
        p.*, 
        c.categoryName AS category_name,
        COALESCE(pc.possible_count, 0) AS possible_count
    FROM 
        products p
    JOIN 
        categories c ON p.categoryID = c.categoryID
    LEFT JOIN (
        SELECT 
            pr.productID,
            MIN(FLOOR(inv.total_quantity / NULLIF(
                CASE 
                    WHEN pr.measurementUnit = 'g' AND inv.unit = 'kg' THEN pr.requiredQuantity / 1000
                    WHEN pr.measurementUnit = 'kg' AND inv.unit = 'g' THEN pr.requiredQuantity * 1000
                    WHEN pr.measurementUnit = 'oz' AND inv.unit = 'g' THEN pr.requiredQuantity * 28.35
                    WHEN pr.measurementUnit = 'g' AND inv.unit = 'oz' THEN pr.requiredQuantity / 28.35
                    WHEN pr.measurementUnit = 'ml' AND inv.unit = 'L' THEN pr.requiredQuantity / 1000
                    WHEN pr.measurementUnit = 'L' AND inv.unit = 'ml' THEN pr.requiredQuantity * 1000
                    WHEN pr.measurementUnit = 'pump' AND inv.unit = 'ml' THEN pr.requiredQuantity * 10
                    WHEN pr.measurementUnit = 'tbsp' AND inv.unit = 'ml' THEN pr.requiredQuantity * 15
                    WHEN pr.measurementUnit = 'tsp' AND inv.unit = 'ml' THEN pr.requiredQuantity * 5
                    WHEN pr.measurementUnit = 'pcs' AND inv.unit = 'box' THEN pr.requiredQuantity / 12
                    WHEN pr.measurementUnit = 'box' AND inv.unit = 'pcs' THEN pr.requiredQuantity * 12
                    WHEN pr.measurementUnit = 'pack' AND inv.unit = 'pcs' THEN pr.requiredQuantity * 6
                    WHEN pr.measurementUnit = 'pcs' AND inv.unit = 'pack' THEN pr.requiredQuantity / 6
                    WHEN pr.measurementUnit = inv.unit THEN pr.requiredQuantity
                    ELSE pr.requiredQuantity 
                END, 0
            ))) AS possible_count
        FROM productrecipe pr
        JOIN (
            SELECT ingredientID, SUM(quantity) AS total_quantity, MAX(unit) AS unit
            FROM inventory
            GROUP BY ingredientID
        ) inv ON pr.ingredientID = inv.ingredientID
        GROUP BY pr.productID
    ) pc ON p.productID = pc.productID
    WHERE 1=1
";

if ($searchProductTerm !== '') {
    $menuItemsQuery .= " AND (p.productName LIKE '%$searchProductTerm%' OR c.categoryName LIKE '%$searchProductTerm%')";
}
if ($categoryFilterId !== null) {
    $menuItemsQuery .= " AND p.categoryID = $categoryFilterId";
}

$menuItemsQuery .= " ORDER BY p.productID DESC";
$menuItemsResults = mysqli_query($conn, $menuItemsQuery);

$menuItems = [];

// Update availability if possible_count is 0
while ($row = mysqli_fetch_assoc($menuItemsResults)) {
    $productID = $row['productID'];
    $possibleCount = $row['possible_count'] ?? 0;

    if ($possibleCount == 0 && $row['isAvailable'] == 1) {
        mysqli_query($conn, "UPDATE products SET isAvailable = 0 WHERE productID = $productID");
        $row['isAvailable'] = 0; // update for display
    }

    $menuItems[] = $row;
}

$currentCategory = 'All'; // default
if ($categoryFilterId !== null) {
    $categoryData = mysqli_query($conn, "SELECT categoryName FROM categories WHERE categoryID = $categoryFilterId");
    if ($row = mysqli_fetch_assoc($categoryData)) {
        $currentCategory = $row['categoryName'];
    }
}

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

    <!-- jquery import ginamit ko ito para sa bagong js ng search at pag add ng row sa ingredient (nagpatulong nako kay bro dito kung pano gamitin) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">


</head>


<body>
    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Mobile Menu Toggle Button -->
    <div class="d-md-none mobile-header d-flex align-items-center pt-3 px-3">
        <button id="menuToggle" class="mobile-menu-toggle me-3">
            <i class="fas fa-bars"></i>
        </button>
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
                <a href="orders.php" class="admin-nav-link">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Order Management</span>
                </a>
                <a href="point-of-sales.php" class="admin-nav-link">
                    <i class="bi bi-shop-window"></i>
                    <span>Point of Sales</span>
                </a>
                <a href="inventory-management.php" class="admin-nav-link active">
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
                <a href="settings.php" class="admin-nav-link">
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
            <a href="orders.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.15s">
                <i class="bi bi-clipboard-check"></i>
                <span>Order Management</span>
            </a>
            <a href="point-of-sales.php" class="admin-nav-link wow animate__animated animate__fadeInLeft"
                data-wow-delay="0.2s">
                <i class="bi bi-shop-window"></i>
                <span>Point of Sales</span>
            </a>
            <a href="inventory-management.php" class="admin-nav-link active wow animate__animated animate__fadeInLeft"
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
            <div class="enhanced-page-header">
                <div class="header-content">
                    <div class="header-title">
                        <h1 class="page-title-text">Menu Management</h1>
                    </div>
                </div>
            </div>

            <div class="action-bar mb-4">
                <div class="row g-3 align-items-end">

                    <!-- Quick Search (Left Side) -->
                    <div class="col-12 col-md-12 col-lg-5 px-md-4">
                        <form method="get" class="search-container">
                            <div class="input-group search-bar">
                                <input type="text" class="form-control search-input" name="searchProduct"
                                    placeholder="Search by item name or code..."
                                    value="<?= htmlspecialchars($searchProductTerm) ?>">
                                <button class="btn search-btn" type="submit">
                                    <i class="bi bi-search"></i>
                                    <span class="d-none d-sm-inline ms-1">Search</span>
                                </button>
                                <?php if (isset($_GET['categoryID'])): ?>
                                    <input type="hidden" name="categoryID" value="<?= (int) $_GET['categoryID'] ?>">
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>


                    <!-- Actions (Right Side) -->
                    <div class="col-12 col-md-12 col-lg-7 px-lg-4">
                        <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-lg-end w-100">

                            <!-- Add Button -->
                            <button class="action-btn" type="button" data-bs-toggle="modal"
                                data-bs-target="#confirmModal">
                                <i class="bi bi-plus-circle"></i>
                                <span class="d-sm-inline">Add</span>
                            </button>

                            <!-- Category Dropdown -->
                            <div class="dropdown">
                                <button class="filter-toggle-btn dropdown-toggle" type="button" id="categoryDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <?= htmlspecialchars($currentCategory) ?>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                                    <li>
                                        <a class="dropdown-item <?= !isset($_GET['categoryID']) ? 'active' : '' ?> "
                                            href="menu-management.php<?= !empty($searchProductTerm) ? '?searchProduct=' . urlencode($searchProductTerm) : '' ?>"
                                            style="background-color: <?= !isset($_GET['categoryID']) ? 'var(--primary-color)' : 'transparent' ?>; 
                               color: <?= !isset($_GET['categoryID']) ? 'var(--text-color-light)' : 'inherit' ?>;">
                                            All
                                        </a>
                                    </li>
                                    <?php
                                    $categories = executeQuery("SELECT * FROM categories ORDER BY categoryName ASC");
                                    while ($category = mysqli_fetch_assoc($categories)) {
                                        $isActive = (isset($_GET['categoryID']) && $_GET['categoryID'] == $category['categoryID']) ? 'active' : '';
                                        $url = "menu-management.php?categoryID=" . $category['categoryID'];
                                        if (!empty($searchProductTerm)) {
                                            $url .= "&searchProduct=" . urlencode($searchProductTerm);
                                        }
                                        echo '<li><a class="dropdown-item ' . $isActive . '" href="' . $url . '">'
                                            . htmlspecialchars($category['categoryName']) . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>



                <div id="productGrid" class="row g-3 m-2">
                    <?php
                    if (!empty($menuItems)) {
                        foreach ($menuItems as $row) {
                            $id = $row['productID'];
                            $name = $row['productName'];
                            $image = $row['image'];
                            $price = $row['price'];
                            $categoryName = $row['category_name'];
                            $possibleCount = $row['possible_count'] ?? 0;

                            $isAvailable = $possibleCount > 0 ? 1 : 0;
                            $unavailableClass = $isAvailable ? '' : ' unavailable';
                            $statusBadgeClass = $isAvailable ? 'status-available' : 'status-unavailable';
                            $statusText = $isAvailable ? 'Available' : 'Unavailable';

                            echo "
            <div class='col-6 col-md-6 col-lg-4 col-xl-2 d-flex px-3 py-2'>
                <div class='menu-item w-100 text-center$unavailableClass'>
                    <div class='mb-2'>
                        <span class='status-badge $statusBadgeClass'>$statusText</span>
                    </div>

                    <div class='menu-img-container'>
                        <img src='../assets/img/img-menu/" . htmlspecialchars($image) . "'
                            alt='" . htmlspecialchars($name) . "'
                            class='img-fluid menu-img " . ($isAvailable ? "" : "img-unavailable") . "'>
                    </div>

                    <div class='menu-name'>" . htmlspecialchars($name) . "</div>
                    <div class='menu-price'>₱" . number_format($price, 2) . "</div>
                    <div class='menu-stock'>Available: " . (int) $possibleCount . " pcs</div>

                    <div class='d-flex flex-wrap justify-content-center gap-2 mt-2'>
                        <button class='btn btn-sm edit-btn'
                            data-bs-toggle='modal'
                            data-bs-target='#editModal'
                            data-id='$id'
                            data-available='" . ($isAvailable ? "1" : "0") . "'>
                            <i class='bi bi-pencil-square'></i> 
                        </button>

                        <button type='button' class='btn btn-del' 
                            data-bs-toggle='modal' 
                            data-bs-target='#deleteConfirmModal'
                            data-product-id='" . $id . "'
                            data-product-name='" . htmlspecialchars($name) . "'>
                            <i class='bi bi-trash'></i>
                        </button>

                    </div>
                </div>
            </div>
            ";
                        }
                    } else {
                        // No products message + clear search button
                        echo "
        <div class='col-12 text-center'>
        <p>No items match the current filters</p>
        <a href='?' class='btn clear-btn mt-2'>
            <i class='bi bi-x-circle'></i> Clear Search 
        </a>
    </div>
        ";
                    }
                    ?>
                </div>





                <!-- Toast Container -->
                <div class=" toast-container p-3 position-fixed end-0 p-3 " style="z-index: 1100">
                    <div id="updateToast" class="toast align-items-center updateToast border-0" role="alert"
                        aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-check-circle-fill px-2"></i>
                                Product updated successfully!
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                                aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Container -->
        <div class="toast-container p-3">
            <div id="addToast" class="toast align-items-center border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex align-items-center">
                    <div class="toast-body d-flex align-items-center">
                        <i id="toastIcon" class="bi "></i>
                        <span id="toastMessage" class="px-2"></span>
                    </div>

                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>




        <?php include '../modal/menu-management-confirm-modal.php'; ?>
        <?php include '../modal/menu-management-edit-modal.php'; ?>
        <?php include '../modal/delete-confirm-menu-management.php'; ?>


        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min js"></script>
        <script src="../assets/js/menu-management.js"></script>
        <script src="../assets/js/admin_sidebar.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const deleteModal = document.getElementById('deleteConfirmModal');

                deleteModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget; // Button that triggered the modal
                    const productId = button.getAttribute('data-product-id');
                    const productName = button.getAttribute('data-product-name');

                    // Update modal content
                    deleteModal.querySelector('#deleteItemName').textContent = productName;
                    deleteModal.querySelector('#deleteProductID').value = productId;
                });
            });



            document.addEventListener('DOMContentLoaded', () => {
                $(document).ready(function () {
                    var ingredients = <?php echo json_encode($ingredients); ?>;
                    let skipAutocompleteChange = false; // flag to skip alert

                    function initAutocomplete(selector) {
                        $(selector).autocomplete({
                            source: ingredients,
                            minLength: 1,
                            appendTo: "#confirmModal",
                            select: function (event, ui) {
                                $(this).val(ui.item.label);
                                $(this).siblings(".ingredient-id").val(ui.item.id);
                                $(this).closest(".ingredient-row")
                                    .find(".measurement-select")
                                    .data("correct-unit", ui.item.unit);
                                return false;
                            },
                            change: function (event, ui) {
                                if (skipAutocompleteChange) return; // skip when cleared manually
                                if (!ui.item) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Ingredient Not Found',
                                        text: 'The ingredient you entered is not on the Inventory.',
                                        confirmButtonColor: 'var(--primary-color)'
                                    });
                                    $(this).val("");
                                    $(this).siblings(".ingredient-id").val("");
                                    $(this).siblings(".cancel-search").hide();
                                }
                            }
                        });
                    }

                    // initialize autocomplete for existing inputs
                    initAutocomplete("#confirmModal .ingredient-search");

                    // Add new ingredient row
                    $("#confirmModal #add-modal-ingredient").click(function () {
                        var row = `
                        <div class="row g-2 mb-2 ingredient-row">
                            <div class="col-md-5 position-relative">
                                <input type="text" class="form-control ingredient-search"
                                    placeholder="Search Ingredient" required style="border: 2px solid var(--primary-color); border-radius: 10px; 
          font-family: var(--secondaryFont); background: var(--card-bg-color);
          color: var(--text-color-dark); padding: 12px;">
                                <input type="hidden" name="ingredientID[]" class="ingredient-id">
                                <button type="button" class="cancel-search" style="position:absolute; right:8px; top:50%; transform:translateY(-50%);
          border:none; background:none; color:#333; font-size:18px; display:none; cursor:pointer;">
                                    &times;
                                </button>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="requiredQuantity[]"
                                    placeholder="Quantity" step="any" required style="border: 2px solid var(--primary-color); border-radius: 10px; 
          font-family: var(--secondaryFont); background: var(--card-bg-color);
          color: var(--text-color-dark); padding: 12px;">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select measurement-select" name="measurementUnit[]" required style="border: 2px solid var(--primary-color); border-radius: 10px; 
          font-family: var(--secondaryFont); background: var(--card-bg-color);
          color: var(--text-color-dark); padding: 12px;">
          <option value="" disabled selected>Select Unit</option>
                                    <option value="pcs">Pieces (pcs)</option>
                                    <option value="box">Box</option>
                                    <option value="pack">Pack</option>
                                    <option value="g">Gram (g)</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="oz">Ounce (oz)</option>
                                    <option value="ml">Milliliter (ml)</option>
                                    <option value="L">Liter (L)</option>
                                    <option value="pump">Pump</option>
                                    <option value="tbsp">Tablespoon (tbsp)</option>
                                    <option value="tsp">Teaspoon (tsp)</option>
                                </select>
                                <input type="text" class="form-control mt-2 d-none custom-unit" name="customUnit[]"
                                    placeholder="Enter custom unit" style="border: 2px solid var(--primary-color); border-radius: 10px; 
          font-family: var(--secondaryFont); background: var(--card-bg-color);
          color: var(--text-color-dark); padding: 12px;">
                            </div>
                            <div class="col-md-1 d-flex justify-content-center align-items-center">
                                <button type="button"
                                    class="btn btn-sm btn-del remove-ingredient d-flex justify-content-center align-items-center"
                                    style="border-radius: 10px; width: 38px; height: 38px; transition: all 0.3s ease;">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </div>
                        </div>`;
                        $("#confirmModal #ingredients-container").append(row);

                        // autocomplete for new row
                        initAutocomplete($("#confirmModal #ingredients-container .ingredient-search").last());
                    });

                    // remove ingredient row
                    $(document).on("click", "#confirmModal .remove-ingredient", function () {
                        $(this).closest(".ingredient-row").remove();
                    });

                    // cancel search button click
                    $(document).on("click", "#confirmModal .cancel-search", function () {
                        skipAutocompleteChange = true; // skip alert
                        const input = $(this).siblings(".ingredient-search");
                        input.val("");
                        input.siblings(".ingredient-id").val("");
                        $(this).hide();
                        setTimeout(() => skipAutocompleteChange = false, 10);
                    });

                    // show/hide cancel search button on input
                    $(document).on("input", "#confirmModal .ingredient-search", function () {
                        $(this).siblings(".cancel-search").toggle($(this).val().trim() !== "");
                    });

                    // Unit mismatch validation
                    $(document).on("change", "#confirmModal .measurement-select", function () {
                        const correctUnit = $(this).data("correct-unit");
                        const chosenUnit = $(this).val();
                        if (!correctUnit) return;

                        const allowedUnits = {
                            "g": ["g", "kg", "oz"],
                            "kg": ["kg", "g"],
                            "oz": ["oz", "g"],
                            "ml": ["ml", "L", "pump", "tbsp", "tsp"],
                            "L": ["L", "ml"],
                            "pump": ["pump", "ml"],
                            "tbsp": ["tbsp", "ml"],
                            "tsp": ["tsp", "ml"],
                            "pcs": ["pcs", "box", "pack"],
                            "box": ["box", "pcs"],
                            "pack": ["pack", "pcs"]
                        };

                        if (!allowedUnits[correctUnit].includes(chosenUnit)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Unit Mismatch',
                                text: `This ingredient requires "${correctUnit}" (allowed: ${allowedUnits[correctUnit].join(", ")}), not "${chosenUnit}".`,
                                confirmButtonColor: 'var(--primary-color)'
                            });
                            $(this).val("");
                        }
                    });
                });

                document.addEventListener('DOMContentLoaded', function () {
                    const availabilityToggle = document.getElementById('availabilityToggle');
                    const availabilityStatus = document.getElementById('availabilityStatus');
                    let currentProductId = null;

                    // Update modal toggle with product data
                    document.querySelectorAll('.edit-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            currentProductId = this.getAttribute('data-id');
                            const isAvailable = this.getAttribute('data-available') === '1';

                            // Set toggle state properly
                            availabilityToggle.checked = isAvailable;
                            updateAvailabilityStatus(isAvailable);
                        });
                    });


                    // Handle toggle change
                    if (availabilityToggle) {
                        availabilityToggle.addEventListener('change', function () {
                            const isChecked = this.checked;
                            updateAvailabilityStatus(isChecked);

                            if (currentProductId) {
                                updateProductAvailability(currentProductId, isChecked ? 1 : 0);
                            }
                        });
                    }

                    // Update text + badge inside modal
                    function updateAvailabilityStatus(isAvailable) {
                        if (availabilityStatus) {
                            availabilityStatus.textContent = isAvailable ? 'Available' : 'Unavailable';
                            availabilityStatus.className = 'status-badge ' +
                                (isAvailable ? 'status-available' : 'status-unavailable');
                        }
                    }

                    // AJAX request to update availability
                    function updateProductAvailability(productId, newAvailability) {
                        fetch('menu-management.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `btnToggleAvailability=1&productID=${productId}&newAvailability=${newAvailability}`
                        })
                            .then(response => response.text())
                            .then(() => {
                                // Update product card without reloading
                                const productCard = document.querySelector(`.edit-btn[data-id="${productId}"]`).closest('.menu-item');
                                const badge = productCard.querySelector('.status-badge');

                                if (newAvailability == 1) {
                                    productCard.classList.remove('unavailable');
                                    badge.textContent = 'Available';
                                    badge.className = 'status-badge status-available';
                                } else {
                                    productCard.classList.add('unavailable');
                                    badge.textContent = 'Unavailable';
                                    badge.className = 'status-badge status-unavailable';
                                }

                                // Show toast
                                const toast = document.getElementById('updateToast');
                                if (toast) {
                                    const bsToast = new bootstrap.Toast(toast);
                                    bsToast.show();
                                }
                            })
                            .catch(err => console.error('Error updating product availability:', err));
                    }
                });

            });



            window.ingredients = <?php echo json_encode($ingredients); ?>;

            const ingredientsData = <?php echo json_encode($ingredients); ?>;

            // Polling interval in milliseconds
            const POLL_INTERVAL = 5000; // every 5 seconds

            function fetchProductAvailability() {
                fetch('fetch-availability.php') // new endpoint that returns JSON of productID -> possible_count
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(item => {
                            const productCard = document.querySelector(`.edit-btn[data-id="${item.productID}"]`)?.closest('.menu-item');
                            if (!productCard) return;

                            const badge = productCard.querySelector('.status-badge');
                            const newAvailable = item.possible_count > 0 ? 1 : 0;

                            // Only update if changed
                            const isCurrentlyAvailable = badge.classList.contains('status-available');
                            if ((newAvailable && !isCurrentlyAvailable) || (!newAvailable && isCurrentlyAvailable)) {
                                if (newAvailable) {
                                    productCard.classList.remove('unavailable');
                                    badge.textContent = 'Available';
                                    badge.className = 'status-badge status-available';
                                } else {
                                    productCard.classList.add('unavailable');
                                    badge.textContent = 'Unavailable';
                                    badge.className = 'status-badge status-unavailable';
                                }
                            }
                        });
                    })
                    .catch(err => console.error('Error fetching availability:', err));
            }

            // Start polling
            setInterval(fetchProductAvailability, POLL_INTERVAL);

        </script>
        <?php if (isset($_SESSION['alertMessage'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const toastEl = document.getElementById('addToast');
                    const toastBody = document.getElementById('toastMessage');
                    const toastIcon = document.getElementById('toastIcon');
                    const bsToast = new bootstrap.Toast(toastEl, { delay: 2500 });

                    const message = '<?= $_SESSION['alertMessage'] ?>';
                    const type = '<?= $_SESSION['alertType'] ?>'; // success, error, warning

                    toastBody.textContent = message;

                    // Use Bootstrap Icons
                    if (type === 'error') {
                        toastIcon.className = 'bi bi-x-circle-fill text-danger';
                    } else if (type === 'warning') {
                        toastIcon.className = 'bi bi-exclamation-triangle-fill';
                    } else {
                        toastIcon.className = 'bi bi-check-circle-fill';
                    }

                    bsToast.show();
                });
            </script>

            <?php
            unset($_SESSION['alertMessage']);
            unset($_SESSION['alertType']);
            ?>
        <?php endif; ?>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
            </script>
</body>

</html>