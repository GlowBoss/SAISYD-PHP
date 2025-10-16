<?php
include 'connect.php';
header('Content-Type: application/json');

try {
    // Update products with 0 or less quantity to unavailable
    $updateQuery = "UPDATE products SET isAvailable = 'No' WHERE availableQuantity <= 0";
    executeQuery($updateQuery);

    // Update products with quantity greater than 0 to available
    $updateQuery2 = "UPDATE products SET isAvailable = 'Yes' WHERE availableQuantity > 0";
    executeQuery($updateQuery2);

    // Step 1: Get all categories that have available products
    $categoriesResult = executeQuery("
        SELECT DISTINCT c.categoryID, c.categoryName
        FROM categories c
        INNER JOIN products p ON c.categoryID = p.categoryID
        WHERE p.isAvailable = 'Yes'
        ORDER BY c.categoryName ASC
    ");

    if (!$categoriesResult) {
        throw new Exception("Failed to fetch categories: " . $conn->error);
    }

    $categories = [];

    while ($cat = $categoriesResult->fetch_assoc()) {
        $categoryID = $cat['categoryID'];

        // Step 2: Get ONLY AVAILABLE products under each category
        $productsResult = executeQuery("
            SELECT productID, productName, image, availableQuantity, price, isAvailable
            FROM products
            WHERE categoryID = {$categoryID} AND isAvailable = 'Yes'
            ORDER BY productName ASC
        ");

        if (!$productsResult) {
            throw new Exception("Failed to fetch products: " . $conn->error);
        }

        $contents = [];
        while ($prod = $productsResult->fetch_assoc()) {
            // Use product name exactly as stored in database
            $productName = $prod['productName'];
            $basePrice = (float) $prod['price'];

            // Create a single size entry with the actual product name and price
            $sizes = [];
            $sizes[] = [
                "name" => "Regular",
                "code" => "",
                "price" => $basePrice
            ];

            $contents[] = [
                "productID" => $prod['productID'],
                "name" => $productName, // Use exact database name
                "code" => "P" . $prod['productID'],
                "img" => $prod['image'],
                "quantity" => (int) $prod['availableQuantity'],
                "isAvailable" => $prod['isAvailable'],
                "sizes" => $sizes,
                "sugarLevels" => [0, 25, 50, 75, 100] // Default sugar levels
            ];
        }

        // Only add categories that have available products
        if (!empty($contents)) {
            $categories[] = [
                "category" => $cat['categoryName'],
                "contents" => $contents
            ];
        }
    }

    if (empty($categories)) {
        throw new Exception("No available products found");
    }

    echo json_encode($categories);

} catch (Exception $e) {
    // Return error message
    echo json_encode(["error" => $e->getMessage()]);
}
?>