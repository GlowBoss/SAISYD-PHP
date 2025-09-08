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
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Item name</label>
                <input type="text" class="form-control" name="item_name" id="edit_item_name">
              </div>

              <div class="mb-3">
                <label class="form-label">Select Category</label>
                <select class="form-select" name="item_group" id="edit_item_group">
                  <option value="coffee">Coffee</option>
                  <option value="tea">Tea</option>
                  <option value="food">Food</option>
                  <option value="beverage">Beverage</option>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="text" class="form-control" name="menu_price" id="edit_menu_price">
              </div>

              <div class="mb-3">
                <label class="form-label">Size</label>
                <select class="form-select" name="menu_size" id="edit_menu_size">
                  <option value="">-- None --</option>
                  <option value="12oz">12oz</option>
                  <option value="16oz">16oz</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Attachment</label>
                <input type="file" class="form-control" name="attachment" id="edit_attachment">
              </div>
            </div>
          </div>

          <!-- Ingredients Section -->
          <div class="mt-4">
            <h6 class="text-start">Ingredients</h6>
            <div id="edit-ingredients-container">
              <!-- JS will populate rows here -->
            </div>
            <button type="button" id="edit-add-ingredient" class="btn btn-outline-primary btn-sm mt-2">+</button>
          </div>

          <!-- Modal Actions -->
          <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-sm me-2" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" class="btn btn-sm">SAVE</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


