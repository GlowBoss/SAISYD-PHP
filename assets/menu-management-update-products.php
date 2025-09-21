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

// Calculate available quantity based on inventory and productRecipe
$availableQuantity = 0;
$res = mysqli_query($conn, "
    SELECT MIN(FLOOR(
        inv.total_quantity /
        CASE 
            WHEN pr.measurementUnit = 'g' AND inv.unit = 'kg' THEN pr.requiredQuantity / 1000
            WHEN pr.measurementUnit = 'kg' AND inv.unit = 'g' THEN pr.requiredQuantity * 1000
            WHEN pr.measurementUnit = 'oz' AND inv.unit = 'g' THEN pr.requiredQuantity * 28.35
            WHEN pr.measurementUnit = 'g' AND inv.unit = 'oz' THEN pr.requiredQuantity / 28.35
            WHEN pr.measurementUnit = 'ml' AND inv.unit = 'L' THEN pr.requiredQuantity / 1000
            WHEN pr.measurementUnit = 'L' AND inv.unit = 'ml' THEN pr.requiredQuantity * 1000
            WHEN pr.measurementUnit = 'pump' AND inv.unit = 'ml' THEN pr.requiredQuantity * 10   -- 1 pump = 10 ml
            WHEN pr.measurementUnit = 'tbsp' AND inv.unit = 'ml' THEN pr.requiredQuantity * 15  -- 1 tbsp ≈ 15 ml
            WHEN pr.measurementUnit = 'tsp' AND inv.unit = 'ml' THEN pr.requiredQuantity * 5    -- 1 tsp ≈ 5 ml
            WHEN pr.measurementUnit = 'pcs' AND inv.unit = 'box' THEN pr.requiredQuantity / 12  -- assume 1 box = 12 pcs
            WHEN pr.measurementUnit = 'box' AND inv.unit = 'pcs' THEN pr.requiredQuantity * 12
            WHEN pr.measurementUnit = 'pack' AND inv.unit = 'pcs' THEN pr.requiredQuantity * 6  -- assume 1 pack = 6 pcs
            WHEN pr.measurementUnit = 'pcs' AND inv.unit = 'pack' THEN pr.requiredQuantity / 6
            WHEN pr.measurementUnit = inv.unit THEN pr.requiredQuantity

            ELSE pr.requiredQuantity END)) AS possible_count
    FROM productRecipe pr JOIN ( SELECT ingredientID, SUM(quantity) AS total_quantity, MAX(unit) AS unit
        FROM inventory GROUP BY ingredientID) inv ON pr.ingredientID = inv.ingredientID
    WHERE pr.productID = $productID
");
if($row = mysqli_fetch_assoc($res)){
    $availableQuantity = intval($row['possible_count']);
}

// Return JSON including updated availability
echo json_encode([
    'success' => true,
    'product' => [
        'availableQuantity' => $availableQuantity
    ]
]);
?>
