<?php
include '../assets/connect.php';

if (isset($_POST['status'])) {
    $status = $_POST['status'] == 1 ? '1' : '0';

    $query = "
        INSERT INTO menusettings (settingName, settingValue)
        VALUES ('customer_menu_enabled', '$status')
        ON DUPLICATE KEY UPDATE settingValue='$status', updatedAt=CURRENT_TIMESTAMP
    ";
    executeQuery($query);

    echo "Customer menu status updated to " . $status;
}
?>