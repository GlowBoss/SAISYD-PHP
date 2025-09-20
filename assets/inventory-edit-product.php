<?php
include 'connect.php';
session_start();

// Ensure admin
if(!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin'){
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Validate
if(empty($_POST['inventoryID']) || empty($_POST['ingredientName'])){
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$inventoryID   = intval($_POST['inventoryID']);
$ingredientID  = intval($_POST['ingredientID']); // could be blank if "new ingredient"
$ingredientName = mysqli_real_escape_string($conn, $_POST['ingredientName']);
$quantity      = floatval($_POST['quantity']);
$unit          = mysqli_real_escape_string($conn, $_POST['unit']);
$expiration    = mysqli_real_escape_string($conn, $_POST['expirationDate']);
$threshold     = floatval($_POST['threshold']);

// If new ingredient, insert it
if(isset($_POST['ingredientID']) && $_POST['ingredientID'] === ''){
    $insertIng = $conn->prepare("INSERT INTO ingredients (ingredientName) VALUES (?)");
    $insertIng->bind_param("s", $ingredientName);
    if($insertIng->execute()){
        $ingredientID = $insertIng->insert_id;
    }
    $insertIng->close();
}

// Update inventory row
$stmt = $conn->prepare("UPDATE inventory 
    SET ingredientID=?, quantity=?, unit=?, expirationDate=?, threshold=? 
    WHERE inventoryID=?");
$stmt->bind_param("idssdi", $ingredientID, $quantity, $unit, $expiration, $threshold, $inventoryID);

if($stmt->execute()){
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}
$stmt->close();
$conn->close();
?>
