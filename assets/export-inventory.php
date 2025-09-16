<?php
include '../assets/connect.php';

// Check if export action is requested
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'export') {
    
    // Get inventory data with ingredient names
    $inventoryQuery = "SELECT i.inventoryID,
                              ing.ingredientName,
                              i.quantity,
                              i.unit,
                              i.lastUpdated,
                              i.expirationDate,
                              i.threshold
                       FROM inventory i
                       LEFT JOIN ingredients ing 
                            ON i.ingredientID = ing.ingredientID
                       ORDER BY i.lastUpdated DESC";

    $result = executeQuery($inventoryQuery);
    
    if (!$result) {
        die('Error fetching inventory data');
    }

    // Prepare data for export
    $inventoryData = [];
    
    // Add header row
    $inventoryData[] = [
        'Item Code',
        'Item Name', 
        'Quantity',
        'Unit',
        'Stock Status',
        'Date Purchased',
        'Expiration Date',
        'Expiry Status',
        'Threshold'
    ];
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate status
        $quantity = $row['quantity'];
        $threshold = $row['threshold'];
        $expirationDate = strtotime($row['expirationDate']);
        $currentDate = time();
        
        $stockStatus = '';
        if ($quantity <= 0) {
            $stockStatus = 'Out of Stock';
        } elseif ($quantity <= $threshold) {
            $stockStatus = 'Low Stock';
        } else {
            $stockStatus = 'Normal';
        }
        
        $expiryStatus = '';
        $daysUntilExpiry = ceil(($expirationDate - $currentDate) / (60 * 60 * 24));
        if ($daysUntilExpiry <= 0) {
            $expiryStatus = 'Expired';
        } elseif ($daysUntilExpiry <= 7) {
            $expiryStatus = 'Expiring Soon';
        } else {
            $expiryStatus = 'Fresh';
        }
        
        $inventoryData[] = [
            str_pad($row['inventoryID'], 3, "0", STR_PAD_LEFT),
            $row['ingredientName'] ?? 'No Ingredient',
            $row['quantity'],
            $row['unit'],
            $stockStatus,
            date("M d, Y", strtotime($row['lastUpdated'])),
            date("M d, Y", strtotime($row['expirationDate'])),
            $expiryStatus,
            $row['threshold']
        ];
    }
    
    // Generate filename with timestamp
    $filename = 'inventory_export_' . date('Y-m-d_H-i-s') . '.csv';
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');
    
    // Create CSV content
    $output = fopen('php://output', 'w');
    
    // Add BOM for Excel UTF-8 support
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    foreach ($inventoryData as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}

// If not a POST request or no action specified, redirect back
header('Location: inventory-management.php');
exit();
?>