<?php
include 'connect.php';
session_start();

// Handle order status update
if (isset($_POST['update_status'])) {
    $orderID     = intval($_POST['orderID']);
    $status_raw  = mysqli_real_escape_string($conn, $_POST['status']);
    $status_lc   = strtolower($status_raw);

    // Get previous status
    $prevStatus = '';
    $res = mysqli_query($conn, "SELECT status FROM orders WHERE orderID = $orderID LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $prevStatus = strtolower($row['status'] ?? '');
    }

    // Update order status
    $updateQuery = "UPDATE orders SET status='$status_raw' WHERE orderID=$orderID";
    if (mysqli_query($conn, $updateQuery)) {

        // Get payment status
        $paymentStatus = null;
        $payRes = mysqli_query($conn, "SELECT paymentStatus FROM payments WHERE orderID = $orderID LIMIT 1");
        if ($payRes && mysqli_num_rows($payRes) > 0) {
            $payRow = mysqli_fetch_assoc($payRes);
            $paymentStatus = strtolower(trim((string)($payRow['paymentStatus'] ?? '')));
        }

        // Deduct inventory only if paymentStatus is NOT unpaid
        if ($status_lc === 'completed' && $prevStatus !== 'completed') {
            if ($paymentStatus === 'unpaid') {
                echo "<div class='alert alert-warning'>Order #$orderID is Unpaid — inventory not deducted.</div>";
                error_log("Skipped inventory deduction for order $orderID because paymentStatus='$paymentStatus'");
            } else {
                $deductQuery = "
                    UPDATE inventory i
                    JOIN productrecipe pr ON i.ingredientID = pr.ingredientID
                    JOIN orderitems oi ON pr.productID = oi.productID
                    SET i.quantity = i.quantity - (pr.requiredQuantity * oi.quantity)
                    WHERE oi.orderID = $orderID
                ";
                if (!mysqli_query($conn, $deductQuery)) {
                    error_log('Inventory deduction failed for order ' . $orderID . ': ' . mysqli_error($conn));
                    echo "<div class='alert alert-danger'>Inventory deduction failed for order #$orderID.</div>";
                } else {
                    echo "<div class='alert alert-success'>Inventory deducted for order #$orderID.</div>";
                }
            }
        }

        echo "<div class='alert alert-success'>Order #$orderID status updated to $status_raw</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
    exit;
}
?>
