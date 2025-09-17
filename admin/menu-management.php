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

    $_SESSION['alertMessage'] = "Product added successfully with ingredients!";
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

$menuItemsQuery = "
    SELECT p.*, c.categoryName AS category_name,COALESCE(pc.possible_count, 0) AS possible_count
    FROM products p
    JOIN categories c ON p.categoryID = c.categoryID
    LEFT JOIN (SELECT pr.productID,MIN(FLOOR(inv.total_quantity /NULLIF(
                    CASE 
                        WHEN pr.measurementUnit = 'g' AND inv.unit = 'kg' THEN pr.requiredQuantity / 1000
                        WHEN pr.measurementUnit = 'kg' AND inv.unit = 'g' THEN pr.requiredQuantity * 1000
                        WHEN pr.measurementUnit = 'oz' AND inv.unit = 'g' THEN pr.requiredQuantity * 28.35
                        WHEN pr.measurementUnit = 'g' AND inv.unit = 'oz' THEN pr.requiredQuantity / 28.35
                        WHEN pr.measurementUnit = 'ml' AND inv.unit = 'L' THEN pr.requiredQuantity / 1000
                        WHEN pr.measurementUnit = 'L' AND inv.unit = 'ml' THEN pr.requiredQuantity * 1000
                        WHEN pr.measurementUnit = 'pump' AND inv.unit = 'ml' THEN pr.requiredQuantity * 10   -- 1 pump = 10ml
                        WHEN pr.measurementUnit = 'tbsp' AND inv.unit = 'ml' THEN pr.requiredQuantity * 15  -- 1 tbsp = 15ml
                        WHEN pr.measurementUnit = 'tsp' AND inv.unit = 'ml' THEN pr.requiredQuantity * 5    -- 1 tsp = 5ml
                        WHEN pr.measurementUnit = 'pcs' AND inv.unit = 'box' THEN pr.requiredQuantity / 12  -- 1 box = 12 pcs
                        WHEN pr.measurementUnit = 'box' AND inv.unit = 'pcs' THEN pr.requiredQuantity * 12
                        WHEN pr.measurementUnit = 'pack' AND inv.unit = 'pcs' THEN pr.requiredQuantity * 6  -- 1 pack = 6 pcs
                        WHEN pr.measurementUnit = 'pcs' AND inv.unit = 'pack' THEN pr.requiredQuantity / 6
                        WHEN pr.measurementUnit = inv.unit THEN pr.requiredQuantity ELSE pr.requiredQuantity 
                    END, 0))) AS possible_count FROM productrecipe pr JOIN (
            SELECT ingredientID, SUM(quantity) AS total_quantity, MAX(unit) AS unit
            FROM inventory
            GROUP BY ingredientID) inv ON pr.ingredientID = inv.ingredientID GROUP BY pr.productID) pc ON p.productID = pc.productID
    WHERE p.isAvailable = 'Yes'
";

if ($searchProductTerm !== '') {
    $menuItemsQuery .= " AND (p.productName LIKE '%$searchProductTerm%' OR c.categoryName LIKE '%$searchProductTerm%')";
}
if ($categoryFilterId !== null) {
    $menuItemsQuery .= " AND p.categoryID = $categoryFilterId";
}

$menuItemsQuery .= " ORDER BY p.productID DESC";
$menuItemsResults = mysqli_query($conn, $menuItemsQuery);

