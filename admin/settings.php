<?php
include('../assets/connect.php');
session_start();

// Prevent unauthorized access
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin') {
    // Redirect non-admin users to login page or a "no access" page
    header("Location: login.php");
    exit();
}


$userToEdit = null;

// EDIT USER FETCH
if (isset($_GET['editUser'])) {
    $userID = $_GET['editUser'];
    $userToEdit = mysqli_fetch_assoc(executeQuery("SELECT * FROM users WHERE userID=$userID"));
}

// ADD USER
if (isset($_POST['btnAddUser'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phonenumber = $_POST['phonenumber'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    if ($password !== $confirm_password) {
        $_SESSION['alertMessage'] = "Passwords do not match.";
        $_SESSION['alertType'] = "error";
    } else {
        $checkQuery = "SELECT * FROM users WHERE email='$email' OR username='$username' LIMIT 1";
        $checkResult = executeQuery($checkQuery);

        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            $_SESSION['alertMessage'] = "Email or username already exists.";
            $_SESSION['alertType'] = "error";
        } else {
            $insertQuery = "
                INSERT INTO users (fullname, email, username, phonenumber, password, role) 
                VALUES ('$fullname', '$email', '$username', '$phonenumber', '$password', '$role')";
            executeQuery($insertQuery);
            $_SESSION['alertMessage'] = "User added successfully!";
            $_SESSION['alertType'] = "success";
        }
    }
    header("Location: settings.php");
    exit();
}

// UPDATE USER
if (isset($_POST['btnUpdateUser'])) {
    $id = $_POST['userID'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phonenumber = $_POST['phonenumber'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $currentUser = mysqli_fetch_assoc(executeQuery("SELECT password FROM users WHERE userID=$id"));
    $currentPassword = $currentUser['password'];

    if (!empty($old_password) && $old_password !== $currentPassword) {
        $_SESSION['alertMessage'] = "Old password is incorrect.";
        $_SESSION['alertType'] = "error";
    } else if (!empty($new_password) && $new_password !== $confirm_password) {
        $_SESSION['alertMessage'] = "New password and confirm password do not match.";
        $_SESSION['alertType'] = "error";
    } else {
        $finalPassword = !empty($new_password) ? $new_password : $currentPassword;

        $checkQuery = "SELECT * FROM users WHERE email='$email' AND userID!=$id LIMIT 1";
        $checkResult = executeQuery($checkQuery);

        if ($checkResult && mysqli_num_rows($checkResult) > 0) {
            $_SESSION['alertMessage'] = "Email already exists for another user.";
            $_SESSION['alertType'] = "error";
        } else {
            $updateQuery = "
                UPDATE users SET
                email='$email',
                username='$username',
                phonenumber='$phonenumber',
                password='$finalPassword'
                WHERE userID=$id";
            executeQuery($updateQuery);
            $_SESSION['alertMessage'] = "User updated successfully!";
            $_SESSION['alertType'] = "success";
        }
    }
    header("Location: settings.php");
    exit();
}

if (isset($_POST['btnDeleteUser'])) {
    $id = $_POST['userID'];

    // Prevent deleting current logged-in user
    if ($id == $_SESSION['userID']) {
        $_SESSION['alertMessage'] = "You cannot delete your own account.";
        $_SESSION['alertType'] = "error";
    } else {
        $deleteQuery = "DELETE FROM users WHERE userID=$id";
        executeQuery($deleteQuery);
        $_SESSION['alertMessage'] = "User deleted successfully!";
        $_SESSION['alertType'] = "success";
    }

    header("Location: settings.php");
    exit();
}
// CHANGE ROLE
if (isset($_POST['userID']) && isset($_POST['role'])) {
    $id = $_POST['userID'];
    $role = $_POST['role'];

    // Prevent changing your own role
    if ($id == $_SESSION['userID']) {
        $_SESSION['alertMessage'] = "You cannot change your own role.";
        $_SESSION['alertType'] = "error";
    } else {
        $updateQuery = "UPDATE users SET role='$role' WHERE userID='$id'";
        mysqli_query($conn, $updateQuery) or die("Error: " . mysqli_error($conn));
        $_SESSION['alertMessage'] = "User role updated successfully!";
        $_SESSION['alertType'] = "success";
    }

    header("Location: settings.php");
    exit();
}

$userResult = executeQuery("
    SELECT * FROM users 
    ORDER BY (userID = {$_SESSION['userID']}) DESC, userID DESC
");
?>




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
            <a href="settings.php" class="admin-nav-link active wow animate__animated animate__fadeInLeft"
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
                        <!-- add button -->
                        <button class="btn btn-add w-100" type="button" data-bs-toggle="modal"
                            data-bs-target="#confirmModal">
                            <i class="bi bi-plus"></i> Add User
                        </button>


                    </div>
                </div>


                <?php if ($userToEdit): ?>
                    <form method="POST" id="updateUserForm">
                        <input type="hidden" name="userID" value="<?= $userToEdit['userID'] ?>">

                        <div class="row align-items-center mb-3 g-2">
                            <!-- Name + Email -->
                            <div class="col-12 col-md-auto p-3">
                                <h5 class="card-title">
                                    <?= $userToEdit['fullname'] ?> | <i><?= $userToEdit['email'] ?></i>
                                </h5>
                            </div>

                            <!-- Back Button -->
                            <div class="col-12 col-md-auto ms-md-auto p-3">
                                <button class="btn btn-add w-100 w-md-auto" type="button" onclick="window.history.back()">
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
                                        <input type="email" class="form-control" name="email"
                                            value="<?= $userToEdit['email'] ?>" placeholder="Enter new email" required>
                                    </div>
                                </div>
                                <!-- Old Password -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Old Password</label>
                                    <div class="mb-2 position-relative">

                                        <input type="password" class="form-control pe-5" id="oldPassword"
                                            name="old_password" value="<?= htmlspecialchars($userToEdit['password']) ?>"
                                            placeholder="Enter old password" readonly
                                            title="Password cannot be typed directly">


                                        <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                                            style="cursor:pointer;"
                                            onclick="togglePassword('oldPassword','oldPasswordIcon')">
                                            <i class="bi bi-eye" id="oldPasswordIcon"></i>
                                        </span>
                                    </div>
                                </div>


                                <!-- Row 2 -->
                                <div class="col-12 col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label">Change Username</label>
                                        <input type="text" class="form-control" name="username"
                                            value="<?= $userToEdit['username'] ?>" placeholder="Enter new username"
                                            required>
                                    </div>
                                </div>
                                <!-- New Password -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label">New Password</label>
                                    <div class="mb-2 position-relative">

                                        <input type="password" class="form-control pe-5" id="newPassword"
                                            name="new_password" placeholder="Enter new password">
                                        <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                                            style="cursor:pointer;"
                                            onclick="togglePassword('newPassword','newPasswordIcon')">
                                            <i class="bi bi-eye" id="newPasswordIcon"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Row 3 -->
                                <div class="col-12 col-md-6">
                                    <div class="mb-2">
                                        <label class="form-label">Change Phone Number</label>
                                        <input type="text" class="form-control" name="phonenumber"
                                            value="<?= $userToEdit['phonenumber'] ?>" placeholder="Enter new phone number"
                                            required>
                                    </div>
                                </div>
                                <!-- Confirm Password -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Confirm New Password</label>
                                    <div class="mb-2 position-relative">

                                        <input type="password" class="form-control pe-5" id="confirmPassword"
                                            name="confirm_password" placeholder="Confirm new password">
                                        <span class="position-absolute top-50 end-0 translate-middle-y me-3"
                                            style="cursor:pointer;"
                                            onclick="togglePassword('confirmPassword','confirmPasswordIcon')">
                                            <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <button class="btn btn-add text-white px-4" type="submit"
                                        name="btnUpdateUser">Save</button>
                                </div>

                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="m-3 ">
                        <div class="card-body" id="cardBody">
                            <h5 class="card-title mb-3">All Users | <?= mysqli_num_rows($userResult) ?></h5>

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
                                        <?php if (mysqli_num_rows($userResult) > 0): ?>
                                            <?php while ($row = mysqli_fetch_assoc($userResult)): ?>
                                                <tr>
                                                    <td>
                                                        <h5>
                                                            <?= htmlspecialchars($row['username']); ?>
                                                            <?= $row['userID'] == $_SESSION['userID'] ? '<span class="badge ms-2 fs-6 py-1" style="background-color: var(--primary-color)">You</span>' : '' ?>
                                                        </h5>

                                                        <h6 class="lead fst-italic d-flex align-items-center">
                                                            <i class="bi bi-person-fill me-2 fw-bold"></i>
                                                            <?= htmlspecialchars($row['fullname']); ?>
                                                        </h6>

                                                        <h6 class="lead fst-italic d-flex align-items-center">
                                                            <i class="bi bi-envelope-fill me-2 fw-bold"></i>
                                                            <?= htmlspecialchars($row['email']); ?>
                                                        </h6>

                                                        <h6 class="lead fst-italic d-flex align-items-center">
                                                            <i class="bi bi-telephone-fill me-2 fw-bold"></i>
                                                            <?= htmlspecialchars($row['phonenumber']); ?>
                                                        </h6>

                                                    </td>
                                                    <td>
                                                        <div
                                                            class="dropdown text-center d-flex justify-content-center align-items-center">
                                                            <button class="btn btn-dropdown dropdown-toggle fw-semibold"
                                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                                <?= $row['userID'] == $_SESSION['userID'] ? 'disabled title="Cannot change your own role"' : '' ?>>
                                                                <?= htmlspecialchars($row['role']); ?>
                                                            </button>

                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <form method="POST" style="margin:0;">
                                                                        <input type="hidden" name="userID"
                                                                            value="<?= $row['userID']; ?>">
                                                                        <input type="hidden" name="role" value="Admin">
                                                                        <button type="submit" class="dropdown-item">Admin</button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form method="POST" style="margin:0;">
                                                                        <input type="hidden" name="userID"
                                                                            value="<?= $row['userID']; ?>">
                                                                        <input type="hidden" name="role" value="Staff">
                                                                        <button type="submit" class="dropdown-item">Staff</button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <form method="GET" style="display:inline;">
                                                            <input type="hidden" name="editUser" value="<?= $row['userID']; ?>">
                                                            <button class="btn btn-action" type="submit">
                                                                <i class="bi bi-pencil-square"></i> Edit Credentials
                                                            </button>
                                                        </form>
                                                        <form method="POST" class="deleteUserForm">
                                                            <input type="hidden" name="userID" value="<?= $row['userID']; ?>">
                                                            <input type="hidden" name="btnDeleteUser" value="1">
                                                            <button type="submit" class="btn btn-delete btn-action-del">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center">No users found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>





                <hr>

                <div class="row align-items-center mb-3 px-2 px-lg-3">
                    <div class="col-12 col-sm-auto px-3">
                        <h1 class="subheading">Menu Settings</h1>
                    </div>
                </div>
                <div class="card rounded-3 mx-3 mb-4">
                    <div class="card-body">
                        <!-- Toggle Switch -->
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="customerMenuSwitch" <?php
                            $settingResult = executeQuery("SELECT settingValue FROM menusettings WHERE settingName='customer_menu_enabled'");
                            $row = mysqli_fetch_assoc($settingResult);
                            if ($row && $row['settingValue'] == '1')
                                echo "checked";
                            ?>>
                            <label class="form-check-label" for="customerMenuSwitch">Enable Customer Menu</label>
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
                    <form id="addUserForm" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="fullname"
                                        placeholder="Enter full name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter email"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password"
                                        placeholder="Enter password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" name="phonenumber"
                                        placeholder="Enter phone number">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" placeholder="Enter username"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="confirm_password"
                                        placeholder="Confirm password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-control custom-select" name="role" required>
                                        <option value="" disabled selected>Select role</option>
                                        <option class="option" value="Admin">Admin</option>
                                        <option class="option" value="Staff">Staff</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-del me-2" data-bs-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-add" name="btnAddUser">CONFIRM</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <?php include '../modal/confirm-toggle-modal.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle password visibility
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {

            // Delete User Confirmation
            document.querySelectorAll('.deleteUserForm').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This user will be deleted!",
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
        });

        document.addEventListener('DOMContentLoaded', function () {
            <?php if (isset($_SESSION['alertMessage'])): ?>
                Swal.fire({
                    icon: '<?= $_SESSION['alertType'] ?>',
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

        document.getElementById('customerMenuSwitch').addEventListener('change', function () {
            let status = this.checked ? 1 : 0;

            fetch('toggle-config.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'status=' + status
            })
                .then(res => res.text())
                .then(data => console.log(data));
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const switchInput = document.getElementById('customerMenuSwitch');
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmToggleModal'));
            const confirmBtn = document.getElementById('confirmToggle');
            const cancelBtn = document.getElementById('cancelToggle');
            const confirmText = document.getElementById('confirmToggleText');

            let intendedState = switchInput.checked; // current state

            switchInput.addEventListener('change', function (e) {
                e.preventDefault(); // stop instant toggle
                intendedState = switchInput.checked;
                switchInput.checked = !intendedState; // revert until confirmed

                confirmText.textContent = intendedState
                    ? "Are you sure you want to ENABLE the Customer Menu?"
                    : "Are you sure you want to DISABLE the Customer Menu?";

                confirmModal.show();
            });

            confirmBtn.addEventListener('click', function () {
                switchInput.checked = intendedState;

                let status = intendedState ? 1 : 0;
                fetch('toggle-config.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'status=' + status
                })
                    .then(res => res.text())
                    .then(data => console.log(data));

                confirmModal.hide();
            });

            cancelBtn.addEventListener('click', function () {
                switchInput.checked = !intendedState;
            });
        });
    </script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
        </script>


</body>

</html>