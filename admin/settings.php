<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/settings.css">
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
        <h4 class="mobile-header-title">Settings</h4>
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
                <a href="sales-and-report.php" class="admin-nav-link">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Sales & Reports</span>
                </a>
            </div>

            <!-- TOOLS Section -->
            <div class="section-header">Tools</div>
            <div>
                <a href="settings.php" class="admin-nav-link active">
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
            <a href="settings.php" class="admin-nav-link active wow animate__animated animate__fadeInLeft" data-wow-delay="0.4s">
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
        <div class="container-fluid"">
            <div class="card rounded-4 cardMain shadow-sm">
            <!-- Header Row  -->
            <div class="d-none d-md-block align-items-center py-4 px-lg-3 px-2">
                <h4 class="subheading fw-bold m-1 d-flex align-items-center">
                    <span>Settings</span>
                </h4>
            </div>

            <div class="row g-2 align-items-center mb-3 px-2 px-lg-3">

                <div class="col-6 col-sm-auto p-3">
                    <h1 class="subheading">User Role Management</h1>
                </div>
                <div class="col-6 col-sm-auto ms-auto">

                    <button class="btn btn-add w-100" type="button" data-bs-toggle="modal"
                        data-bs-target="#confirmModal">
                        <i class="bi bi-plus"></i> Add User
                    </button>
                </div>
            </div>
            <div class="card rounded-3 m-3 cardMain ">
                <div class="card-body" id="cardBody">
                    <!-- ALL USERS | [USER COUNT php] -->
                    <h5 class="card-title mb-3">All Users | 5</h5>
                    <div class="table-responsive" style="min-height: 20vh; max-height: 40vh; overflow-y: auto;">
                        <table class="table table-bordered">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- sample data -->
                                <tr>
                                    <td>
                                        <h5>Brandon Areej D. Mauricio</h5>
                                        <h6 class="lead fst-italic">email@gmail.com</h6>
                                    </td>
                                    <td>
                                        <div class="dropdown text-center">
                                            <button class="btn btn-dropdown dropdown-toggle fw-semibold"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Role
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Admin</a></li>
                                                <li><a class="dropdown-item" href="#">Staff</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-action mx-auto" id="editUserBtn">
                                            <i class="bi bi-pencil-square"></i> Edit Credentials
                                        </button>
                                        <button class="btn btn-action text-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row align-items-center mb-3 px-2 px-lg-3">
                <div class="col-12 col-sm-auto px-3">
                    <h1 class="subheading">Menu Settings</h1>
                </div>
            </div>
            <div class="card rounded-3 mx-3 mb-4">
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="customerMenuSwitch">
                        <label class="form-check-label" for="switchCheckChecked">Enable Customer Menu</label>
                    </div>
                </div>
            </div>



        </div>
    </div>
    </div>

    <!-- add user modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center modalText">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="full_name"
                                        placeholder="Enter full name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter email">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Enter password">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" name="phone_number"
                                        placeholder="Enter phone number">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username"
                                        placeholder="Enter username">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Confirm password">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-del me-2" data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-add" data-bs-toggle="modal"
                                data-bs-target="#confirmModal">CONFIRM</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const div = document.getElementById('cardBody');
        const originalContent = div.innerHTML;

        function attachListeners() {
            const editBtn = document.getElementById('editUserBtn');
            if (editBtn) {
                editBtn.addEventListener('click', changeContent);
            }

            const deleteBtn = document.querySelector('.btn-delete');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    alert("Delete user function goes here!");
                });
            }
        }

        //card div contents
        function changeContent() {
            div.innerHTML = `
                <div class="row align-items-center mb-3 g-2">
                    <!-- Name + Email -->
                    <div class="col-12 col-md-auto p-3">
                        <h5 class="card-title">
                            Brandon Areej D. Mauricio | <i>email@gmail.com</i>
                        </h5>
                    </div>

                    <!-- Back Button -->
                    <div class="col-12 col-md-auto ms-md-auto p-3">
                        <button class="btn btn-add w-100 w-md-auto" id="backBtn">
                            <i class="bi bi-arrow-left "></i> Back
                        </button>
                    </div>
                </div>
                <div class="card rounded-3 p-3" style="border:none;">
                    <div class="row g-3 position-relative">

                        <!-- Row 1 -->
                        <div class="col-12 col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Change Email Address</label>
                            <input type="email" class="form-control" placeholder="Enter new email">
                        </div>
                        </div>
                        <div class="col-12 col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Old Password</label>
                            <input type="password" class="form-control" placeholder="Enter old password">
                        </div>
                        </div>

                        <!-- Row 2 -->
                        <div class="col-12 col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Change Username</label>
                            <input type="text" class="form-control" placeholder="Enter new username">
                        </div>
                        </div>
                        <div class="col-12 col-md-6">
                        <div class="mb-2">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" placeholder="Enter new password">
                        </div>
                        </div>

                        <!-- Row 3 -->
                        <div class="col-12 col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Change Phone Number</label>
                            <input type="text" class="form-control" placeholder="Enter new phone number">
                        </div>
                        </div>
                        <div class="col-12 col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" placeholder="Confirm new password">
                        </div>
                        </div>
                    <div class="col-12 col-md-6">
                        <button class="btn btn-add text-white px-4" type="submit">Save</button>
                    </div>
                    </div>
    `;
            const backBtn = document.getElementById('backBtn');
            backBtn.addEventListener('click', function () {
                div.innerHTML = originalContent;
                attachListeners();
            });
        }
        attachListeners();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>

</body>

</html>