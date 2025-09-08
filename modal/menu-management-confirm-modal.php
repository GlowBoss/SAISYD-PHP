<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title modalText" id="confirmModalLabel">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modalText">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Item Name</label>
                                <input type="text" class="form-control" name="productName" placeholder="Enter item name"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Item Group</label>
                                <select class="form-select" name="categoryID" required>
                                    <option disabled selected>Select Category</option>
                                    <?php
                                    $getCategories = mysqli_query($conn, "SELECT * FROM categories");
                                    while ($cat = mysqli_fetch_assoc($getCategories)) {
                                        echo '<option value="' . $cat['categoryID'] . '">' . $cat['categoryName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Price</label>
                                <input type="text" class="form-control" name="price" placeholder="Enter price" required>
                            </div>

                            <!-- (name="availableQuantity") -->
                            <div class="mb-3">
                                <label class="form-label">Available Quantity</label>
                                <input type="number" class="form-control" 
                                    placeholder="Enter stock" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Attachment</label>
                                <input type="file" class="form-control" name="image">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-start mb-3">Ingredients</h6>
                    <div id="ingredients-container">
                        <div class="row g-2 mb-2 ingredient-row">
                            <div class="col-md-5">
                                <input type="text" class="form-control ingredient-search"
                                    placeholder="Search Ingredient" required>
                                <input type="hidden" name="ingredientID[]" class="ingredient-id">
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="requiredQuantity[]"
                                    placeholder="Quantity" required>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select measurement-select" name="measurementUnit[]" required>
                                    <!-- dito nagdagdag lang me ng mga iba pang option pasabi nalang if tatanggalin ko yung iba -->
                                    <option value="" disabled selected>Select Unit</option>
                                    <option class="option-hover" value="pcs">pcs</option>
                                    <option class="option-hover" value="kg">kg</option>
                                    <option class="option-hover"  value="g">g</option>
                                    <option class="option-hover"  value="ml">ml</option>
                                    <option  class="option-hover" value="L">L</option>
                                    <option  class="option-hover" value="oz">oz</option>
                                    <option  class="option-hover" value="pack">pack</option>
                                    <option  class="option-hover" value="pack">pack</option>
                                    <option  value="box">box</option>
                                </select>
                                <input type="text" class="form-control mt-2 d-none custom-unit" name="customUnit[]"
                                    placeholder="Enter custom unit">
                            </div>
                            <div class="col-md-1 d-flex align-items-center">
                                <button type="button" class="btn btn-sm remove-ingredient">&times;</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-ingredient">+</button>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-de btn-del me-2" data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" name="btnAddProduct" class="btn btn-sm">ADD ITEM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>