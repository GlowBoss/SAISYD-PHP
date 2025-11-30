<?php
include '../assets/connect.php';

// Low Stock Items (quantity > 0 AND quantity <= threshold)
// Use GREATEST to automatically convert negative values to 0
$lowStockSql = "
    SELECT i.ingredientName, 
           GREATEST(inv.quantity, 0) as quantity, 
           inv.unit, 
           inv.threshold
    FROM ingredients i
    INNER JOIN inventory inv ON i.ingredientID = inv.ingredientID
    WHERE GREATEST(inv.quantity, 0) > 0 
      AND GREATEST(inv.quantity, 0) <= inv.threshold
    ORDER BY GREATEST(inv.quantity, 0) ASC
";
$lowStockResult = executeQuery($lowStockSql);
$lowStockCount = 0;
$lowStockRows = [];
if ($lowStockResult) {
  $lowStockCount = mysqli_num_rows($lowStockResult);
  while ($row = mysqli_fetch_assoc($lowStockResult)) {
    $lowStockRows[] = $row;
  }
}

// Out of Stock Items (quantity <= 0)
// Includes both 0 and negative values
$outOfStockSql = "
    SELECT i.ingredientName, inv.unit
    FROM ingredients i
    INNER JOIN inventory inv ON i.ingredientID = inv.ingredientID
    WHERE GREATEST(inv.quantity, 0) = 0
    ORDER BY i.ingredientName ASC
";
$outOfStockResult = executeQuery($outOfStockSql);
$outOfStockCount = 0;
$outOfStockRows = [];
if ($outOfStockResult) {
  $outOfStockCount = mysqli_num_rows($outOfStockResult);
  while ($row = mysqli_fetch_assoc($outOfStockResult)) {
    $outOfStockRows[] = $row;
  }
}

// Expired Items
// Use GREATEST to handle negative quantities
$expiredSql = "
    SELECT i.ingredientName, 
           GREATEST(inv.quantity, 0) as quantity, 
           inv.unit, 
           inv.expirationDate
    FROM ingredients i
    INNER JOIN inventory inv ON i.ingredientID = inv.ingredientID
    WHERE inv.expirationDate < CURDATE()
    ORDER BY inv.expirationDate ASC
";
$expiredResult = executeQuery($expiredSql);
$expiredCount = 0;
$expiredRows = [];
if ($expiredResult) {
  $expiredCount = mysqli_num_rows($expiredResult);
  while ($row = mysqli_fetch_assoc($expiredResult)) {
    $expiredRows[] = $row;
  }
}

$totalCount = $lowStockCount + $outOfStockCount + $expiredCount;
?>

<style>
  .modal-body>div::-webkit-scrollbar {
    width: 8px;
  }

  .modal-body>div::-webkit-scrollbar-track {
    background: var(--card-bg-color);
    border-radius: 10px;
  }

  .modal-body>div::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 10px;
  }

  .modal-body>div::-webkit-scrollbar-thumb:hover {
    background: var(--text-color-dark);
  }

  @media (max-width: 576px) {
    .modal-dialog {
      margin: 0.5rem;
      max-width: calc(100% - 1rem);
    }

    .modal-content {
      border-radius: 1rem !important;
    }

    .modal-header .modal-title {
      font-size: 1.1rem !important;
      line-height: 1.3;
    }

    .modal-body {
      padding: 1rem !important;
    }

    .list-group-item {
      font-size: 0.9rem;
      padding: 0.75rem !important;
    }

    .badge {
      font-size: 0.75rem !important;
      padding: 0.25rem 0.5rem !important;
    }
  }

  @media (max-width: 375px) {
    .modal-dialog {
      margin: 0.25rem;
      max-width: calc(100% - 0.5rem);
    }

    .btn {
      font-size: 0.8rem !important;
      padding: 0.5rem 1rem !important;
    }

    .modal-header .btn-close {
      padding: 0.25rem;
      margin: -0.25rem;
    }
  }

  @media (hover: none) and (pointer: coarse) {
    .btn:hover {
      transform: none !important;
    }

    .btn:active {
      transform: translateY(1px) !important;
      transition: transform 0.1s ease !important;
    }
  }
