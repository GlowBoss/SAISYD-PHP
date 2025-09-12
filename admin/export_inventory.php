<?php
include '../assets/connect.php';

$inventoryQuery = "SELECT i.inventoryID,
                          ing.ingredientName,
                          i.quantity,
                          i.unit,
                          i.lastUpdated,
                          i.expirationDate
                   FROM inventory i
                   LEFT JOIN ingredients ing 
                        ON i.ingredientID = ing.ingredientID
                   ORDER BY i.inventoryID ASC";

$result = executeQuery($inventoryQuery);

// Handle filename from query parameter
$filename = isset($_GET['filename']) && $_GET['filename'] !== "" 
    ? $_GET['filename'] . ".xls" 
    : "inventory_export.xls";

// Headers to download as Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');


$output = fopen("php://output", "w");

fputcsv($output, ['Item Code', 'Item Name', 'Quantity', 'Unit', 'Date Purchased', 'Expiration Date'], "\t");

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            str_pad($row['inventoryID'], 3, "0", STR_PAD_LEFT),
            $row['ingredientName'] ?? 'No Ingredient',
            $row['quantity'],
            $row['unit'],
            date("M d Y", strtotime($row['lastUpdated'])),
            date("M d Y", strtotime($row['expirationDate']))
        ], "\t");
    }
}

fclose($output);
exit;
