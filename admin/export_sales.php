<?php
include '../assets/connect.php';

$salesQuery = "SELECT 
                  o.orderID,
                  p.productName,
                  c.categoryName,
                  p.price,
                  od.quantity,
                  (p.price * od.quantity) AS total,
                  p.productID
               FROM orders o
               JOIN orderitems od ON o.orderID = od.orderID
               JOIN products p ON od.productID = p.productID
               JOIN categories c ON p.categoryID = c.categoryID
               ORDER BY o.orderID ASC";

$result = executeQuery($salesQuery);

// Send as Excel-compatible content
header('Content-Type: application/vnd.ms-excel');

header('Cache-Control: max-age=0');
$output = fopen("php://output", "w");

// Table Header
fputcsv($output, ['#', 'Item Name', 'Category', 'Price (Each)', 'Quantity', 'Total', 'Product ID'], "\t");

// Data Row
if ($result && mysqli_num_rows($result) > 0) {
    $i = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $i++,
            $row['productName'],
            $row['categoryName'],
            "₱" . $row['price'],
            $row['quantity'],
            "₱" . $row['total'],
            $row['productID']
        ], "\t");
    }
}
fclose($output);
exit;
