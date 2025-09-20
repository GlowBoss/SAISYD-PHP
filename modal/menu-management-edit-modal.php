<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title modalText" id="editModalLabel">Edit Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body modalText">
        <!-- required for file uploads -->
        <form id="editItemForm" method="POST" enctype="multipart/form-data">

          <!-- Hidden Product ID -->
          <input type="hidden" name="product_id" id="edit_product_id">

          <div class="row">
            <!-- Left Column -->
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Item Name</label>
                <input type="text" class="form-control" name="item_name" id="edit_item_name" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Item Group</label>
                <select class="form-select" name="item_group" id="edit_item_group" required>
                  <?php
                  // Fetch all categories from the database
                  $categoriesQuery = "SELECT * FROM Categories";
                  $categoriesResult = mysqli_query($conn, $categoriesQuery);

                  while ($cat = mysqli_fetch_assoc($categoriesResult)) {
                    echo '<option value="' . $cat['categoryID'] . '">' . htmlspecialchars($cat['categoryName']) . '</option>';
                  }
                  ?>
                </select>
              </div>

              <!-- Availability Toggle Section -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Availability Status</label>
                <div class="d-flex align-items-center gap-3">
                  <label class="availability-toggle">
                    <input type="checkbox" id="availabilityToggle">
                    <span class="toggle-slider"></span>
                  </label>
                  <span id="availabilityStatus" class="status-badge status-available">Available</span>
                </div>
                <small class="text-muted">Toggle to enable/disable this item in POS and menu</small>
              </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="text" class="form-control" name="menu_price" id="edit_menu_price" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Size</label>
                <select class="form-select" name="menu_size" id="edit_menu_size">
                  <option value="12oz">12oz</option>
                  <option value="16oz">16oz</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Attachment</label>
                <input type="file" class="form-control" name="attachment" id="edit_current_image">
                <input type="text" class="form-control mt-1" id="edit_current_image_text" readonly>
              </div>
            </div>
          </div>

          <hr>
          <!-- Ingredients Section -->
          <h6 class="text-start mb-3">Ingredients</h6>
          <div id="edit-ingredients-container">
            <!-- JS will populate rows here -->
          </div>

          <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="edit-add-ingredient">+</button>

          <!-- Modal Actions -->
          <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-de btn-del me-2" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" class="btn btn-sm">SAVE</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const availabilityToggle = document.getElementById('availabilityToggle');
    const availabilityStatus = document.getElementById('availabilityStatus');
    let currentProductId = null;

    // Open modal with product info
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentProductId = this.getAttribute('data-id');
            const isAvailable = this.getAttribute('data-available') === '1';

            if (availabilityToggle) {
                availabilityToggle.checked = isAvailable;
                updateAvailabilityStatus(isAvailable);
            }
        });
    });

    // Toggle availability directly
    if (availabilityToggle) {
        availabilityToggle.addEventListener('change', function() {
            const isChecked = this.checked;
            updateAvailabilityStatus(isChecked);

            if (currentProductId) {
                updateProductAvailability(currentProductId, isChecked ? 1 : 0);
            }
        });
    }

    // Update text + badge inside modal
    function updateAvailabilityStatus(isAvailable) {
        if (availabilityStatus) {
            availabilityStatus.textContent = isAvailable ? 'Available' : 'Unavailable';
            availabilityStatus.className = 'status-badge ' + 
                (isAvailable ? 'status-available' : 'status-unavailable');
        }
    }

    // AJAX update without page reload
    function updateProductAvailability(productId, newAvailability) {
        fetch('menu-management.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `btnToggleAvailability=1&productID=${productId}&newAvailability=${newAvailability}`
        })
        .then(response => response.text())
        .then(() => {
            // Update product card outside modal
            const productCard = document.querySelector(`.edit-btn[data-id="${productId}"]`).closest('.menu-item');
            const badge = productCard.querySelector('.status-badge');

            if (newAvailability == 1) {
                productCard.classList.remove('unavailable');
                badge.textContent = 'Available';
                badge.className = 'status-badge status-available';
            } else {
                productCard.classList.add('unavailable');
                badge.textContent = 'Unavailable';
                badge.className = 'status-badge status-unavailable';
            }

            // ✅ Show toast feedback
            const toast = document.getElementById('updateToast');
            if (toast) {
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            }
        })
        .catch(err => console.error('Error updating availability:', err));
    }
});
</script>
