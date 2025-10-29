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
        WHEN pr.measurementUnit = 'g' AND inv.unit = 'kg' THEN pr.requiredQuantity / 1000
        WHEN pr.measurementUnit = 'kg' AND inv.unit = 'g' THEN pr.requiredQuantity * 1000
        WHEN pr.measurementUnit = 'oz' AND inv.unit = 'g' THEN pr.requiredQuantity * 28.35
        WHEN pr.measurementUnit = 'g' AND inv.unit = 'oz' THEN pr.requiredQuantity / 28.35
        WHEN pr.measurementUnit = 'ml' AND inv.unit = 'L' THEN pr.requiredQuantity / 1000
        WHEN pr.measurementUnit = 'L' AND inv.unit = 'ml' THEN pr.requiredQuantity * 1000
        WHEN pr.measurementUnit = 'pump' AND inv.unit = 'ml' THEN pr.requiredQuantity * 10
        WHEN pr.measurementUnit = 'tbsp' AND inv.unit = 'ml' THEN pr.requiredQuantity * 15
        WHEN pr.measurementUnit = 'tsp' AND inv.unit = 'ml' THEN pr.requiredQuantity * 5
        WHEN pr.measurementUnit = 'pcs' AND inv.unit = 'box' THEN pr.requiredQuantity / 12
        WHEN pr.measurementUnit = 'box' AND inv.unit = 'pcs' THEN pr.requiredQuantity * 12
        WHEN pr.measurementUnit = 'pack' AND inv.unit = 'pcs' THEN pr.requiredQuantity * 6
        WHEN pr.measurementUnit = 'pcs' AND inv.unit = 'pack' THEN pr.requiredQuantity / 6
        WHEN pr.measurementUnit = inv.unit THEN pr.requiredQuantity
        ELSE pr.requiredQuantity
    END
)) availableQuantity
FROM productRecipe pr
JOIN (
    SELECT ingredientID, SUM(quantity) total_quantity, MAX(unit) unit
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
if ($currentRow && $r = mysqli_fetch_assoc($currentRow)) $currentAvailability = $r['isAvailable'];

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
