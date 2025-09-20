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

function addToCart()
{
    $productID = $_POST['productID'] ?? '';
    $productName = $_POST['productName'] ?? '';
    $price = (float) ($_POST['price'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 1);
    $sugarLevel = $_POST['sugarLevel'] ?? '';
    $iceLevel = $_POST['iceLevel'] ?? '';
    $size = $_POST['size'] ?? '';

    if (empty($productID) || empty($productName) || $price <= 0 || $quantity <= 0) {
        echo json_encode(['error' => 'Invalid product data']);
        return;
    }

    // Create unique key for cart item
    $itemKey = $productID . '_' . $size . '_' . $sugarLevel . '_' . $iceLevel;

    // Check if item already exists in cart
    if (isset($_SESSION['cart'][$itemKey])) {
        $_SESSION['cart'][$itemKey]['quantity'] += $quantity;
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

    if (isset($_SESSION['cart'][$itemKey]) && $quantity > 0) {
        $_SESSION['cart'][$itemKey]['quantity'] = $quantity;
        $_SESSION['cart'][$itemKey]['totalPrice'] = $_SESSION['cart'][$itemKey]['price'] * $quantity;
        echo json_encode(['success' => true, 'message' => 'Quantity updated']);
    } else {
        echo json_encode(['error' => 'Item not found or invalid quantity']);
    }
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

    $paymentMethod = $_POST['paymentMethod'] ?? 'Cash';
    $customerName = $_POST['customerName'] ?? 'Walk-in Customer';
    $orderType = $_POST['orderType'] ?? 'dine-in';
    $contactNumber = $_POST['contactNumber'] ?? null;

    if (empty($_SESSION['cart'])) {
        echo json_encode(['error' => 'Cart is empty']);
        return;
    }

    try {
        // Start transaction
        mysqli_autocommit($conn, false);

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

        // Insert order - matching your database schema exactly
        $insertOrderQuery = "INSERT INTO orders (orderDate, customerName, orderContactNumber, totalAmount, orderType, orderNumber, status, isDone, userID) 
                           VALUES (NOW(), '$customerName', " .
            ($contactNumber ? "'$contactNumber'" : "NULL") . ", 
                           '$total', '$orderType', '$orderNumber', 'pending', 0, 1)";

        $orderResult = executeQuery($insertOrderQuery);

        if (!$orderResult) {
            throw new Exception('Failed to create order: ' . mysqli_error($conn));
        }

        $orderID = mysqli_insert_id($conn);

        // Insert order items - matching your orderitems table structure
        foreach ($_SESSION['cart'] as $item) {
            $productID = (int) $item['productID'];
            $quantity = (int) $item['quantity'];

            // Convert sugar level to boolean (1 or 0) - assuming it's stored as tinyint(1)
            $sugar = 0;
            if (!empty($item['sugarLevel']) && $item['sugarLevel'] !== '0' && $item['sugarLevel'] !== '0%') {
                $sugar = 1;
            }

            // Ice level - matching enum('Less','Normal','Extra') - convert from display text to DB values
            $ice = 'Normal'; // default
            if (!empty($item['iceLevel'])) {
                $iceLevel = strtolower($item['iceLevel']);
                if (strpos($iceLevel, 'less') !== false || strpos($iceLevel, 'no') !== false) {
                    $ice = 'Less';
                } elseif (strpos($iceLevel, 'extra') !== false) {
                    $ice = 'Extra';
                } else {
                    $ice = 'Normal';
                }
            }

            $notes = !empty($item['size']) ? mysqli_real_escape_string($conn, $item['size']) : '';

            $insertItemQuery = "INSERT INTO orderitems (orderID, productID, quantity, sugar, ice, notes) 
                               VALUES ('$orderID', '$productID', '$quantity', '$sugar', '$ice', '$notes')";

            $itemResult = executeQuery($insertItemQuery);
            if (!$itemResult) {
                throw new Exception('Failed to add order item: ' . mysqli_error($conn));
            }
        }

        // Insert payment - matching your payments table structure
        $insertPaymentQuery = "INSERT INTO payments (orderID, paymentMethod, paymentStatus, referenceNumber) 
                              VALUES ('$orderID', '$paymentMethod', 'Paid', NULL)";

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