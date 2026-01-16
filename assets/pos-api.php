<?php
include 'connect.php';
header('Content-Type: application/json');

try {
    // Update products with 0 or less quantity to unavailable
    $updateQuery = "UPDATE products SET isAvailable = 'No' WHERE availableQuantity <= 0";
    executeQuery($updateQuery);
    $beverageCategories = [1, 2, 3, 4, 5, 6];
    $categoriesResult = executeQuery("
        SELECT DISTINCT c.categoryID, c.categoryName
        FROM categories c
        INNER JOIN products p ON c.categoryID = p.categoryID
        WHERE p.isAvailable = 'Yes'
        ORDER BY c.categoryName ASC
    ");

    if (!$categoriesResult) {
        throw new Exception("Failed to fetch categories");
    }

    $categories = [];

    while ($cat = $categoriesResult->fetch_assoc()) {
        $categoryID = $cat['categoryID'];
        $categoryName = $cat['categoryName'];
        $hasSugarIce = in_array($categoryID, $beverageCategories);
        $stmt = $conn->prepare("
            SELECT productID, productName, image, availableQuantity, price, isAvailable
            FROM products
            WHERE categoryID = ? AND isAvailable = 'Yes'
            ORDER BY productName ASC
        ");

        if (!$stmt) {
            throw new Exception("Failed to prepare statement");
        }

        $stmt->bind_param("i", $categoryID);
        $stmt->execute();
        $productsResult = $stmt->get_result();

        if (!$productsResult) {
            throw new Exception("Failed to fetch products");
        }

        $contents = [];
        while ($prod = $productsResult->fetch_assoc()) {
            $productName = $prod['productName'];
            $basePrice = (float) $prod['price'];
            $productID = $prod['productID'];

            
            $sizes = [
                [
                    "name" => "",
                    "code" => "",
                    "price" => $basePrice
                ]
            ];

            // Build product object
            $product = [
                "productID" => $productID,
                "name" => $productName,
                "code" => "P" . $productID,
                "img" => $prod['image'],
                "quantity" => (int) $prod['availableQuantity'],
                "isAvailable" => $prod['isAvailable'],
                "sizes" => $sizes
            ];

            // Only add sugar levels if it's a beverage category
            if ($hasSugarIce) {
                $product["sugarLevels"] = [0, 25, 50, 75, 100];
            } else {
                $product["sugarLevels"] = [];
            }

            $contents[] = $product;
        }

        $stmt->close();

        // Only add categories that have available products
        if (!empty($contents)) {
            $categories[] = [
                "category" => $categoryName,
                "categoryID" => $categoryID,
                "hasSugarIce" => $hasSugarIce,
                "contents" => $contents
            ];
        }
    }

    if (empty($categories)) {
        echo json_encode([
            "error" => "No available products found",
            "categories" => []
        ]);
        exit;
    }

    echo json_encode($categories);

} catch (Exception $e) {
    // Log error for debugging
    error_log("POS API Error: " . $e->getMessage());

    // Return error message
    http_response_code(500);
    echo json_encode([
        "error" => $e->getMessage(),
        "categories" => []
    ]);
}
?>