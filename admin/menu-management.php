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
$categoryFilter = '';

// dito kona nilagay sa taas tong pag 
$ingredients = [];
$result = mysqli_query($conn, "SELECT ingredientID, ingredientName FROM ingredients ORDER BY ingredientName ASC");
while ($row = mysqli_fetch_assoc($result)) {
    $ingredients[] = [
        "id" => $row['ingredientID'],
        "label" => $row['ingredientName'],
        "value" => $row['ingredientName']
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
        $targetDir = "../assets/product-images/";
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
            $imagePath = "../assets/product-images/" . $row['image'];
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



// fetch lahat 
$productGetQuery = "
    SELECT products.productID, products.productName, products.image, 
           products.availableQuantity, products.price, products.isAvailable, 
           categories.categoryName
    FROM products
    LEFT JOIN categories ON products.categoryID = categories.categoryID
    WHERE 1=1
";

// Search product
if (isset($_GET['searchProduct']) && !empty($_GET['searchProduct'])) {
    $searchProductTerm = $_GET['searchProduct'];
    $searchProductTerm = str_replace("'", "", $searchProductTerm);
    $productGetQuery .= " AND (products.productName LIKE '%$searchProductTerm%' 
                          OR categories.categoryName LIKE '%$searchProductTerm%')";
}

// Category Filter 
if (isset($_GET['categoryID']) && !empty($_GET['categoryID'])) {
    $categoryID = (int) $_GET['categoryID'];
    $productGetQuery .= " AND products.categoryID = $categoryID";
}

// Order
$productGetQuery .= " ORDER BY products.productID DESC";

$productGetResult = executeQuery($productGetQuery);

// Current Category Label 
$currentCategory = "Sort by Category";
if (isset($_GET['categoryID']) && !empty($_GET['categoryID'])) {
    $catID = (int) $_GET['categoryID'];
    $catResult = executeQuery("SELECT categoryName FROM categories WHERE categoryID = $catID LIMIT 1");
    if ($rowCat = mysqli_fetch_assoc($catResult)) {
        $currentCategory = htmlspecialchars($rowCat['categoryName']);
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
                                <?= $currentCategory ?>
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="categoryDropdown">
                                <li><a class="dropdown-item"
                                        href="menu-management.php?<?= !empty($searchProductTerm) ? 'searchProduct=' . urlencode($searchProductTerm) : '' ?>">All</a>
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

                <!-- Menu -->
                <div id="productGrid" class="row g-2 m-3 align-items-center">

                    <!-- Products Loop -->
                    <?php while ($row = mysqli_fetch_assoc($productGetResult)): ?>
                        <div class="col-6 col-md-4 col-lg-2">
                            <div class="menu-item border p-3 rounded shadow-sm text-center h-100 d-flex flex-column">
                                <img src="../assets/product-images/<?= $row['image'] ?>"
                                    alt="<?= htmlspecialchars($row['productName']) ?>" class="img-fluid mb-2 menu-img"
                                    style="max-height:150px; object-fit:contain;">

                                <!-- Product name with ellipsis -->
                                <div class="lead menu-name fs-6 text-truncate" style="max-width: 100%;"
                                    title="<?= htmlspecialchars($row['productName']) ?>">
                                    <?= htmlspecialchars($row['productName']) ?>
                                </div>

                                <div class="d-flex justify-content-center align-items-center gap-2 my-2">
                                    <span class="lead fw-bold menu-price">₱<?= number_format($row['price'], 2) ?></span>
                                </div>

                                <div class="small text-muted mb-2">
                                    <?= htmlspecialchars($row['categoryName']) ?> •
                                    Stock: <?= $row['availableQuantity'] ?> •
                                    <?= $row['isAvailable'] ? "Available" : "Out of Stock" ?>
                                </div>

                                <!-- Buttons pushed to bottom -->
                                <div class="mt-auto">
                                    <div class="d-flex flex-wrap justify-content-center gap-2">
                                        <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                            data-id="<?= $row['productID'] ?>">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>

                                        <form method="POST" class="deleteProductForm">
                                            <input type="hidden" name="productID" value="<?= $row['productID'] ?>">
                                            <input type="hidden" name="btnDeleteProduct" value="1">
                                            <button type="submit" class="btn btn-del">
                                                <i class="bi bi-trash"></i>Delete
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>

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

            // para sa search product
            $(document).ready(function () {
                // kukunin nito lahat ng ingredients galing PHP (ingredients table) which is yung nasa taas
                var ingredients = <?php echo json_encode($ingredients); ?>;

                function initAutocomplete(selector) {
                    $(selector).autocomplete({
                        source: ingredients,  // user gets suggestions habang nagta-type
                        minLength: 1, // kahit 1 char lang lalabas na agad ang list
                        appendTo: "#confirmModal", // para gumana sa loob ng modal
                        select: function (event, ui) {
                            $(this).val(ui.item.label);
                            $(this).siblings(".ingredient-id").val(ui.item.id);
                            return false;

                            //  Without autocomplete: 
                            //  plain text input lang
                            //  pwedeng mali spelling halimbawa "sugarr"
                            //  pwedeng mag-enter ng ingredient na wala sa database
                            //  walang hidden ID  mahirap i-link sa tamang ingredient record
                        },
                        change: function (event, ui) {
                            // kapag walang match sa database
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
                initAutocomplete(".ingredient-search");
                // Add new ingredient row using jquery
                $("#add-ingredient").click(function () {
                    var row = `
                        <div class="row g-2 mb-2 ingredient-row">
                        <div class="col-md-5">
                <input type="text" class="form-control ingredient-search" placeholder="Search Ingredient" required>
                <input type="hidden" name="ingredientID[]" class="ingredient-id">
                     </div>
                     <div class="col-md-3">
                <input type="number" class="form-control" name="requiredQuantity[]" placeholder="Quantity" required>
                     </div>
                 <div class="col-md-3">
                <select class="form-select measurement-select" name="measurementUnit[]" required>
                    <option value="" disabled selected>Select Unit</option>
                    <option value="pcs">pcs</option>
                    <option value="kg">kg</option>
                    <option value="g">g</option>
                    <option value="ml">ml</option>
                    <option value="L">L</option>
                    <option value="oz">oz</option>
                    <option value="pack">pack</option>
                    <option value="box">box</option>
                </select>
                 </div>
                  <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-sm remove-ingredient">&times;</button>
                     </div>
                      </div>`;
                    $("#ingredients-container").append(row);

                    //  autocomplete: bagong row may suggestions pa rin
                    // kapag walang autocomplete: bagong row magiging plain input lang
                    initAutocomplete($("#ingredients-container .ingredient-search").last());
                });
                $(document).on("click", ".remove-ingredient", function () {
                    $(this).closest(".ingredient-row").remove();
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>
</body>

</html>