<?php
include 'connect.php';
header('Content-Type: application/json');

try {
    // Update products with 0 or less quantity to unavailable
    $updateQuery = "UPDATE products SET isAvailable = 'No' WHERE availableQuantity <= 0";
    executeQuery($updateQuery);

    // Define which categories should have sugar/ice options based on database categoryIDs
    // 1=Espresso Based, 2=Non-Coffee, 3=Frappé, 4=Milktea, 5=Fruit Tea, 6=Fruit Soda
    // 7=Pasta, 8=Korean Egg-Drop Sandwich, 9=Mini Korean Egg-Drop Sandwich
    // 10=Rice Meals, 11=Wings (Ala Carte), 12=Combo Meals, 13=Snacks
    $beverageCategories = [1, 2, 3, 4, 5, 6]; // IDs for beverage categories

    // Step 1: Get all categories that have available products
    $categoriesResult = executeQuery("
        SELECT DISTINCT c.categoryID, c.categoryName
        FROM categories c
        INNER JOIN products p ON c.categoryID = p.categoryID
        WHERE p.isAvailable = 'Yes'
        ORDER BY c.categoryID ASC
    ");

    if (!$categoriesResult) {
        throw new Exception("Failed to fetch categories");
    }

    $categories = [];

    while ($cat = $categoriesResult->fetch_assoc()) {
        $categoryID = $cat['categoryID'];
        $categoryName = $cat['categoryName'];

        // Check if this category should have sugar/ice options based on categoryID
        $hasSugarIce = in_array($categoryID, $beverageCategories);

        // Step 2: Get ONLY AVAILABLE products under each category
        // Use prepared statement to prevent SQL injection
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

            // For now, using single Regular size
            $sizes = [[
                "name" => "Regular",
                "code" => "",
                "price" => $basePrice
            ]];

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