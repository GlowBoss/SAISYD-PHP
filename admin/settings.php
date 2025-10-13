<?php
include('../assets/connect.php');
session_start();

// Prevent unauthorized access
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin') {
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
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $accNumber = $_POST['accNumber'];
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
                INSERT INTO users (fullName, email, username, accNumber, password, role) 
                VALUES ('$fullName', '$email', '$username', '$accNumber', '$password', '$role')";
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
    $accNumber = $_POST['accNumber'];
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
                accNumber='$accNumber',
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

// DELETE USER
if (isset($_POST['btnDeleteUser'])) {
    $id = $_POST['userID'];

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

$totalUsers = mysqli_num_rows($userResult);
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

    <!-- Bootstrap Icons -->
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
        <div class="container-fluid px-3 px-lg-4">

            <!-- Header Section -->
            <div class="header-section">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start mb-4">
                    <div class="text-center text-md-start w-100">
                        <h1 class="page-title pt-lg-4 pt-0">Settings</h1>
                    </div>

                    <div class="stats-cards d-none d-lg-flex">
                        <div class="stat-card">
                            <div class="stat-number" id="totalUsers"><?php echo $totalUsers; ?></div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Stats Cards -->
                <div class="mobile-stats d-lg-none mb-4">
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="stat-card">
                                <div class="stat-number" id="totalUsersMobile"><?php echo $totalUsers; ?></div>
                                <div class="stat-label">Total Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($userToEdit): ?>
                <!-- Edit User Section -->
                <div class="edit-section">
                    <div class="edit-card">
                        <div class="edit-header">
                            <div class="edit-header-content">
                                <h2 class="edit-title">
                                    <i class="bi bi-person-circle me-2"></i><?= htmlspecialchars($userToEdit['fullName']) ?>
                                </h2>
                                <p class="edit-subtitle">
                                    <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($userToEdit['email']) ?>
                                </p>
                            </div>
                            <button class="btn btn-back" type="button" onclick="window.history.back()">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </button>
                        </div>

                        <form method="POST" id="updateUserForm">
                            <input type="hidden" name="userID" value="<?= $userToEdit['userID'] ?>">

                            <div class="form-section">
                                <h5 class="form-section-title">
                                    <i class="bi bi-info-circle me-2"></i>Personal Information
                                </h5>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Full Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text form-icon"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control"
                                                value="<?= htmlspecialchars($userToEdit['fullName']) ?>" disabled>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text form-icon"><i class="bi bi-at"></i></span>
                                            <input type="text" class="form-control" name="username"
                                                value="<?= htmlspecialchars($userToEdit['username']) ?>"
                                                placeholder="Enter username" required>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text form-icon"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control" name="email"
                                                value="<?= htmlspecialchars($userToEdit['email']) ?>"
                                                placeholder="Enter email address" required>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text form-icon"><i class="bi bi-telephone"></i></span>
                                            <input type="text" class="form-control" name="accNumber"
                                                value="<?= htmlspecialchars($userToEdit['accNumber']) ?>"
                                                placeholder="Enter phone number" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h5 class="form-section-title">
                                    <i class="bi bi-lock me-2"></i>Security
                                </h5>
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Current Password</label>
                                        <div class="input-group position-relative">
                                            <span class="input-group-text form-icon"><i class="bi bi-key"></i></span>
                                            <input type="password" class="form-control pe-5" id="oldPassword"
                                                name="old_password" value="<?= htmlspecialchars($userToEdit['password']) ?>"
                                                placeholder="Current password" readonly title="Password display only">
                                            <span class="password-toggle"
                                                onclick="togglePassword('oldPassword','oldPasswordIcon')">
                                                <i class="bi bi-eye" id="oldPasswordIcon"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">New Password</label>
                                        <div class="input-group position-relative">
                                            <span class="input-group-text form-icon"><i class="bi bi-key"></i></span>
                                            <input type="password" class="form-control pe-5" id="newPassword"
                                                name="new_password" placeholder="Leave blank to keep current password">
                                            <span class="password-toggle"
                                                onclick="togglePassword('newPassword','newPasswordIcon')">
                                                <i class="bi bi-eye" id="newPasswordIcon"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label">Confirm New Password</label>
                                        <div class="input-group position-relative">
                                            <span class="input-group-text form-icon"><i class="bi bi-key"></i></span>
                                            <input type="password" class="form-control pe-5" id="confirmPassword"
                                                name="confirm_password" placeholder="Confirm new password">
                                            <span class="password-toggle"
                                                onclick="togglePassword('confirmPassword','confirmPasswordIcon')">
                                                <i class="bi bi-eye" id="confirmPasswordIcon"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button class="btn btn-save" type="submit" name="btnUpdateUser">
                                    <i class="bi bi-check-circle me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <!-- User Management Section -->
                <div class="action-bar mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-lg-8">
                            <h2 class="section-title">
                                <i class="bi bi-people-fill me-2"></i>User Management
                            </h2>
                        </div>
                        <div class="col-12 col-lg-4">
                            <button class="btn btn-add w-100" type="button" data-bs-toggle="modal"
                                data-bs-target="#confirmModal">
                                <i class="bi bi-plus-circle me-1"></i>Add New User
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="users-table-container">
                    <div class="table-responsive">
                        <table class="table users-table" id="usersTable">
                            <thead class="table-header">
                                <tr>
                                    <th scope="col">
                                        <div class="th-content">
                                            <span>User Information</span>
                                        </div>
                                    </th>
                                    <th scope="col">
                                        <div class="th-content">
                                            <span>Contact</span>
                                        </div>
                                    </th>
                                    <th scope="col" class="actions-col">
                                        <div class="th-content">
                                            <span>Actions</span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="table-body">
                                <?php if (mysqli_num_rows($userResult) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($userResult)): ?>
                                        <tr>
                                            <!-- User Info -->
                                            <td>
                                                <div class="user-info-cell">
                                                    <div class="user-avatar">
                                                        <i class="bi bi-person-fill"></i>
                                                    </div>
                                                    <div class="user-details">
                                                        <h6 class="user-name">
                                                            <?= htmlspecialchars($row['username']); ?>
                                                            <?php if ($row['userID'] == $_SESSION['userID']): ?>
                                                                <span class="badge badge-you">You</span>
                                                            <?php endif; ?>
                                                        </h6>
                                                        <p class="user-fullname">
                                                            <?= htmlspecialchars($row['fullName']); ?>
                                                        </p>
                                                        <span class="role-badge">
                                                            <i
                                                                class="bi bi-shield-check me-1"></i><?= htmlspecialchars($row['role']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Contact Info -->
                                            <td>
                                                <div class="contact-info">
                                                    <p class="contact-item">
                                                        <i class="bi bi-envelope me-2"></i>
                                                        <span class="contact-text"><?= htmlspecialchars($row['email']); ?></span>
                                                    </p>
                                                    <p class="contact-item">
                                                        <i class="bi bi-telephone me-2"></i>
                                                        <span
                                                            class="contact-text"><?= htmlspecialchars($row['accNumber']); ?></span>
                                                    </p>
                                                </div>
                                            </td>

                                            <!-- Actions -->
                                            <td class="actions-cell">
                                                <div class="action-buttons">
                                                    <form method="GET" style="display: inline;">
                                                        <input type="hidden" name="editUser" value="<?= $row['userID']; ?>">
                                                        <button class="btn action-btn edit-btn" type="submit" title="Edit User">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" class="deleteUserForm" style="display: inline;">
                                                        <input type="hidden" name="userID" value="<?= $row['userID']; ?>">
                                                        <input type="hidden" name="btnDeleteUser" value="1">
                                                        <button type="submit" class="btn action-btn delete-btn" title="Delete User">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="no-records">
                                            <div class="no-records-content">
                                                <i class="bi bi-inbox"></i>
                                                <p>No users found</p>
                                                <button class="btn btn-add" data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal">
                                                    <i class="bi bi-plus-circle"></i> Add First User
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Menu Settings Section -->
                <div class="menu-settings-section">
                    <div class="action-bar mb-4">
                        <h2 class="section-title">
                            <i class="bi bi-menu-button-wide me-2"></i>Menu Configuration
                        </h2>
                    </div>

                    <div class="row g-3">
                        <!-- Customer Menu Toggle Card -->
                        <div class="col-12 col-md-6">
                            <div class="settings-card">
                                <div class="settings-card-header">
                                    <div class="settings-card-title">
                                        <i class="bi bi-menu-button-wide"></i>
                                        <span>Customer Menu</span>
                                    </div>
                                </div>
                                <div class="settings-card-body">
                                    <p class="settings-description">Enable or disable customer access to the online menu</p>
                                    <div class="toggle-wrapper">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="customerMenuSwitch" <?php
                                                $settingResult = executeQuery("SELECT settingValue FROM menusettings WHERE settingName='customer_menu_enabled'");
                                                $row = mysqli_fetch_assoc($settingResult);
                                                if ($row && $row['settingValue'] == '1')
                                                    echo "checked";
                                                ?>>
                                            <label class="form-check-label" for="customerMenuSwitch">
                                                <span id="toggleStatus">
                                                    <?php echo ($row && $row['settingValue'] == '1') ? 'Enabled' : 'Disabled'; ?>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- QR Code Card -->
                        <div class="col-12 col-md-6">
                            <div class="settings-card">
                                <div class="settings-card-header">
                                    <div class="settings-card-title">
                                        <i class="bi bi-qr-code"></i>
                                        <span>QR Code</span>
                                    </div>
                                </div>
                                <div class="settings-card-body">
                                    <p class="settings-description">Display customer menu QR code for easy access</p>
                                    <button class="btn btn-qr w-100" type="button" data-bs-toggle="modal"
                                        data-bs-target="#qrModal">
                                        <i class="bi bi-qr-code me-2"></i>Show QR Code
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <!-- Modals -->
    <?php include '../modal/add-user-modal.php'; ?>
    <?php include '../modal/qr-modal.php'; ?>
    <?php include '../modal/confirm-toggle-modal.php'; ?>

    <!-- Toast Notifications -->
    <?php if (isset($_SESSION['alertMessage'])): ?>
        <?php
        $isSuccess = $_SESSION['alertType'] === 'success';
        $icon = $isSuccess ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
        $iconColor = $isSuccess ? 'var(--accent-color)' : '#e74c3c';
        ?>

        <div id="alertToast" class="toast align-items-center border-0 fade show position-fixed top-0 end-0 m-3" role="alert"
            aria-live="assertive" aria-atomic="true" data-bs-delay="2000" data-bs-autohide="true"
            style="background-color: var(--text-color-dark); color: var(--text-color-light); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.25); z-index: 9999;">
            <div class="d-flex align-items-center">
                <i class="<?= $icon ?> ms-3" style="font-size: 1.2rem; color: <?= $iconColor ?>;"></i>
                <div class="toast-body" style="font-family: var(--secondaryFont);">
                    <?= htmlspecialchars($_SESSION['alertMessage']) ?>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"
                    style="filter: invert(1);"></button>
            </div>
        </div>

        <?php
        unset($_SESSION['alertMessage']);
        unset($_SESSION['alertType']);
        ?>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
    <script src="../assets/js/admin_sidebar.js"></script>

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

        // Function to show toast dynamically
        function showToast(message, type = 'success') {
            const existingToast = document.querySelector('.toast');
            if (existingToast) {
                existingToast.remove();
            }

            const isSuccess = type === 'success';
            const icon = isSuccess ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
            const iconColor = isSuccess ? 'var(--accent-color)' : '#e74c3c';

            const toastHTML = `
        <div id="dynamicToast" class="toast align-items-center border-0 fade show position-fixed top-0 end-0 m-3" 
             role="alert" aria-live="assertive" aria-atomic="true" 
             data-bs-delay="3000" data-bs-autohide="true"
             style="background-color: var(--text-color-dark); color: var(--text-color-light); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.25); z-index: 9999;">
            <div class="d-flex align-items-center">
                <i class="${icon} ms-3" style="font-size: 1.2rem; color: ${iconColor};"></i>
                <div class="toast-body" style="font-family: var(--secondaryFont);">
                    ${message}
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"
                        style="filter: invert(1);"></button>
            </div>
        </div>
    `;

            document.body.insertAdjacentHTML('beforeend', toastHTML);

            const toastElement = document.getElementById('dynamicToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();

            toastElement.addEventListener('hidden.bs.toast', function () {
                toastElement.remove();
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Initialize WOW animations
            if (typeof WOW !== 'undefined') {
                new WOW().init();
            }

            // Initialize and show toast if exists
            const toastElement = document.getElementById('alertToast');
            if (toastElement) {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }

            // Delete User Confirmation
            document.querySelectorAll('.deleteUserForm').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '<span style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px; font-weight: bold;">Confirm Delete</span>',
                        html: `
                        <div style="text-align: center; padding: 1rem 0;">
                            <i class="bi bi-exclamation-triangle-fill" style="font-size: 4rem; color: #dc3545; display: block; margin-bottom: 1.5rem;"></i>
                            <p style="font-family: var(--secondaryFont); color: var(--text-color-dark); font-size: 1.1rem; margin-bottom: 0.5rem; line-height: 1.5;">
                                Are you sure you want to delete this user?
                            </p>
                            <p style="color: #dc3545; font-family: var(--secondaryFont); font-weight: 600; margin-top: 1rem; margin-bottom: 0; font-size: 0.95rem;">
                                This action cannot be undone!
                            </p>
                        </div>
                    `,
                        icon: false,
                        showCancelButton: true,
                        confirmButtonText: '<i class="bi bi-trash-fill" style="margin-right: 8px;"></i>DELETE',
                        cancelButtonText: 'CANCEL',
                        reverseButtons: true,
                        width: '450px',
                        padding: '2rem 1.5rem 1.5rem',
                        background: 'var(--bg-color)',
                        customClass: {
                            popup: 'swal2-border-radius',
                            confirmButton: 'swal2-confirm-radius',
                            cancelButton: 'swal2-cancel-radius',
                            actions: 'swal-actions-custom'
                        },
                        buttonsStyling: false,
                        didOpen: () => {
                            // Add custom styles to actions container
                            const actionsContainer = document.querySelector('.swal-actions-custom');
                            if (actionsContainer) {
                                actionsContainer.style.cssText = `
                                display: flex !important;
                                gap: 12px !important;
                                justify-content: center !important;
                                width: 100% !important;
                                margin-top: 1.5rem !important;
                            `;
                            }

                            // Style cancel button
                            const cancelBtn = Swal.getCancelButton();
                            cancelBtn.style.cssText = `
                            background: var(--card-bg-color);
                            color: var(--text-color-dark);
                            border: 2px solid var(--primary-color);
                            border-radius: 10px;
                            font-family: var(--primaryFont);
                            letter-spacing: 1px;
                            font-weight: bold;
                            padding: 10px 24px;
                            transition: all 0.3s ease;
                            min-width: 120px;
                            font-size: 0.95rem;
                            cursor: pointer;
                        `;
                            cancelBtn.onmouseover = () => {
                                cancelBtn.style.background = 'var(--primary-color)';
                                cancelBtn.style.color = 'var(--text-color-light)';
                                cancelBtn.style.transform = 'translateY(-2px)';
                            };
                            cancelBtn.onmouseout = () => {
                                cancelBtn.style.background = 'var(--card-bg-color)';
                                cancelBtn.style.color = 'var(--text-color-dark)';
                                cancelBtn.style.transform = 'translateY(0)';
                            };

                            // Style confirm button
                            const confirmBtn = Swal.getConfirmButton();
                            confirmBtn.style.cssText = `
                            background: #dc3545;
                            color: white;
                            border: none;
                            border-radius: 10px;
                            font-family: var(--primaryFont);
                            letter-spacing: 1px;
                            font-weight: bold;
                            padding: 10px 24px;
                            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
                            transition: all 0.3s ease;
                            min-width: 120px;
                            font-size: 0.95rem;
                            cursor: pointer;
                        `;
                            confirmBtn.onmouseover = () => {
                                confirmBtn.style.background = '#b02a37';
                                confirmBtn.style.transform = 'translateY(-2px)';
                                confirmBtn.style.boxShadow = '0 6px 12px rgba(220, 53, 69, 0.4)';
                            };
                            confirmBtn.onmouseout = () => {
                                confirmBtn.style.background = '#dc3545';
                                confirmBtn.style.transform = 'translateY(0)';
                                confirmBtn.style.boxShadow = '0 4px 8px rgba(220, 53, 69, 0.3)';
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Customer Menu Toggle with Confirmation
            const switchInput = document.getElementById('customerMenuSwitch');
            if (switchInput) {
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmToggleModal'));
                const confirmBtn = document.getElementById('confirmToggle');
                const cancelBtn = document.getElementById('cancelToggle');
                const confirmText = document.getElementById('confirmToggleText');

                let intendedState = switchInput.checked;

                switchInput.addEventListener('change', function (e) {
                    e.preventDefault();
                    intendedState = switchInput.checked;
                    switchInput.checked = !intendedState;

                    confirmText.textContent = intendedState
                        ? "Are you sure you want to ENABLE the Customer Menu?"
                        : "Are you sure you want to DISABLE the Customer Menu?";

                    confirmModal.show();
                });

                confirmBtn.addEventListener('click', function () {
                    switchInput.checked = intendedState;

                    // Update the toggle status text
                    const toggleStatus = document.getElementById('toggleStatus');
                    if (toggleStatus) {
                        toggleStatus.textContent = intendedState ? 'Enabled' : 'Disabled';
                    }

                    let status = intendedState ? 1 : 0;
                    fetch('toggle-config.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'status=' + status
                    })
                        .then(res => res.text())
                        .then(data => {
                            console.log(data);
                            showToast('Customer Menu ' + (intendedState ? 'enabled' : 'disabled') + ' successfully!', 'success');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('An error occurred. Please try again.', 'error');
                        });

                    confirmModal.hide();
                });

                cancelBtn.addEventListener('click', function () {
                    switchInput.checked = !intendedState;
                });
            }
        });
    </script>

</body>

</html>