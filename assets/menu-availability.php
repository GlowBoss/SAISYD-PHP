<?php
include('connect.php');
session_start();

header('Content-Type: application/json');

// Auth check
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$productID = intval($_POST['productID'] ?? 0);
$requestedAvailability = isset($_POST['newAvailability']) ? ($_POST['newAvailability'] === 'Yes' ? 'Yes' : 'No') : null;

// Validate
if ($productID <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// --- Always calculate availableQuantity first ---
$sql = "
SELECT MIN(FLOOR(
    inv.total_quantity /
    CASE
        -- ==========================================
        -- KG INVENTORY (can be deducted by: kg, g)
        -- ==========================================
        WHEN inv.unit = 'kg' AND pr.measurementUnit = 'kg' THEN pr.requiredQuantity
        WHEN inv.unit = 'kg' AND pr.measurementUnit = 'g' THEN pr.requiredQuantity / 1000
        
        -- ==========================================
        -- G INVENTORY (can be deducted by: kg, g)
        -- ==========================================
        WHEN inv.unit = 'g' AND pr.measurementUnit = 'g' THEN pr.requiredQuantity
        WHEN inv.unit = 'g' AND pr.measurementUnit = 'kg' THEN pr.requiredQuantity * 1000
        
        -- ==========================================
        -- LITER INVENTORY (can be deducted by: L, ml, pump, tbsp, tsp, shot, cup)
        -- ==========================================
        WHEN inv.unit = 'L' AND pr.measurementUnit = 'L' THEN pr.requiredQuantity
        WHEN inv.unit = 'L' AND pr.measurementUnit = 'ml' THEN pr.requiredQuantity / 1000
        WHEN inv.unit = 'L' AND pr.measurementUnit = 'pump' THEN pr.requiredQuantity * 10 / 1000
        WHEN inv.unit = 'L' AND pr.measurementUnit = 'tbsp' THEN pr.requiredQuantity * 15 / 1000
        WHEN inv.unit = 'L' AND pr.measurementUnit = 'tsp' THEN pr.requiredQuantity * 5 / 1000
        WHEN inv.unit = 'L' AND pr.measurementUnit = 'shot' THEN pr.requiredQuantity * 30 / 1000
        WHEN inv.unit = 'L' AND pr.measurementUnit = 'cup' THEN pr.requiredQuantity * 240 / 1000
        
        -- ==========================================
        -- ML INVENTORY (can be deducted by: ml, pump, tbsp, tsp, shot, cup)
        -- ==========================================
        WHEN inv.unit = 'ml' AND pr.measurementUnit = 'ml' THEN pr.requiredQuantity
        WHEN inv.unit = 'ml' AND pr.measurementUnit = 'pump' THEN pr.requiredQuantity * 10
        WHEN inv.unit = 'ml' AND pr.measurementUnit = 'tbsp' THEN pr.requiredQuantity * 15
        WHEN inv.unit = 'ml' AND pr.measurementUnit = 'tsp' THEN pr.requiredQuantity * 5
        WHEN inv.unit = 'ml' AND pr.measurementUnit = 'shot' THEN pr.requiredQuantity * 30
        WHEN inv.unit = 'ml' AND pr.measurementUnit = 'cup' THEN pr.requiredQuantity * 240
        
        -- ==========================================
        -- PIECES INVENTORY (can only be deducted by: pcs)
        -- ==========================================
        WHEN inv.unit = 'pcs' AND pr.measurementUnit = 'pcs' THEN pr.requiredQuantity
        
        -- ==========================================
        -- FALLBACK: Same unit or no conversion
        -- ==========================================
        ELSE pr.requiredQuantity
    END
)) AS availableQuantity
FROM productrecipe pr
JOIN (
    SELECT ingredientID, SUM(quantity) AS total_quantity, MAX(unit) AS unit
    FROM inventory
    GROUP BY ingredientID
) inv ON pr.ingredientID = inv.ingredientID
WHERE pr.productID = $productID;
";

$res = mysqli_query($conn, $sql);
$availableQuantity = 0;
if ($res && $row = mysqli_fetch_assoc($res)) {
    $val = $row['availableQuantity'];
    $availableQuantity = is_null($val) ? 0 : intval($val);
}

// Fetch current availability
$currentRow = mysqli_query($conn, "SELECT isAvailable FROM products WHERE productID = $productID");
$currentAvailability = null;
if ($currentRow && $r = mysqli_fetch_assoc($currentRow))
    $currentAvailability = $r['isAvailable'];

// --- Decide final status ---
if ($requestedAvailability !== null) {
    // Always respect manual toggle (Yes or No)
    $finalAvailability = $requestedAvailability;
    $reason = 'manual_toggle';
} else if ($availableQuantity <= 0) {
    // Auto unavailable when stock runs out
    $finalAvailability = 'No';
    $reason = 'auto_unavailable';
} else {
    // Keep current availability if not manually changed
    $finalAvailability = $currentAvailability ?? 'Yes';
    $reason = 'keep_current';
}


// Update database
$updateSql = "UPDATE products 
              SET availableQuantity = $availableQuantity, isAvailable = '$finalAvailability'
              WHERE productID = $productID";

if (!mysqli_query($conn, $updateSql)) {
    echo json_encode(['success' => false, 'message' => 'DB update failed: ' . mysqli_error($conn)]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Availability updated successfully',
    'product' => [
        'productID' => $productID,
        'availableQuantity' => $availableQuantity,
        'isAvailable' => $finalAvailability
    ],
    'meta' => [
        'reason' => $reason
    ]
]);
?>