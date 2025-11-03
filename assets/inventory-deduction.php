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

        // ✅ If it changed to "preparing", update payment status to "Paid"
        if ($status_lc === 'preparing' && $prevStatus !== 'preparing') {
            $updatePayment = "UPDATE payments SET paymentStatus = 'Paid' WHERE orderID = $orderID";
            if (!mysqli_query($conn, $updatePayment)) {
                error_log('Payment update failed for order ' . $orderID . ': ' . mysqli_error($conn));
            }
        }

        // If it changed to completed then deduct inventory stocks
        if ($status_lc === 'completed' && $prevStatus !== 'completed') {
            $deductQuery = "
                UPDATE inventory i
                JOIN productrecipe pr ON i.ingredientID = pr.ingredientID
                JOIN orderitems oi ON pr.productID = oi.productID
                SET i.quantity = i.quantity - (pr.requiredQuantity * oi.quantity)
                WHERE oi.orderID = $orderID
            ";
            if (!mysqli_query($conn, $deductQuery)) {
                // Log deduction failure
                error_log('Inventory deduction failed for order ' . $orderID . ': ' . mysqli_error($conn));
            }
        }

        echo "<div class='alert alert-success'>Order #$orderID status updated to $status_raw</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
    exit;
}
?>
