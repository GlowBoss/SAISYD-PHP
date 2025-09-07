<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title modalText" id="confirmModalLabel">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modalText">
                <form id="editItemForm">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Item Name</label>
                                <input type="text" class="form-control" name="item_name" placeholder="Enter item name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item Group</label>
                                <select class="form-select" name="item_group">
                                    <option disabled selected>Select Category</option>
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
                                <input type="text" class="form-control" name="menu_price" placeholder="Enter price">
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


                    <hr>
                    <h6 class="text-start mb-3">Ingredients</h6>
                    <div id="ingredients-container">
                        <div class="row g-2 mb-2 ingredient-row">
                            <div class="col-md-5">
                                <input type="text" class="form-control" name="ingredient_name[]" placeholder="Name" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="ingredient_qty[]" placeholder="Quantity" required>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="ingredient_unit[]"
                                    placeholder="Unit (pcs, kg, ml)" required>
                            </div>
                            <div class="col-md-1 d-flex align-items-center">
                                <button type="button" class="btn btn-sm remove-ingredient" placeholder
                                    required>&times;</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-ingredient">
                        +
                    </button>


                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-de btn-del me-2" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn btn-sm">ADD ITEM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>