// Categories for dropdown
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY categoryName ASC");
$currentCategory = "All";
if ($categoryFilterId !== null) {
    $catQuery = mysqli_query($conn, "SELECT categoryName FROM categories WHERE categoryID = $categoryFilterId LIMIT 1");
    if ($catQuery && mysqli_num_rows($catQuery) > 0) {
        $catRow = mysqli_fetch_assoc($catQuery);
        $currentCategory = $catRow['categoryName'];
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
                <a href="orders.php" class="admin-nav-link">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Order Management</span>
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
            <div class="cardMain shadow-sm">

                <!-- Header Row -->
                <div class="d-none d-md-block align-items-center py-4 px-lg-3 px-2">
                    <div class="subheading fw-bold m-1 d-flex align-items-center">
                        <span style="color: var(--text-color-dark);">Menu Management</span>
                    </div>
                </div>

                <div class="row g-2 align-items-center mb-3 px-2 px-lg-3 m-3">
                    <!-- search -->
                    <div class="col">
                        <form method="get" class="d-flex">
                            <input type="text" class="form-control search ms-lg-2" name="searchProduct"
                                placeholder="Search" value="<?= htmlspecialchars($searchProductTerm) ?>">
                            <?php if (isset($_GET['categoryID'])): ?>
                                <input type="hidden" name="categoryID" value="<?= (int) $_GET['categoryID'] ?>">
                            <?php endif; ?>
                        </form>
                    </div>

                    <!-- add button -->
                    <div class="col-auto ps-0 ps-sm-3">
                        <button class="btn btnAdd" type="button" data-bs-toggle="modal" data-bs-target="#confirmModal">
                            <i class="bi bi-plus-circle"></i>
                            <span class="d-none d-sm-inline ms-2">Add</span>
                        </button>
                    </div>

                    <!-- category part -->
                    <div class="col-12 col-sm-auto">
                        <div class="dropdown">
                            <button class="btn btn-dropdown dropdown-toggle w-100" type="button" id="categoryDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?= htmlspecialchars($currentCategory) ?>
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="categoryDropdown">
                                <li>
                                    <a class="dropdown-item <?= !isset($_GET['categoryID']) ? 'active' : '' ?>"
                                        href="menu-management.php<?= !empty($searchProductTerm) ? '?searchProduct=' . urlencode($searchProductTerm) : '' ?>"
                                        style="background-color: <?= !isset($_GET['categoryID']) ? 'var(--primary-color)' : 'transparent' ?>; 
                                            color: <?= !isset($_GET['categoryID']) ? 'var(--text-color-dark)' : 'inherit' ?>;">
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

                <div id="productGrid" class="row g-2 m-3 align-items-center">
                    <?php
                    if (mysqli_num_rows($menuItemsResults) > 0) {
                        while ($row = mysqli_fetch_assoc($menuItemsResults)) {
                            $id = $row['productID'];
                            $name = $row['productName'];
                            $image = $row['image'];
                            $price = $row['price'];
                            $categoryName = $row['category_name'];
                            $possibleCount = $row['possible_count'] ?? 0;

                            echo "
        <div class='col-6 col-md-4 col-lg-2'>
            <div class='menu-item border p-3 rounded shadow-sm text-center m-lg-2'>
                <img src='../assets/img/img-menu/" . htmlspecialchars($image) . "' 
                     alt='" . htmlspecialchars($name) . "' 
                     class='img-fluid mb-2 menu-img'>

                <div class='lead menu-name fs-6'>" . htmlspecialchars($name) . "</div>
                <div class='d-flex justify-content-center align-items-center gap-2 my-2'>
                    <span class='lead fw-bold menu-price'>₱" . number_format($price, 2) . "</span>
                </div>
                <div class='text-muted' >
                    Available: " . (int) $possibleCount . " pcs
                </div>
                

                <div class='d-flex flex-wrap justify-content-center gap-2 mt-2'>
                    <button class='btn btn-sm edit-btn'
                        data-bs-toggle='modal'
                        data-bs-target='#editModal'
                        data-id='$id'>
                        <i class='bi bi-pencil-square'></i> Edit
                     </button>
                    <form method='POST' class='deleteProductForm'>
                        <input type='hidden' name='productID' value='$id'>
                        <input type='hidden' name='btnDeleteProduct' value='1'>
                        <button type='submit' class='btn btn-del'>
                            <i class='bi bi-trash'></i> Delete
                        </button>
                    </form>
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
    </div>

    </div>


    <?php include '../modal/menu-management-confirm-modal.php'; ?>
    <?php include '../modal/menu-management-edit-modal.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min js"></script>
    <script src="../assets/js/menu-management.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Delete Product Confirmation
            document.querySelectorAll('.deleteProductForm').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This product will be deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: 'var(--primary-color)',
                        cancelButtonColor: 'var(--btn-hover2)',
                        customClass: {
                            popup: 'swal2-border-radius',
                            confirmButton: 'swal2-confirm-radius',
                            cancelButton: 'swal2-cancel-radius'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // para sa search product (ADD modal only)
            $(document).ready(function () {
                var ingredients = <?php echo json_encode($ingredients); ?>;

                function initAutocomplete(selector) {
                    $(selector).autocomplete({
                        source: ingredients,
                        minLength: 1,
                        appendTo: "#confirmModal", // scoped sa Add modal
                        select: function (event, ui) {
                            $(this).val(ui.item.label);
                            $(this).siblings(".ingredient-id").val(ui.item.id);

                            // attach correct unit for this ingredient
                            $(this).closest(".ingredient-row")
                                .find(".measurement-select")
                                .data("correct-unit", ui.item.unit);

                            return false;
                        },
                        change: function (event, ui) {
                            if (!ui.item) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Ingredient Not Found',
                                    text: 'The ingredient you entered is not on the Inventory.',
                                    confirmButtonColor: 'var(--primary-color)',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        popup: 'swal2-border-radius',
                                        confirmButton: 'swal2-confirm-radius',
                                        cancelButton: 'swal2-cancel-radius'
                                    }
                                });
                                $(this).val("");
                                $(this).siblings(".ingredient-id").val("");
                            }
                        }
                    });
                }

                // initialize autocomplete for inputs inside Add Product modal only
                initAutocomplete("#confirmModal .ingredient-search");

                // Add new ingredient row inside Add modal
                $("#confirmModal #add-modal-ingredient").click(function () {
                    var row = `
                    <div class="row g-2 mb-2 ingredient-row">
                            <div class="col-md-5">
                                <input type="text" class="form-control ingredient-search"
                                    placeholder="Search Ingredient" required
                                    style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                          font-family: var(--secondaryFont); background: var(--card-bg-color);
                                          color: var(--text-color-dark); padding: 12px;">
                                <input type="hidden" name="ingredientID[]" class="ingredient-id">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="requiredQuantity[]"
                                    placeholder="Quantity" step="any" required
                                    style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                          font-family: var(--secondaryFont); background: var(--card-bg-color);
                                          color: var(--text-color-dark); padding: 12px;">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select measurement-select" name="measurementUnit[]" required
                                    style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                          font-family: var(--secondaryFont); background: var(--card-bg-color);
                                          color: var(--text-color-dark); padding: 12px;">
                                    <option value="" disabled selected>Select Unit</option>
                                    <option value="pcs">pcs</option>
                                    <option value="box">box</option>
                                    <option value="pack">pack</option>
                                    <option value="g">g</option>
                                    <option value="kg">kg</option>
                                    <option value="oz">oz</option>
                                    <option value="ml">ml</option>
                                    <option value="L">L</option>
                                    <option value="pump">pump</option>
                                    <option value="tbsp">tbsp</option>
                                    <option value="tsp">tsp</option>
                                </select>
                                <input type="text" class="form-control mt-2 d-none custom-unit" name="customUnit[]"
                                    placeholder="Enter custom unit"
                                    style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                          font-family: var(--secondaryFont); background: var(--card-bg-color);
                                          color: var(--text-color-dark); padding: 12px;">
                            </div>
                            <div class="col-md-1 d-flex align-items-center">
                                <button type="button" class="btn btn-sm remove-ingredient"
                                    style="background: var(--card-bg-color); 
                                           color: var(--text-color-dark); 
                                           border: 2px solid var(--primary-color);
                                           border-radius: 8px; font-family: var(--primaryFont);">
                                    &times;
                                </button>
                            </div>
                        </div>`;
                    $("#confirmModal #ingredients-container").append(row);

                    // autocomplete for newly added row (scoped to Add modal)
                    initAutocomplete($("#confirmModal #ingredients-container .ingredient-search").last());
                });
                // remove ingredient row (scoped to Add modal)
                $(document).on("click", "#confirmModal .remove-ingredient", function () {
                    $(this).closest(".ingredient-row").remove();
                });
                // Unit mismatch validation
                // Unit mismatch validation (mirrors SQL CASE conversions)
                $(document).on("change", "#confirmModal .measurement-select", function () {
                    const correctUnit = $(this).data("correct-unit"); // from ingredient
                    const chosenUnit = $(this).val(); // from user

                    if (correctUnit) {
                        // allowed conversions (same as SQL)
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

                        const validUnits = allowedUnits[correctUnit] || [correctUnit];

                        if (!validUnits.includes(chosenUnit)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Unit Mismatch',
                                text: `This ingredient requires "${correctUnit}" (allowed: ${validUnits.join(", ")}), not "${chosenUnit}".`,
                                confirmButtonColor: 'var(--primary-color)'
                            });
                            $(this).val(""); // reset invalid unit
                        }
                    }
                });
            });

            // Session Alerts
            <?php if (isset($_SESSION['alertMessage'])): ?>
                Swal.fire({
                    icon: '<?= $_SESSION['alertType'] ?>', // success, error, warning, info, question
                    title: '<?= $_SESSION['alertMessage'] ?>',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
                });
                <?php
                unset($_SESSION['alertMessage']);
                unset($_SESSION['alertType']);
                ?>
            <?php endif; ?>
        });



        window.ingredients = <?php echo json_encode($ingredients); ?>;
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>
</body>

</html>