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

                    <!-- Products -->
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="menu-item border p-3 rounded shadow-sm text-center">
                            <img src="../assets/img/coffee.png" alt="Amerikano" class="img-fluid mb-2 menu-img">
                            <div class="lead menu-name fs-6">Amerikano (S)</div>
                            <div class="d-flex justify-content-center align-items-center gap-2 my-2">
                                <span class="lead fw-bold menu-price">₱140</span>
                            </div>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class=" bi-pencil-square"></i> Edit
                                </button>
                                <button class="btn btn-del">
                                    <i class=" bi-trash"></i>Delete
                            </div>
                        </div>
                    </div>

                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="menu-item border p-3 rounded shadow-sm text-center">
                            <img src="../assets/img/coffee.png" class="img-fluid mb-2 menu-img">
                            <div class="lead menu-name fs-6">Cappuccino (S)</div>
                            <div class="d-flex justify-content-center align-items-center gap-2 my-2">
                                <span class="lead fw-bold menu-price">₱160</span>
                            </div>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editModal">
                                    <i class=" bi-pencil-square"></i> Edit
                                </button>
                                <button class="btn btn-del">
                                    <i class=" bi-trash"></i>Delete
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- MODALLLL FOR PRODUCTTT -->
    <div class="modal fade " id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title modalText" id="confirmModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modalText">
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
                                    <select class="form-select" name="item_group">
                                        <option disabled selected>Select Category</option>
                                        <option value="coffee">Coffee</option>
                                        <option value="tea">Tea</option>
                                        <option value="food">Food</option>
                                        <option value="beverage">Beverage</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="text" class="form-control" name="menu_price" placeholder="Enter price">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Size</label>
                                    <select class="form-select" name="menu_size">
                                        <option value="None" selected>-- None --</option>
                                        <option value="12oz">12oz</option>
                                        <option value="16oz">16oz</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Attachment</label>
                                    <input type="file" class="form-control" name="attachment">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-de btn-del me-2"
                                data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-sm" data-bs-toggle="modal"
                                data-bs-target="#confirmModal">ADD ITEM</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- EDITT MODALLLLL -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title modalText" id="editModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modalText">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Item name</label>
                                    <input type="text" class="form-control" name="item_name" value="Amerikano">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Select Category</label>
                                    <select class="form-select" name="item_group">
                                        <option selected value="coffee">Coffee</option>
                                        <option value="tea">Tea</option>
                                        <option value="food">Food</option>
                                        <option value="beverage">Beverage</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="text" class="form-control" name="menu_price" value="140">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Size</label>
                                    <select class="form-select" name="menu_size">
                                        <option value="None" selected>-- None --</option>
                                        <option value="12oz">12oz</option>
                                        <option value="16oz">16oz</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Attachment</label>
                                    <input type="file" class="form-control" name="attachment">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-sm btn-del me-2"
                                data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-sm ">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>

        let currentEditingCard = null;

        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-warning')) {
                const card = e.target.closest('.menu-item');
                currentEditingCard = card;

                const name = card.querySelector('.menu-name').textContent;
                const price = card.querySelector('.menu-price').textContent.replace('₱', '');
                const size = card.querySelector('.menu-size').textContent;
                const category = 'coffee';

                const editForm = document.querySelector('#editModal form');
                editForm.item_name.value = name;
                editForm.menu_price.value = price;
                editForm.menu_size.value = size;
                editForm.item_group.value = category;
            }
        });

        document.querySelector('#editModal form').addEventListener('submit', function (e) {
            e.preventDefault();

            if (!currentEditingCard) return;

            const name = this.item_name.value.trim();
            const price = this.menu_price.value.trim();
            const size = this.menu_size.value.trim();

            currentEditingCard.querySelector('.menu-name').textContent = name;
            currentEditingCard.querySelector('.menu-price').textContent = `₱${price}`;
            currentEditingCard.querySelector('.menu-size').textContent = size;

            const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            modal.hide();
        });

        // eto ay for adding 
        document.querySelector('#confirmModal form').addEventListener('submit', function (e) {
            e.preventDefault();

            const name = this.item_name.value.trim();
            const category = this.item_group.value;
            const price = this.menu_price.value.trim();
            const size = this.menu_size.value.trim();

            if (!name || !category || !price || !size) {
                alert('Please fill in all required fields.');
                return;
            }

            const productHTML = `
      <div class="col">
        <div class="menu-item border p-3 rounded shadow-sm text-center width-auto">
          <img src="../assets/img/coffee.png" alt="${name}" class="img-fluid mb-2" style="max-height: 150px;">
          <div class="lead menu-name fw-bold">${name}</div>
          <div class="d-flex justify-content-center align-items-center gap-2 my-2">
            <span class="lead fw-bold menu-price">₱${price}</span>
            <span class="lead menu-size">${size}</span>
          </div>
          <div class="d-flex flex-wrap justify-content-center gap-2">
            <button class="btn btn-warning btn-sm rounded-4 flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal" data-bs-target="#editModal">
              <i class="bi bi-pencil-square"></i> Edit
            </button>
            <button class="btn btn-danger btn-sm rounded-4 flex-grow-1 flex-sm-grow-0 delete-btn">
              <i class="bi bi-trash"></i> Delete
            </button>
          </div>
        </div>
      </div>`;

            document.getElementById('productGrid').insertAdjacentHTML('beforeend', productHTML);

            // Re-attach delete event to the new button
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.onclick = function () {
                    const card = this.closest('.col');
                    if (confirm('Are you sure you want to delete this item?')) {
                        card.remove();
                    }
                };
            });

            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();

            // Clear form
            this.reset();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>

</body>

</html>