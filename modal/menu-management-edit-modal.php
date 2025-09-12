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
