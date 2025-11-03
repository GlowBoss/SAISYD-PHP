<?php
include 'connect.php';
header('Content-Type: application/json');

try {
    // Update products with 0 or less quantity to unavailable
    $updateQuery = "UPDATE products SET isAvailable = 'No' WHERE availableQuantity <= 0";
    executeQuery($updateQuery);


    // Step 1: Get all categories that have available products
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

        // Determine if this category should have sugar/ice options
        $categoryLower = strtolower($categoryName);
        $hasSugarIce = (
            strpos($categoryLower, 'coffee') !== false ||
            strpos($categoryLower, 'tea') !== false ||
            strpos($categoryLower, 'frappe') !== false ||
            strpos($categoryLower, 'milktea') !== false ||
            strpos($categoryLower, 'soda') !== false ||
            strpos($categoryLower, 'drink') !== false ||
            strpos($categoryLower, 'beverage') !== false
        );

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

            // Check if product has specific sizes in database
            // If you have a product_sizes table, uncomment this:
            /*
            $sizesStmt = $conn->prepare("
                SELECT sizeName, priceModifier 
                FROM product_sizes 
                WHERE productID = ?
                ORDER BY priceModifier ASC
            ");
            $sizesStmt->bind_param("i", $productID);
            $sizesStmt->execute();
            $sizesResult = $sizesStmt->get_result();
            
            $sizes = [];
            if ($sizesResult->num_rows > 0) {
                while ($size = $sizesResult->fetch_assoc()) {
                    $sizes[] = [
                        "name" => $size['sizeName'],
                        "code" => substr($size['sizeName'], 0, 1),
                        "price" => $basePrice + $size['priceModifier']
                    ];
                }
            } else {
                // Default to Regular if no sizes defined
                $sizes[] = [
                    "name" => "Regular",
                    "code" => "",
                    "price" => $basePrice
                ];
            }
            $sizesStmt->close();
            */

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