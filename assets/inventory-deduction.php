<?php
include 'connect.php';
session_start();

/**
 * Convert recipe unit to inventory unit for proper deduction
 * 
 * @param float $qty - Required quantity from recipe
 * @param string $fromUnit - Recipe measurement unit (e.g., 'g', 'ml', 'pump')
 * @param string $toUnit - Inventory unit (e.g., 'kg', 'L', 'pcs')
 * @return float - Converted quantity in inventory units
 */
function convertToInventoryUnit($qty, $fromUnit, $toUnit) {
    $fromUnit = strtolower(trim($fromUnit));
    $toUnit = strtolower(trim($toUnit));
    
    // Same unit = no conversion
    if ($fromUnit === $toUnit) {
        return $qty;
    }
    
    // ==========================================
    // WEIGHT CONVERSIONS
    // ==========================================
    // KG inventory can be deducted by: kg, g
    if ($toUnit === 'kg') {
        if ($fromUnit === 'kg') return $qty;
        if ($fromUnit === 'g') return $qty / 1000;
    }
    
    // G inventory can be deducted by: g, kg
    if ($toUnit === 'g') {
        if ($fromUnit === 'g') return $qty;
        if ($fromUnit === 'kg') return $qty * 1000;
    }
    
    // ==========================================
    // VOLUME CONVERSIONS
    // ==========================================
    // Conversion factors to milliliters
    $toMilliliters = [
        'l' => 1000,
        'ml' => 1,
        'pump' => 10,
        'tbsp' => 15,
        'tsp' => 5,
        'shot' => 30,
        'cup' => 240
    ];
    
    // L inventory can be deducted by: L, ml, pump, tbsp, tsp, shot, cup
    if ($toUnit === 'l') {
        if (isset($toMilliliters[$fromUnit])) {
            $inMl = $qty * $toMilliliters[$fromUnit];
            return $inMl / 1000; // Convert ml to L
        }
    }
    
    // ML inventory can be deducted by: ml, pump, tbsp, tsp, shot, cup
    if ($toUnit === 'ml') {
        if (isset($toMilliliters[$fromUnit])) {
            return $qty * $toMilliliters[$fromUnit]; // Convert to ml
        }
    }
    
    // ==========================================
    // PIECES CONVERSIONS
    // ==========================================
    // PCS inventory can only be deducted by: pcs
    if ($toUnit === 'pcs' && $fromUnit === 'pcs') {
        return $qty;
    }
    
    // ==========================================
    // FALLBACK: No valid conversion found
    // ==========================================
    error_log("WARNING: No conversion found from '$fromUnit' to '$toUnit' - using original quantity (may cause inventory errors)");
    return $qty;
}

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

        // ✅ Inventory deduction logic (FIXED WITH UNIT CONVERSION)
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

                        //  Get recipe WITH UNITS
                        $recipeRes = mysqli_query($conn, "
                            SELECT 
                                pr.ingredientID, 
                                pr.requiredQuantity,
                                pr.measurementUnit,
                                inv.unit as inventoryUnit
                            FROM productrecipe pr
                            JOIN (
                                SELECT ingredientID, MAX(unit) as unit
                                FROM inventory
                                GROUP BY ingredientID
                            ) inv ON pr.ingredientID = inv.ingredientID
                            WHERE pr.productID = $productID
                        ");

                        while ($rec = mysqli_fetch_assoc($recipeRes)) {
                            $ingredientID = intval($rec['ingredientID']);
                            $requiredQty  = floatval($rec['requiredQuantity']);
                            $recipeUnit = $rec['measurementUnit'] ?? '';
                            $invUnit = $rec['inventoryUnit'] ?? '';

                            // Apply sugar/syrup multiplier only to correct ingredient
                            if ($ingredientID == $sweetenerID) {
                                $requiredQty *= $sugarMultiplier;
                            }

                            //  CONVERT UNITS BEFORE CALCULATING DEDUCTION
                            $convertedQty = convertToInventoryUnit($requiredQty, $recipeUnit, $invUnit);
                            $totalDeduction = $convertedQty * $orderQty;

                            // Log for debugging (optional - remove in production)
                            error_log("Order $orderID | Ingredient $ingredientID | Recipe: $requiredQty $recipeUnit | Converted: $convertedQty $invUnit | Order Qty: $orderQty | Total Deduction: $totalDeduction $invUnit");

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
                    echo "<div class='alert alert-success'>Inventory deducted for order #$orderID (sugar/syrup adjusted with proper unit conversion).</div>";
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