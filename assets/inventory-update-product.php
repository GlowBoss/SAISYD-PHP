<?php
include 'connect.php';
session_start();
header('Content-Type: application/json');

// Prevent unauthorized access
if (!isset($_SESSION['userID'])) {
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

// Validate required fields
if (
    !isset($_POST['inventoryID'], $_POST['ingredientIDEdit'], $_POST['ingredientNameEdit'], 
            $_POST['quantityEdit'], $_POST['unitEdit'], $_POST['expirationEdit'], $_POST['thresholdEdit'])
) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$inventoryID   = intval($_POST['inventoryID']);
$ingredientID  = intval($_POST['ingredientIDEdit']); // ✅ must exist
$ingredient    = trim($_POST['ingredientNameEdit']);
$quantity      = floatval($_POST['quantityEdit']);
$unit          = mysqli_real_escape_string($conn, $_POST['unitEdit']);
$expiration    = $_POST['expirationEdit'];  // YYYY-MM-DD
$threshold     = floatval($_POST['thresholdEdit']);

// ✅ Ensure ingredientID exists in DB (optional safety check)
$checkStmt = $conn->prepare("SELECT ingredientID FROM ingredients WHERE ingredientID = ?");
$checkStmt->bind_param("i", $ingredientID);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Invalid ingredient ID"]);
    $checkStmt->close();
    exit;
}
$checkStmt->close();

// ✅ Update inventory row
$sql = "UPDATE inventory 
        SET ingredientID = ?, 
            quantity = ?, 
            unit = ?, 
            expirationDate = ?, 
            threshold = ?, 
            lastUpdated = NOW()
        WHERE inventoryID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("idssdi", $ingredientID, $quantity, $unit, $expiration, $threshold, $inventoryID);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Inventory updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error updating: " . $conn->error]);
}

$stmt->close();
$conn->close();
?>
