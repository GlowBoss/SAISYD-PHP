<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title modalText" id="editModalLabel">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modalText">
                 <form id="editItemForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Item name</label>
                                <input type="text" class="form-control" name="item_name" value="Amerikano">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Select Category</label>
                                <select class="form-select" name="item_group">
                                    <option selected value="coffee">Coffee</option>
                                    <option value="tea">Tea</option>
                                    <option value="food">Food</option>
                                    <option value="beverage">Beverage</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Price</label>
                                <input type="text" class="form-control" name="menu_price" value="140">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Size</label>
                                <select class="form-select" name="menu_size">
                                    <option value="None" selected>-- None --</option>
                                    <option value="12oz">12oz</option>
                                    <option value="16oz">16oz</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Attachment</label>
                                <input type="file" class="form-control" name="attachment">
                            </div>
                        </div>
                    </div>

                    <!-- Ingredients Section -->
                     <!-- Yung value is sample data lang pag nag edit -->
                    <div class="mt-4">
                        <h6 class="text-start">Ingredients</h6>
                        <div id="edit-ingredients-container">
                            <div class="row g-2 mb-2 ingredient-row">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="ingredient_name[]" value="Test" placeholder="Name" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control" name="ingredient_qty[]" value="test" placeholder="Quantity" required >
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="ingredient_unit[]"
                                        placeholder="Unit (pcs, kg, ml)" required>
                                </div>
                                <div class="col-md-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-sm remove-ingredient" placeholder required>&times;</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="edit-add-ingredient" class="btn btn-outline-primary btn-sm mt-2">+
                        </button>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-sm btn-del me-2" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn btn-sm">SAVE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>