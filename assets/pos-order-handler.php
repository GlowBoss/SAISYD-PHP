<?php
include 'connect.php';
session_start();
header('Content-Type: application/json');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add_to_cart':
        addToCart();
        break;
    case 'get_cart':
        getCart();
        break;
    case 'clear_cart':
        clearCart();
        break;
    case 'checkout':
        checkout();
        break;
    case 'update_quantity':
        updateQuantity();
        break;
    case 'remove_item':
        removeItem();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

 // Get staff name from database based on userID in session
function getStaffName()
{
    global $conn;

    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        return 'POS: Walk-in Customer';
    }

    $userID = intval($_SESSION['userID']);
    $query = "SELECT fullName FROM users WHERE userID = $userID";
    $result = executeQuery($query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        return 'POS: ' . $user['fullName'];
    }

    return 'POS: Walk-in Customer';
}

// Updates product availability based on quantity
function updateProductAvailability($productID = null)
{
    global $conn;

    if ($productID) {
        // Update specific product
        $query = "UPDATE products 
                  SET isAvailable = CASE 
                      WHEN availableQuantity <= 0 THEN 'No'
                      ELSE 'Yes'
                  END 
                  WHERE productID = " . intval($productID);
    } else {
        // Update all products
        $query = "UPDATE products 
                  SET isAvailable = CASE 
                      WHEN availableQuantity <= 0 THEN 'No'
                      ELSE 'Yes'
                  END";
    }
    return executeQuery($query);
}

// Check if product has enough stock
function checkProductStock($productID, $requestedQuantity)
{
    global $conn;

    $productID = intval($productID);
    $requestedQuantity = intval($requestedQuantity);

    $checkQuery = "SELECT availableQuantity, productName FROM products WHERE productID = $productID";
    $result = executeQuery($checkQuery);
    $product = mysqli_fetch_assoc($result);

    if (!$product) {
        return ['available' => false, 'message' => 'Product not found'];
    }

    if ($product['availableQuantity'] < $requestedQuantity) {
        return [
            'available' => false,
            'message' => 'Insufficient stock for ' . $product['productName'] . '. Available: ' . $product['availableQuantity']
        ];
    }

    return ['available' => true, 'message' => 'Stock available'];
}

