<?php
include '../assets/connect.php';
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

    $paymentMethod = $_POST['paymentMethod'] ?? 'cash';
    $customerName = $_POST['customerName'] ?? '';
    $orderType = $_POST['orderType'] ?? 'dine-in';
    $contactNumber = $_POST['contactNumber'] ?? '';

    if (empty($_SESSION['cart'])) {
        echo json_encode(['error' => 'Cart is empty']);
        return;
    }

    try {
        // Start transaction
        $conn->autocommit(false);

        // Calculate total
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['totalPrice'];
        }

        // Generate order number
        $orderResult = executeQuery("SELECT COUNT(*) as count FROM orders");
        $orderCount = $orderResult->fetch_assoc()['count'];
        $orderNumber = 'ORD-' . str_pad($orderCount + 1, 4, '0', STR_PAD_LEFT);

        // Insert order
        $insertOrderQuery = "INSERT INTO orders (orderDate, customerName, totalAmount, orderType, orderNumber, status, orderContactNumber, address) 
                           VALUES (NOW(), ?, ?, ?, ?, 'pending', ?, '')";
        $stmt = $conn->prepare($insertOrderQuery);
        $stmt->bind_param('sdsss', $customerName, $total, $orderType, $orderNumber, $contactNumber);

        if (!$stmt->execute()) {
            throw new Exception('Failed to create order');
        }

        $orderID = $conn->insert_id;

        // Insert order items
        $insertItemQuery = "INSERT INTO orderitems (orderID, productID, quantity, sugar, ice, notes) VALUES (?, ?, ?, ?, ?, '')";
        $itemStmt = $conn->prepare($insertItemQuery);

        foreach ($_SESSION['cart'] as $item) {
            $itemStmt->bind_param('iiiss', $orderID, $item['productID'], $item['quantity'], $item['sugarLevel'], $item['iceLevel']);
            if (!$itemStmt->execute()) {
                throw new Exception('Failed to add order item');
            }
        }

        // Insert payment
        $insertPaymentQuery = "INSERT INTO payments (paymentMethod, paymentStatus, referenceNumber, orderID) VALUES (?, 'pending', '', ?)";
        $paymentStmt = $conn->prepare($insertPaymentQuery);
        $paymentStmt->bind_param('si', $paymentMethod, $orderID);

        if (!$paymentStmt->execute()) {
            throw new Exception('Failed to create payment record');
        }

        // Commit transaction
        $conn->commit();

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
        $conn->rollback();
        echo json_encode(['error' => $e->getMessage()]);
    } finally {
        $conn->autocommit(true);
    }
}
?>