<?php
include 'connect.php';
session_start();

// Ensure admin
if(!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin'){
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check for POST data
if(empty($_POST['productID'])){
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$productID = intval($_POST['productID']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$price = floatval($_POST['price']);
$categoryID = intval($_POST['categoryID']);
$ingredients = isset($_POST['ingredients']) ? json_decode($_POST['ingredients'], true) : [];

// Handle file upload
$imageFile = '';
if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK){
    $uploadDir = '../assets/img/img-menu/';
    $imageFile = basename($_FILES['attachment']['name']);
    $targetPath = $uploadDir . $imageFile;
    move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath);
}

// Update product info
$updateQuery = "UPDATE products SET productName='$name', price=$price, categoryID=$categoryID";
if($imageFile){
    $updateQuery .= ", image='$imageFile'";
}
$updateQuery .= " WHERE productID=$productID";

mysqli_query($conn, $updateQuery);

// Delete old ingredients
mysqli_query($conn, "DELETE FROM productRecipe WHERE productID=$productID");

// Insert updated ingredients
if(!empty($ingredients)){
    $stmt = $conn->prepare("INSERT INTO productRecipe (productID, ingredientID, measurementUnit, requiredQuantity) VALUES (?, ?, ?, ?)");
    foreach($ingredients as $ing){
        $ingredientName = mysqli_real_escape_string($conn, $ing['name']);
        $qty = floatval($ing['qty']);
        $unit = mysqli_real_escape_string($conn, $ing['unit']);

        // Lookup ingredientID from name
        $res = mysqli_query($conn, "SELECT ingredientID FROM ingredients WHERE ingredientName='$ingredientName'");
        if($row = mysqli_fetch_assoc($res)){
            $ingredientID = $row['ingredientID'];
            $stmt->bind_param("iisd", $productID, $ingredientID, $unit, $qty);
            $stmt->execute();
        }
    }
    $stmt->close();
}

echo json_encode(['success' => true]);
?>
