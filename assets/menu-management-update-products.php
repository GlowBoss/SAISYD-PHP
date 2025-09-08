<?php
include 'connect.php';
session_start();

// Ensure admin
if(!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin'){
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if(!$data || !isset($data['productID'])){
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$productID = intval($data['productID']);
$name = mysqli_real_escape_string($conn, $data['name']);
$price = floatval($data['price']);
$ingredients = $data['ingredients'] ?? [];

// Update main product
mysqli_query($conn, "UPDATE products SET productName='$name', price='$price' WHERE productID=$productID");

// Delete old ingredients for this product
mysqli_query($conn, "DELETE FROM productRecipe WHERE productID=$productID");

// Insert updated ingredients
$stmt = $conn->prepare("INSERT INTO productRecipe (productID, ingredientID, measurementUnit, requiredQuantity) VALUES (?, ?, ?, ?)");

foreach($ingredients as $ing){
    // Find ingredientID from name if not known
    $ingredientName = mysqli_real_escape_string($conn, $ing['name']);
    $qty = floatval($ing['qty']);
    $unit = mysqli_real_escape_string($conn, $ing['unit']);

    // Assuming ingredient already exists
    $ingredientRes = mysqli_query($conn, "SELECT ingredientID FROM ingredients WHERE ingredientName='$ingredientName'");
    if($ingredientRow = mysqli_fetch_assoc($ingredientRes)){
        $ingredientID = $ingredientRow['ingredientID'];
        $stmt->bind_param("iisd", $productID, $ingredientID, $unit, $qty);
        $stmt->execute();
    }
}

$stmt->close();

echo json_encode(['success' => true]);