function addToCart()
{
    $productID = $_POST['productID'] ?? '';
    $productName = $_POST['productName'] ?? '';
    $price = (float) ($_POST['price'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 1);
    $sugarLevel = $_POST['sugarLevel'] ?? null;
    $iceLevel = $_POST['iceLevel'] ?? null;
    $size = $_POST['size'] ?? 'Regular';

    if (empty($productID) || empty($productName) || $price <= 0 || $quantity <= 0) {
        echo json_encode(['error' => 'Invalid product data']);
        return;
    }

    // Check stock availability before adding to cart
    $stockCheck = checkProductStock($productID, $quantity);
    if (!$stockCheck['available']) {
        echo json_encode(['error' => $stockCheck['message']]);
        return;
    }

    // Convert empty strings to null
    if ($sugarLevel === '' || $sugarLevel === '0') {
        $sugarLevel = null;
    }
    if ($iceLevel === '') {
        $iceLevel = null;
    }

    // Create unique key for cart item
    $itemKey = $productID . '_' . ($sugarLevel ?? 'none') . '_' . ($iceLevel ?? 'none');

    // Check if item already exists in cart
    if (isset($_SESSION['cart'][$itemKey])) {
        $newQuantity = $_SESSION['cart'][$itemKey]['quantity'] + $quantity;

        // Check stock for new total quantity
        $stockCheck = checkProductStock($productID, $newQuantity);
        if (!$stockCheck['available']) {
            echo json_encode(['error' => $stockCheck['message']]);
            return;
        }

        $_SESSION['cart'][$itemKey]['quantity'] = $newQuantity;
    } else {
        $_SESSION['cart'][$itemKey] = [
            'productID' => $productID,
            'productName' => $productName,
            'price' => $price,
            'quantity' => $quantity,
            'sugarLevel' => $sugarLevel,
            'iceLevel' => $iceLevel,
            'size' => $size,
            'totalPrice' => $price * $quantity
        ];
    }

    // Update total price
    $_SESSION['cart'][$itemKey]['totalPrice'] = $_SESSION['cart'][$itemKey]['price'] * $_SESSION['cart'][$itemKey]['quantity'];

    echo json_encode(['success' => true, 'message' => 'Item added to cart']);
}

function getCart()
{
    $cart = $_SESSION['cart'] ?? [];
    $total = 0;

    foreach ($cart as $item) {
        $total += $item['totalPrice'];
    }

    echo json_encode([
        'cart' => array_values($cart),
        'total' => $total,
        'itemCount' => count($cart)
    ]);
}

function clearCart()
{
    $_SESSION['cart'] = [];
    echo json_encode(['success' => true, 'message' => 'Cart cleared']);
}

function updateQuantity()
{
    $itemKey = $_POST['itemKey'] ?? '';
    $quantity = (int) ($_POST['quantity'] ?? 1);

    if (!isset($_SESSION['cart'][$itemKey])) {
        echo json_encode(['error' => 'Item not found']);
        return;
    }

    if ($quantity <= 0) {
        echo json_encode(['error' => 'Invalid quantity']);
        return;
    }

    // Check stock availability for new quantity
    $productID = $_SESSION['cart'][$itemKey]['productID'];
    $stockCheck = checkProductStock($productID, $quantity);
    if (!$stockCheck['available']) {
        echo json_encode(['error' => $stockCheck['message']]);
        return;
    }

    $_SESSION['cart'][$itemKey]['quantity'] = $quantity;
    $_SESSION['cart'][$itemKey]['totalPrice'] = $_SESSION['cart'][$itemKey]['price'] * $quantity;
    echo json_encode(['success' => true, 'message' => 'Quantity updated']);
}

function removeItem()
{
    $itemKey = $_POST['itemKey'] ?? '';

    if (isset($_SESSION['cart'][$itemKey])) {
        unset($_SESSION['cart'][$itemKey]);
        echo json_encode(['success' => true, 'message' => 'Item removed']);
    } else {
        echo json_encode(['error' => 'Item not found']);
    }
}

function checkout()
{
    global $conn;

    $paymentMethod = $_POST['paymentMethod'] ?? 'CASH';

    // Get staff name from database - this will be the customer name
    $customerName = getStaffName();

    $orderType = $_POST['orderType'] ?? 'dine-in';
    $contactNumber = $_POST['contactNumber'] ?? null;

    if (empty($_SESSION['cart'])) {
        echo json_encode(['error' => 'Cart is empty']);
        return;
    }

    try {
        // Start transaction
        mysqli_autocommit($conn, false);

        // First, verify all items in cart still have sufficient stock
        foreach ($_SESSION['cart'] as $item) {
            $stockCheck = checkProductStock($item['productID'], $item['quantity']);
            if (!$stockCheck['available']) {
                throw new Exception($stockCheck['message']);
            }
        }

        // Calculate total
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['totalPrice'];
        }

        // Generate order number - using the orderNumber column as integer
        $orderResult = executeQuery("SELECT orderNumber FROM orders ORDER BY orderID DESC LIMIT 1");
        $orderNumber = 1; // Default starting number

        if ($orderResult && mysqli_num_rows($orderResult) > 0) {
            $row = mysqli_fetch_assoc($orderResult);
            $lastOrderNumber = $row['orderNumber'];
            $orderNumber = $lastOrderNumber + 1;
        }

        // Escape string values for security
        $customerName = mysqli_real_escape_string($conn, $customerName);
        $orderType = mysqli_real_escape_string($conn, $orderType);
        $paymentMethod = mysqli_real_escape_string($conn, $paymentMethod);

        if ($contactNumber) {
            $contactNumber = mysqli_real_escape_string($conn, $contactNumber);
        }

        // Get userID from session, default to 1 if not set
        $userID = isset($_SESSION['userID']) ? intval($_SESSION['userID']) : 1;

        // Insert order - matching your database schema exactly
        $insertOrderQuery = "INSERT INTO orders (orderDate, customerName, orderContactNumber, totalAmount, orderType, orderNumber, status, isDone, userID) 
                           VALUES (NOW(), '$customerName', " .
            ($contactNumber ? "'$contactNumber'" : "NULL") . ", 
                           '$total', '$orderType', '$orderNumber', 'pending', 0, $userID)";

        $orderResult = executeQuery($insertOrderQuery);

        if (!$orderResult) {
            throw new Exception('Failed to create order: ' . mysqli_error($conn));
        }

        $orderID = mysqli_insert_id($conn);

        // Insert order items and update product quantities
        foreach ($_SESSION['cart'] as $item) {
            $productID = (int) $item['productID'];
            $quantity = (int) $item['quantity'];

            // Handle sugar level - store as varchar or NULL
            $sugar = 'NULL';
            if (isset($item['sugarLevel']) && $item['sugarLevel'] !== null && $item['sugarLevel'] !== '' && $item['sugarLevel'] !== '0') {
                $sugarValue = trim((string) $item['sugarLevel']);
                if ($sugarValue !== '' && $sugarValue !== '0') {
                    $sugar = "'" . mysqli_real_escape_string($conn, $sugarValue) . "% Sugar'";
                }
            }

            $ice = 'NULL'; 

            // Check if iceLevel exists and has a meaningful value
            if (array_key_exists('iceLevel', $item) && $item['iceLevel'] !== null && $item['iceLevel'] !== '') {
                $iceLevel = trim((string) $item['iceLevel']);

                // Match the exact values being sent from the frontend
                if ($iceLevel === 'Less Ice') {
                    $ice = "'Less Ice'";
                } elseif ($iceLevel === 'Extra Ice') {
                    $ice = "'Extra Ice'";
                } elseif ($iceLevel === 'Default Ice') {
                    $ice = "'Default Ice'";
                }
                // If it's any other value or empty, $ice remains NULL
            }

            // Handle notes - only store if there's actual content (not size)
            $notes = 'NULL'; // Default to NULL, not storing size anymore

            // Insert order item - note: values are already quoted where needed
            $insertItemQuery = "INSERT INTO orderitems (orderID, productID, quantity, sugar, ice, notes) 
                   VALUES ('$orderID', '$productID', '$quantity', $sugar, $ice, $notes)";

            $itemResult = executeQuery($insertItemQuery);
            if (!$itemResult) {
                throw new Exception('Failed to add order item: ' . mysqli_error($conn));
            }

            // Update product quantity and availability
            $updateProductQuery = "UPDATE products 
                      SET availableQuantity = availableQuantity - $quantity
                      WHERE productID = $productID";

            $updateResult = executeQuery($updateProductQuery);
            if (!$updateResult) {
                throw new Exception('Failed to update product quantity: ' . mysqli_error($conn));
            }

            // Then update availability based on the NEW quantity
            updateProductAvailability($productID);
            if (!$updateResult) {
                throw new Exception('Failed to update product quantity: ' . mysqli_error($conn));
            }

            // Verify the update was successful
            $checkQuery = "SELECT availableQuantity FROM products WHERE productID = $productID";
            $checkResult = executeQuery($checkQuery);
            $updatedProduct = mysqli_fetch_assoc($checkResult);

            if ($updatedProduct['availableQuantity'] < 0) {
                throw new Exception('Product ' . $item['productName'] . ' went into negative stock. Transaction rolled back.');
            }
        }

        // Insert payment - matching your payments table structure
        $insertPaymentQuery = "INSERT INTO payments (orderID, paymentMethod, paymentStatus, referenceNumber) 
                              VALUES ('$orderID', '$paymentMethod', 'Unpaid', NULL)";

        $paymentResult = executeQuery($insertPaymentQuery);
        if (!$paymentResult) {
            throw new Exception('Failed to create payment record: ' . mysqli_error($conn));
        }

        // Commit transaction
        mysqli_commit($conn);

        // Clear cart
        $_SESSION['cart'] = [];

        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully',
            'orderNumber' => $orderNumber,
            'orderID' => $orderID,
            'total' => $total
        ]);

    } catch (Exception $e) {
        // Rollback transaction
        mysqli_rollback($conn);
        echo json_encode(['error' => $e->getMessage()]);
    } finally {
        mysqli_autocommit($conn, true);
    }
}
?>