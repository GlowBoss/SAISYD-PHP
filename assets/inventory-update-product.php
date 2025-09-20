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
    !isset($_POST['inventoryID'], $_POST['ingredientNameEdit'], $_POST['quantityEdit'], 
            $_POST['unitEdit'], $_POST['expirationEdit'], $_POST['thresholdEdit'])
) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

$inventoryID   = intval($_POST['inventoryID']);
$ingredientID  = !empty($_POST['ingredientIDEdit']) ? intval($_POST['ingredientIDEdit']) : 0;
$ingredient    = trim($_POST['ingredientNameEdit']);
$quantity      = floatval($_POST['quantityEdit']);
$unit          = mysqli_real_escape_string($conn, $_POST['unitEdit']);
$expiration    = $_POST['expirationEdit'];  // YYYY-MM-DD
$threshold     = floatval($_POST['thresholdEdit']);

// ✅ If no ingredientID → add new ingredient
if ($ingredientID === 0 && !empty($ingredient)) {
    $stmtNew = $conn->prepare("INSERT INTO ingredients (ingredientName) VALUES (?)");
    $stmtNew->bind_param("s", $ingredient);
    if ($stmtNew->execute()) {
        $ingredientID = $stmtNew->insert_id;
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add new ingredient: " . $conn->error]);
        exit;
    }
    $stmtNew->close();
}

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
