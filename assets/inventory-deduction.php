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

        // ✅ If it changed to "preparing", update payment status to "Paid"
        if ($status_lc === 'preparing' && $prevStatus !== 'preparing') {
            $updatePayment = "UPDATE payments SET paymentStatus = 'Paid' WHERE orderID = $orderID";
            if (!mysqli_query($conn, $updatePayment)) {
                error_log('Payment update failed for order ' . $orderID . ': ' . mysqli_error($conn));
            }
        }

        // Recheck payment status
        $paymentStatus = null;
        $payRes = mysqli_query($conn, "SELECT paymentStatus FROM payments WHERE orderID = $orderID LIMIT 1");
        if ($payRes && mysqli_num_rows($payRes) > 0) {
            $payRow = mysqli_fetch_assoc($payRes);
            $paymentStatus = strtolower(trim((string)($payRow['paymentStatus'] ?? '')));
        }

        // ✅ Inventory deduction logic
        if ($status_lc === 'completed' && $prevStatus !== 'completed') {
            if ($paymentStatus === 'unpaid') {
                echo "<div class='alert alert-warning'>Order #$orderID is Unpaid — inventory not deducted.</div>";
                error_log("Skipped inventory deduction for order $orderID because paymentStatus='$paymentStatus'");
            } else {
                // Fetch order items and categories
                $orderItemsRes = mysqli_query($conn, "
                    SELECT 
                        oi.productID, 
                        oi.quantity, 
                        oi.sugar,
                        p.categoryID
                    FROM orderitems oi
                    JOIN products p ON oi.productID = p.productID
                    WHERE oi.orderID = $orderID
                ");

                if ($orderItemsRes && mysqli_num_rows($orderItemsRes) > 0) {
                    while ($item = mysqli_fetch_assoc($orderItemsRes)) {
                        $productID = intval($item['productID']);
                        $orderQty  = floatval($item['quantity']);
                        $sugarText = strtolower(trim($item['sugar'] ?? '100% sugar'));
                        $category  = strtolower(trim($item['categoryID'] ?? ''));

                        // Convert "25% sugar" -> 0.25, etc.
                        $sugarMultiplier = 1.0;
                        if (preg_match('/(\d+)%\s*sugar/', $sugarText, $match)) {
                            $sugarMultiplier = floatval($match[1]) / 100;
                        }

                        // Determine which sweetener to scale
                        $sweetenerID = 21; // Default: Sugar
                        if (in_array($category, ['3','5','6'])) {
                            $sweetenerID = 52; // Brown Sugar Syrup
                        }

                        // Get recipe for the product
                        $recipeRes = mysqli_query($conn, "
                            SELECT ingredientID, requiredQuantity
                            FROM productrecipe
                            WHERE productID = $productID
                        ");

                        while ($rec = mysqli_fetch_assoc($recipeRes)) {
                            $ingredientID = intval($rec['ingredientID']);
                            $requiredQty  = floatval($rec['requiredQuantity']);

                            // Apply sugar/syrup multiplier only to correct ingredient
                            if ($ingredientID == $sweetenerID) {
                                $requiredQty *= $sugarMultiplier;
                            }

                            $totalDeduction = $requiredQty * $orderQty;

                            // Deduct from inventory
                            $updateInv = "
                                UPDATE inventory
                                SET quantity = quantity - $totalDeduction
                                WHERE ingredientID = $ingredientID
                            ";
                            if (!mysqli_query($conn, $updateInv)) {
                                error_log("Inventory deduction failed for ingredient $ingredientID (order $orderID): " . mysqli_error($conn));
                            }
                        }
                    }
                    echo "<div class='alert alert-success'>Inventory deducted for order #$orderID (sugar/syrup adjusted).</div>";
                } else {
                    echo "<div class='alert alert-warning'>No order items found for order #$orderID.</div>";
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
