<?php
// db connection
$conn = new mysqli("localhost", "root", "", "saisyd");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// query low stock items (adjust threshold as needed, e.g. < 10)
$sql = "
    SELECT i.ingredientName, inv.quantity 
    FROM ingredients i
    INNER JOIN inventory inv ON i.ingredientID = inv.ingredientID
    WHERE inv.quantity < 10
    ORDER BY inv.quantity ASC
";
$result = $conn->query($sql);
?>

<!-- Low Stock Alert Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content stock-modal">

      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold" id="stockModalLabel">Low Stock Alert</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body text-center">
        <ul class="list-group list-group-flush">
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><?= htmlspecialchars($row['ingredientName']); ?></span>
                <span class="badge bg-danger rounded-pill"><?= $row['quantity']; ?></span>
              </li>
            <?php endwhile; ?>
          <?php else: ?>
            <li class="list-group-item text-muted">No items are low on stock 🎉</li>
          <?php endif; ?>
        </ul>
      </div>

      <div class="modal-footer border-0 d-flex justify-content-center">
        <a href="inventory-management.php" class="btn custom-visit px-4 py-2 rounded-pill">Go to Inventory</a>
        <button type="button" class="btn custom-logout px-4 py-2 rounded-pill" data-bs-dismiss="modal">Dismiss</button>
      </div>
    </div>
  </div>
</div>

<?php $conn->close(); ?>
