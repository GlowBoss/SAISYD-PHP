<?php
// db connection
$conn = new mysqli("localhost", "root", "", "saisyd");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// query low stock items (adjust threshold as needed, e.g. < 10)
$sql = "
    SELECT i.ingredientName, inv.quantity, inv.unit
    FROM ingredients i
    INNER JOIN inventory inv ON i.ingredientID = inv.ingredientID
    WHERE inv.quantity < 10
    ORDER BY inv.quantity ASC
";

$result = $conn->query($sql);

$count = $result->num_rows;
?>

<!-- Low Stock Alert Modal -->
<div class="modal fade" id="stockModal" data-bs-backdrop="true" tabindex="-1" aria-labelledby="stockModalLabel"
  aria-hidden="true" data-lowstock-count="<?= $count ?>">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color); max-height: 90vh;">

      <!-- Header -->
      <div class="modal-header border-0 pb-2 px-3 px-sm-4">
        <h1 class="modal-title fs-5 fs-sm-4 fw-bold" id="stockModalLabel"
          style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
          Low Stock Alert
        </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
          style="filter: invert(50%);"></button>
      </div>

      <!-- Body -->
      <div class="modal-body px-3 px-sm-4">
        <!-- Alert Icon -->
        <div class="text-center mb-3 mb-sm-4">
          <?php if ($count > 0): ?>
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 3rem; color: #dc3545;"></i>
          <?php else: ?>
            <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #198754;"></i>
          <?php endif; ?>
        </div>

        <!-- Message -->
        <div class="text-center mb-3 mb-sm-4">
          <?php if ($count > 0): ?>
            <p class="mb-2 fs-6 fs-sm-5"
              style="font-family: var(--secondaryFont); color: var(--text-color-dark); line-height: 1.4;">
              The following items are running low on stock:
            </p>
            <p class="fw-bold mb-2 fs-6 fs-sm-5" style="font-family: var(--primaryFont); color: var(--text-color-dark);">
              <strong><?= $count ?> Item<?= $count > 1 ? 's' : '' ?> Need Attention</strong>
            </p>
          <?php else: ?>
            <p class="mb-2 fs-6 fs-sm-5"
              style="font-family: var(--secondaryFont); color: var(--text-color-dark); line-height: 1.4;">
              All inventory items are well stocked!
            </p>
            <p class="fw-bold mb-2 fs-6 fs-sm-5" style="font-family: var(--primaryFont); color: var(--text-color-dark);">
              <strong>No Low Stock Items</strong>
            </p>
          <?php endif; ?>
        </div>

        <!-- Stock Items List -->
        <?php if ($count > 0): ?>
          <div class="mb-3 mb-sm-4" style="max-height: 250px; max-height: min(250px, 40vh); overflow-y: auto;">
            <ul class="list-group list-group-flush">
              <?php while ($row = $result->fetch_assoc()): ?>
                <li
                  class="list-group-item d-flex justify-content-between align-items-center border-0 mb-2 rounded-3 p-2 p-sm-3"
                  style="background: var(--card-bg-color);">
                  <div class="flex-grow-1 text-start me-2">
                    <span class="fw-bold d-block fs-6 fs-sm-5"
                      style="color: var(--text-color-dark); font-family: var(--primaryFont); word-break: break-word;">
                      <?= htmlspecialchars($row['ingredientName']) ?>
                    </span>
                  </div>
                  <span class="badge rounded-pill px-2 px-sm-3 py-1 py-sm-2 flex-shrink-0" style="
                      background: color-mix(in srgb, var(--primary-color) 15%, transparent);
                      color: var(--text-color-dark);
                      border: 1px solid color-mix(in srgb, #dc3545 30%, transparent);
                      font-family: var(--primaryFont);
                      font-size: 0.8rem;
                    ">
                    <?= htmlspecialchars($row['quantity']) . ' ' . htmlspecialchars($row['unit']) ?>
                  </span>


                </li>
              <?php endwhile; ?>
            </ul>
          </div>
        <?php else: ?>
          <p class="text-center fs-6" style="font-family: var(--secondaryFont); color: var(--text-color-dark);">
            Your inventory levels are healthy. Keep up the good work!
          </p>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-3 justify-content-center">
          <!-- Dismiss Button -->
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

          <!-- Go to Inventory Button -->
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
            <i class="bi bi-box-seam me-1 me-sm-2"></i><span class="d-none d-sm-inline">GO TO </span>INVENTORY
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Additional responsive styles */
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

  /* Touch device optimizations */
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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Handle smooth modal animations
    const stockModal = document.getElementById('stockModal');

    if (stockModal) {
      stockModal.addEventListener('show.bs.modal', function () {
        // Add any custom animations when modal shows
        const modalContent = this.querySelector('.modal-content');
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'scale(0.9)';

        setTimeout(() => {
          modalContent.style.transition = 'all 0.3s ease';
          modalContent.style.opacity = '1';
          modalContent.style.transform = 'scale(1)';
        }, 10);
      });

      // Prevent modal from closing on backdrop click for mobile
      stockModal.addEventListener('click', function (e) {
        if (e.target === this) {
          // Allow backdrop click on larger screens only
          if (window.innerWidth > 576) {
            bootstrap.Modal.getInstance(this).hide();
          }
        }
      });

      // Add click tracking for analytics (optional)
      const inventoryLink = stockModal.querySelector('a[href*="inventory-management"]');
      if (inventoryLink) {
        inventoryLink.addEventListener('click', function () {
          console.log('User navigated to inventory management from stock alert');
        });
      }
    }
  });
</script>

<?php $conn->close(); ?>