</style>

<div class="modal fade" id="stockModal" data-bs-backdrop="true" tabindex="-1" aria-labelledby="stockModalLabel"
  aria-hidden="true" data-lowstock-count="<?= $totalCount ?>">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color); max-height: 90vh;">

      <div class="modal-header border-0 pb-2 px-3 px-sm-4 d-flex justify-content-between align-items-center">
        <h1 class="modal-title fs-5 fs-sm-4 fw-bold" id="stockModalLabel"
          style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
          Inventory Alert
        </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
          style="filter: invert(50%);"></button>
      </div>

      <div class="modal-body px-3 px-sm-4">
        <div class="text-center mb-3 mb-sm-4">
          <?php if ($totalCount > 0): ?>
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: #dc3545;"></i>
          <?php else: ?>
            <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #198754;"></i>
          <?php endif; ?>
        </div>

        <div class="text-center mb-3 mb-sm-4">
          <?php if ($totalCount > 0): ?>
            <p class="mb-2 fs-6 fs-sm-5"
              style="font-family: var(--secondaryFont); color: var(--text-color-dark); line-height: 1.4;">
              Your inventory needs attention:
            </p>
            <p class="fw-bold mb-2 fs-6 fs-sm-5" style="font-family: var(--primaryFont); color: var(--text-color-dark);">
              <strong><?= $totalCount ?> Issue<?= $totalCount > 1 ? 's' : '' ?> Detected</strong>
            </p>
            <?php if ($lowStockCount > 0): ?>
              <span class="badge me-2"
                style="background: color-mix(in srgb, #ffc107 15%, transparent); color: #ffc107; border: 1px solid color-mix(in srgb, #ffc107 30%, transparent);"><?= $lowStockCount ?>
                Low Stock</span>
            <?php endif; ?>
            <?php if ($outOfStockCount > 0): ?>
              <span class="badge me-2"
                style="background: color-mix(in srgb, #6c757d 15%, transparent); color: #6c757d; border: 1px solid color-mix(in srgb, #6c757d 30%, transparent);"><?= $outOfStockCount ?>
                Out of Stock</span>
            <?php endif; ?>
            <?php if ($expiredCount > 0): ?>
               <span class="badge"
                style="background: color-mix(in srgb, #dc3545 15%, transparent); color: #dc3545; border: 1px solid color-mix(in srgb, #dc3545 30%, transparent);"><?= $expiredCount ?>
                Expired</span>
            <?php endif; ?>
          <?php else: ?>
            <p class="mb-2 fs-6 fs-sm-5"
              style="font-family: var(--secondaryFont); color: var(--text-color-dark); line-height: 1.4;">
              All inventory items are well stocked and fresh!
            </p>
            <p class="fw-bold mb-2 fs-6 fs-sm-5" style="font-family: var(--primaryFont); color: var(--text-color-dark);">
              <strong>No Issues Found</strong>
            </p>
          <?php endif; ?>
        </div>

        <?php if ($totalCount > 0): ?>
          <div class="mb-3 mb-sm-4" style="max-height: 400px; max-height: min(400px, 50vh); overflow-y: auto;">

            <?php if ($lowStockCount > 0): ?>
              <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                  <i class="bi bi-exclamation-circle-fill text-warning me-2" style="font-size: 1.2rem;"></i>
                  <h6 class="mb-0 fw-bold" style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                    Low Stock Items (<?= $lowStockCount ?>)
                  </h6>
                </div>
                <ul class="list-group list-group-flush">
                  <?php foreach ($lowStockRows as $row): ?>
                    <li
                      class="list-group-item d-flex justify-content-between align-items-center border-0 mb-2 rounded-3 p-2 p-sm-3"
                      style="background: var(--card-bg-color);">
                      <div class="flex-grow-1 text-start me-2">
                        <span class="fw-bold d-block fs-6 fs-sm-5"
                          style="color: var(--text-color-dark); font-family: var(--primaryFont); word-break: break-word;">
                          <?= htmlspecialchars($row['ingredientName']) ?>
                        </span>
                        <small class="text-muted" style="font-size: 0.75rem;">
                          Threshold: <?= number_format($row['threshold'], 3) ?> <?= htmlspecialchars($row['unit']) ?>
                        </small>
                      </div>
                      <span class="badge rounded-pill px-2 px-sm-3 py-1 py-sm-2 flex-shrink-0" style="
                          background: color-mix(in srgb, #ffc107 15%, transparent);
                          color: var(--text-color-dark);
                          border: 1px solid color-mix(in srgb, #ffc107 30%, transparent);
                          font-family: var(--primaryFont);
                          font-size: 0.8rem;
                        ">
                        <?= number_format($row['quantity'], 3) ?> <?= htmlspecialchars($row['unit']) ?>
                      </span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if ($outOfStockCount > 0): ?>
              <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                  <i class="bi bi-calendar-x-fill text-secondary me-2" style="font-size: 1.2rem;"></i>
                  <h6 class="mb-0 fw-bold" style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                    Out of Stock Items (<?= $outOfStockCount ?>)
                  </h6>
                </div>
                <ul class="list-group list-group-flush">
                  <?php foreach ($outOfStockRows as $row): ?>
                    <li
                      class="list-group-item d-flex justify-content-between align-items-center border-0 mb-2 rounded-3 p-2 p-sm-3"
                      style="background: var(--card-bg-color);">
                      <div class="flex-grow-1 text-start me-2">
                        <span class="fw-bold d-block fs-6 fs-sm-5"
                          style="color: var(--text-color-dark); font-family: var(--primaryFont); word-break: break-word;">
                          <?= htmlspecialchars($row['ingredientName']) ?>
                        </span>
                        <small class="text-secondary" style="font-size: 0.75rem;">
                          No stock available
                        </small>
                      </div>
                      <span class="badge rounded-pill px-2 px-sm-3 py-1 py-sm-2 flex-shrink-0" style="
                          background: color-mix(in srgb, #6c757d 15%, transparent);
                          color: var(--text-color-dark);
                          border: 1px solid color-mix(in srgb, #6c757d 30%, transparent);
                          font-family: var(--primaryFont);
                          font-size: 0.8rem;
                        ">
                        0.000 <?= htmlspecialchars($row['unit']) ?>
                      </span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if ($expiredCount > 0): ?>
              <div class="mb-3">
                <div class="d-flex align-items-center mb-2">
                  <i class="bi bi-x-circle-fill text-danger me-2" style="font-size: 1.2rem;"></i>
                  <h6 class="mb-0 fw-bold" style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                    Expired Items (<?= $expiredCount ?>)
                  </h6>
                </div>
                <ul class="list-group list-group-flush">
                  <?php foreach ($expiredRows as $row): ?>
                    <?php
                    $expDate = new DateTime($row['expirationDate']);
                    $today = new DateTime();
                    $daysExpired = $today->diff($expDate)->days;
                    ?>
                    <li
                      class="list-group-item d-flex justify-content-between align-items-center border-0 mb-2 rounded-3 p-2 p-sm-3"
                      style="background: var(--card-bg-color);">
                      <div class="flex-grow-1 text-start me-2">
                        <span class="fw-bold d-block fs-6 fs-sm-5"
                          style="color: var(--text-color-dark); font-family: var(--primaryFont); word-break: break-word;">
                          <?= htmlspecialchars($row['ingredientName']) ?>
                        </span>
                        <small class="text-danger" style="font-size: 0.75rem;">
                          Expired: <?= $expDate->format('M d, Y') ?> (<?= $daysExpired ?>
                          day<?= $daysExpired > 1 ? 's' : '' ?> ago)
                        </small>
                      </div>
                      <span class="badge rounded-pill px-2 px-sm-3 py-1 py-sm-2 flex-shrink-0" style="
                          background: color-mix(in srgb, #dc3545 15%, transparent);
                          color: #dc3545;
                          border: 1px solid color-mix(in srgb, #dc3545 30%, transparent);
                          font-family: var(--primaryFont);
                          font-size: 0.8rem;
                        ">
                        <?= number_format($row['quantity'], 3) ?> <?= htmlspecialchars($row['unit']) ?>
                      </span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

          </div>
        <?php else: ?>
          <p class="text-center fs-6" style="font-family: var(--secondaryFont); color: var(--text-color-dark);">
            Your inventory levels are healthy. Keep up the good work!
          </p>
        <?php endif; ?>

        <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-3 justify-content-center">
          <button type="button" class="btn fw-bold px-3 px-sm-4 py-2 order-2 order-sm-1" data-bs-dismiss="modal" style="
              background: var(--card-bg-color); 
              color: var(--text-color-dark); 
              border: 2px solid var(--primary-color);
              border-radius: 10px; 
              font-family: var(--primaryFont); 
              letter-spacing: 1px; 
              transition: all 0.3s ease;
              min-width: 100px;
              font-size: 0.9rem;
            " onmouseover="
              this.style.background='var(--primary-color)'; 
              this.style.color='var(--text-color-light)';
              this.style.transform='translateY(-2px)';
              this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)';
            " onmouseout="
              this.style.background='var(--card-bg-color)'; 
              this.style.color='var(--text-color-dark)';
              this.style.transform='translateY(0)';
              this.style.boxShadow='none';
            " ontouchstart="
              this.style.background='var(--primary-color)'; 
              this.style.color='var(--text-color-light)';
            " ontouchend="
              setTimeout(() => {
                  this.style.background='var(--card-bg-color)'; 
                  this.style.color='var(--text-color-dark)';
              }, 150);
            ">
            DISMISS
          </button>

          <a href="inventory-management.php" class="btn fw-bold px-3 px-sm-4 py-2 order-1 order-sm-2" style="
              background: var(--text-color-dark); 
              color: white; 
              border: none;
              border-radius: 10px; 
              font-family: var(--primaryFont); 
              letter-spacing: 1px; 
              box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); 
              transition: all 0.3s ease;
              min-width: 130px;
              text-decoration: none;
              display: inline-block;
              text-align: center;
              font-size: 0.9rem;
            " onmouseover="
              this.style.background='var(--primary-color)';   
              this.style.transform='translateY(-2px)';    
              this.style.boxShadow='0 6px 12px rgba(0, 0, 0, 0.4)';
            " onmouseout="
              this.style.background='var(--text-color-dark)'; 
              this.style.transform='translateY(0)';
              this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)';
            " ontouchstart="
              this.style.background='var(--primary-color)';
            " ontouchend="
              setTimeout(() => {
                  this.style.background='var(--text-color-dark)';
              }, 150);
            ">
            <i class="bi bi-box-seam me-1 me-sm-2"></i>GO TO INVENTORY
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const stockModal = document.getElementById('stockModal');

    if (stockModal) {
      stockModal.addEventListener('show.bs.modal', function () {
        const modalContent = this.querySelector('.modal-content');
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'scale(0.9)';

        setTimeout(() => {
          modalContent.style.transition = 'all 0.3s ease';
          modalContent.style.opacity = '1';
          modalContent.style.transform = 'scale(1)';
        }, 10);
      });

      stockModal.addEventListener('click', function (e) {
        if (e.target === this) {
          if (window.innerWidth > 576) {
            bootstrap.Modal.getInstance(this).hide();
          }
        }
      });

      const inventoryLink = stockModal.querySelector('a[href*="inventory-management"]');
      if (inventoryLink) {
        inventoryLink.addEventListener('click', function () {
          console.log('User navigated to inventory management from stock alert');
        });
      }
    }
  });
</script>