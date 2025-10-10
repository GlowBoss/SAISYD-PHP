<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h1 class="modal-title fs-4 fw-bold" id="confirmModalLabel"
                    style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
                    Add Product
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="filter: invert(50%);"></button>
            </div>

            <!-- Body -->
            <div class="modal-body modalText text-start">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-4">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Item Name <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" class="form-control" name="productName" required
                                    placeholder="Enter item name" style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                          font-family: var(--secondaryFont); background: var(--card-bg-color);
                                          color: var(--text-color-dark); padding: 12px;">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Item Group <span style="color: #dc3545;">*</span>
                                </label>
                                <select class="form-select" name="categoryID" required style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                          font-family: var(--secondaryFont); background: var(--card-bg-color);
                                          color: var(--text-color-dark); padding: 12px;">
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

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Price <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" class="form-control" name="price" required placeholder="Enter price"
                                    style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                          font-family: var(--secondaryFont); background: var(--card-bg-color);
                                          color: var(--text-color-dark); padding: 12px;">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Attachment
                                </label>
                                <input type="file" class="form-control" name="image" id="productImage" accept="image/*"
                                    style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                          font-family: var(--secondaryFont); background: var(--card-bg-color);
                                          color: var(--text-color-dark); padding: 12px;">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-start mb-3 fw-bold"
                        style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                        Ingredients
                    </h6>

                    <div id="ingredients-container" style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
                        <div class="row g-2 mb-2 ingredient-row">
                            <div class="col-md-5 position-relative">
                                <input type="text" class="form-control ingredient-search"
                                    placeholder="Search Ingredient" required style="border: 2px solid var(--primary-color); border-radius: 10px; 
          font-family: var(--secondaryFont); background: var(--card-bg-color);
          color: var(--text-color-dark); padding: 12px;">
                                <input type="hidden" name="ingredientID[]" class="ingredient-id">
                                <button type="button" class="cancel-search" style="position:absolute; right:8px; top:50%; transform:translateY(-50%);
          border:none; background:none; color:#333; font-size:18px; display:none; cursor:pointer;">
                                    &times;
                                </button>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control" name="requiredQuantity[]"
                                    placeholder="Quantity" step="any" required style="border: 2px solid var(--primary-color); border-radius: 10px; 
          font-family: var(--secondaryFont); background: var(--card-bg-color);
          color: var(--text-color-dark); padding: 12px;">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select measurement-select" name="measurementUnit[]" required style="border: 2px solid var(--primary-color); border-radius: 10px; 
          font-family: var(--secondaryFont); background: var(--card-bg-color);
          color: var(--text-color-dark); padding: 12px;">
                                    <option value="" disabled selected>Select Unit</option>
                                    <option value="pcs">Pieces (pcs)</option>
                                    <option value="box">Box</option>
                                    <option value="pack">Pack</option>
                                    <option value="g">Gram (g)</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="oz">Ounce (oz)</option>
                                    <option value="ml">Milliliter (ml)</option>
                                    <option value="L">Liter (L)</option>
                                    <option value="pump">Pump</option>
                                    <option value="tbsp">Tablespoon (tbsp)</option>
                                    <option value="tsp">Teaspoon (tsp)</option>
                                </select>

                                <input type="text" class="form-control mt-2 d-none custom-unit" name="customUnit[]"
                                    placeholder="Enter custom unit" style="border: 2px solid var(--primary-color); border-radius: 10px; 
          font-family: var(--secondaryFont); background: var(--card-bg-color);
          color: var(--text-color-dark); padding: 12px;">

                            </div>
                            <div class="col-md-1 d-flex justify-content-center align-items-center">
                                <button type="button"
                                    class="btn btn-sm btn-del remove-ingredient d-flex justify-content-center align-items-center"
                                    style="border-radius: 10px; width: 38px; height: 38px; transition: all 0.3s ease;">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Add Ingredient Button -->
                    <div class="text-center mt-2">
                        <button type="button" class="btn d-flex align-items-center justify-content-center mx-auto"
                            id="add-modal-ingredient"
                            style="background: transparent; border: none; color: var(--text-color-dark); transition: all 0.3s ease;">
                            <i class="bi bi-plus-circle-fill"
                                style="font-size: 2rem; color: var(--text-color-dark); transition: all 0.3s ease;"></i>
                        </button>
                    </div>


                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <button type="button" class="btn fw-bold px-4 py-2" data-bs-dismiss="modal" style="
                                    background: var(--card-bg-color); 
                                    color: var(--text-color-dark); 
                                    border: 2px solid var(--primary-color);
                                    border-radius: 10px; 
                                    font-family: var(--primaryFont); 
                                    letter-spacing: 1px; 
                                    transition: all 0.3s ease;
                                    min-width: 120px;
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
                                ">
                            CANCEL
                        </button>

                        <button type="submit" name="btnAddProduct" class="btn fw-bold px-4 py-2" style="
                                    background: var(--text-color-dark); 
                                    color: var(--text-color-light); 
                                    border: none;
                                    border-radius: 10px; 
                                    font-family: var(--primaryFont); 
                                    letter-spacing: 1px; 
                                    box-shadow: 0 4px 8px rgba(196, 162, 119, 0.3); 
                                    transition: all 0.3s ease;
                                    min-width: 120px;
                                " onmouseover="
                                    this.style.background='var(--primary-color)'; 
                                    this.style.transform='translateY(-2px)';
                                    this.style.boxShadow='0 6px 12px rgba(196, 162, 119, 0.4)';
                                " onmouseout="
                                    this.style.background='var(--text-color-dark)'; 
                                    this.style.transform='translateY(0)';
                                    this.style.boxShadow='0 4px 8px rgba(196, 162, 119, 0.3)';
                                ">
                            <i class="bi bi-plus-circle-fill me-2"></i>ADD ITEM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    #ingredients-container {
        max-height: 250px;
        overflow-y: auto;
        padding-right: 5px;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-color) var(--card-bg-color);
    }

    /* For Chrome, Edge, Safari */
    #ingredients-container::-webkit-scrollbar {
        width: 8px;
    }

    #ingredients-container::-webkit-scrollbar-track {
        background: var(--card-bg-color);
        border-radius: 10px;
    }

    #ingredients-container::-webkit-scrollbar-thumb {
        background-color: var(--primary-color);
        border-radius: 10px;
        border: 2px solid var(--card-bg-color);
    }

    #ingredients-container::-webkit-scrollbar-thumb:hover {
        background-color: var(--text-color-dark);
    }

    #add-modal-ingredient:hover i {
        color: var(--primary-color);
        transform: scale(1.15);
    }



    .remove-ingredient:hover {
        background: var(--primary-color);
        color: var(--text-color-light);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Custom Delete Button */
    .btn-del {
        background: rgba(231, 76, 60, 0.15);
        color: #e74c3c;
        border: none;
    }

    .btn-del:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        background-color: #e74c3c;
        color: var(--text-color-light);
    }
</style>