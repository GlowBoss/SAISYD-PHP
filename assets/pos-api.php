<?php
include 'connect.php';
header('Content-Type: application/json');

try {
    // Step 1: Get categories that have available products
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

        // Step 2: Get products under each category
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
            // Since your database doesn't have product_sizes table, we'll create default sizes
            $sizes = [];
            
            // Check if product name contains size information
            $productName = $prod['productName'];
            $basePrice = (float) $prod['price'];
            
            if (strpos($productName, '(12oz)') !== false) {
                // Product has 12oz size, add both 12oz and 16oz
                $baseName = str_replace(' (12oz)', '', $productName);
                $sizes[] = [
                    "name" => "12oz",
                    "code" => "S1",
                    "price" => $basePrice
                ];
                $sizes[] = [
                    "name" => "16oz", 
                    "code" => "S2",
                    "price" => $basePrice + 20 // Add 20 pesos for larger size
                ];
            } elseif (strpos($productName, '(16oz)') !== false) {
                // Product has 16oz size, add both 12oz and 16oz
                $baseName = str_replace(' (16oz)', '', $productName);
                $sizes[] = [
                    "name" => "12oz",
                    "code" => "S1", 
                    "price" => max(50, $basePrice - 20) // Subtract 20 but minimum 50
                ];
                $sizes[] = [
                    "name" => "16oz",
                    "code" => "S2",
                    "price" => $basePrice
                ];
            } else {
                // No size specified, use regular
                $baseName = $productName;
                $sizes[] = [
                    "name" => "Regular",
                    "code" => "",
                    "price" => $basePrice
                ];
            }
            
            // Clean the product name for display
            $displayName = str_replace(['(12oz)', '(16oz)'], '', $productName);
            $displayName = trim($displayName);
            
            $contents[] = [
                "productID"   => $prod['productID'],
                "name"        => $displayName,
                "code"        => "P" . $prod['productID'],
                "img"         => $prod['image'],
                "quantity"    => (int) $prod['availableQuantity'],
                "sizes"       => $sizes,
                "sugarLevels" => [0, 25, 50, 75, 100] // Default sugar levels
            ];
        }

        // Only add category if it has products
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