<?php
include '../assets/connect.php';
session_start();

if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'Admin') {
  header("Location: login.php");
  exit();
}

$lowStockQuery = "SELECT *
FROM inventory p
JOIN ingredients i ON p.ingredientID = i.ingredientID
WHERE p.quantity < p.threshold;";
$result = mysqli_query($conn, $lowStockQuery);

$lowStockItems = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $lowStockItems[] = $row;
    }
}

if (!empty($lowStockItems)) {
  $count = count($lowStockItems);
?>
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel"
     aria-hidden="true" data-lowstock-count="<?= $count ?>">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content stock-modal">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="stockModalLabel">Low Stock Alert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-3">The following items are running low on stock:</p>
        <ul class="list-group list-group-flush">
          <?php foreach ($lowStockItems as $item): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><b>ID No. <?= htmlspecialchars($item['inventoryID']) ?></b>
                <?= htmlspecialchars($item['ingredientName']) ?> :</span>
              <span class="badge rounded-pill" style="background-color: var(--btn-hover1);">
                <?= htmlspecialchars($item['quantity']) ?> <?= htmlspecialchars($item['unit']) ?>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="modal-footer border-0 d-flex justify-content-center">
        <a href="../admin/inventory-management.php"
           class="btn custom-visit px-4 py-2 rounded-pill">Go to Inventory</a>
        <button type="button" class="btn custom-logout px-4 py-2 rounded-pill"
                data-bs-dismiss="modal">Dismiss</button>
      </div>
    </div>
  </div>
</div>
<?php
}
?>
