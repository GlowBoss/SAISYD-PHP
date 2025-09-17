<?php
include 'connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing ID']);
    exit;
}

$id = intval($_GET['id']);

$query = "SELECT p.productID, p.productName, p.price, p.image, p.categoryID,
                 pr.productRecipeID, pr.requiredQuantity, pr.measurementUnit,
                 i.ingredientID, i.ingredientName,
                 inv.quantity AS inventoryQuantity, inv.unit AS inventoryUnit
          FROM products p
          LEFT JOIN productRecipe pr ON p.productID = pr.productID
          LEFT JOIN ingredients i ON pr.ingredientID = i.ingredientID
          LEFT JOIN inventory inv ON i.ingredientID = inv.ingredientID
          WHERE p.productID = $id";


$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['product' => null, 'ingredients' => []]);
    exit;
}

$product = null;
$ingredients = [];

while ($row = mysqli_fetch_assoc($result)) {
    if (!$product) {
        $product = [
            "productID" => $row['productID'],
            "productName" => $row['productName'],
            "price" => $row['price'],
            "image" => $row['image'],          
            "categoryID" => $row['categoryID'] 
        ];

    }

    if ($row['ingredientID']) {
        $ingredients[] = [
            "productRecipeID" => $row['productRecipeID'],
            "ingredientID" => $row['ingredientID'],
            "ingredientName" => $row['ingredientName'],
            "requiredQuantity" => $row['requiredQuantity'],
            "measurementUnit" => $row['measurementUnit'],
            "inventoryQuantity" => $row['inventoryQuantity'],
            "inventoryUnit" => $row['inventoryUnit']
        ];
    }
}

echo json_encode(['product' => $product, 'ingredients' => $ingredients]);
