<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--btn-hover1) !important;
        box-shadow: 0 0 0 0.2rem rgba(46, 26, 0, 0.15) !important;
    }

    .form-select option {
        background: var(--card-bg-color);
        color: var(--text-color-dark);
    }

    .autocomplete-dropdown {
        border-top: none !important;
    }

    .autocomplete-item {
        padding: 12px 15px;
        cursor: pointer;
        border-bottom: 1px solid rgba(196, 162, 119, 0.2);
        font-family: var(--secondaryFont);
        color: var(--text-color-dark);
        transition: background-color 0.2s ease;
    }

    .autocomplete-item:hover,
    .autocomplete-item.selected {
        background-color: var(--primary-color);
        color: var(--text-color-light);
    }

    .autocomplete-item:last-child {
        border-bottom: none;
        border-radius: 0 0 10px 10px;
    }

    .new-ingredient-item {
        font-style: italic;
        color: var(--text-color-dark);
        font-weight: bold;
    }

    .new-ingredient-item:hover {
        background-color: var(--primary-color);
        color: var(--text-color-light);
    }
</style>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" data-bs-backdrop="true" tabindex="-1" aria-labelledby="editItemModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h1 class="modal-title fs-4 fw-bold" id="addItemModalLabel"
                    style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
                    Edit Inventory Item
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="filter: invert(50%);"></button>
            </div>

            <!-- Body -->
            <form id="updateForm">
                <div class="modal-body">

                    <input type="hidden" id="inventoryID" name="inventoryID" value="">
                    <input type="hidden" id="ingredientID" name="ingredientID" value="">
                    <div class="row g-4">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Ingredient Search Input -->
                            <div class="mb-4 position-relative">
                                <label for="ingredientName" class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Ingredient <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" class="form-control" id="ingredientNameEdit" name="ingredientNameEdit"
                                    required placeholder="Type ingredient name..." autocomplete="on" style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                              font-family: var(--secondaryFont); background: var(--card-bg-color);
                                              color: var(--text-color-dark); padding: 12px;">

                                <!-- Autocomplete Dropdown -->
                                <div id="ingredientDropdown" class="autocomplete-dropdown position-absolute w-100"
                                    style=" left: 0; z-index: 1050; max-height: 200px; overflow-y: auto;
                                            background: var(--card-bg-color); border: 2px solid var(--primary-color); 
                                            border-top: none; border-radius: 0 0 10px 10px; display: none; ">
                                </div>

                                <div class="form-text mt-2"
                                    style="font-family: var(--secondaryFont); color: var(--gray); font-size: 0.85rem;">
                                    Start typing to search existing ingredients or add new one
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="mb-4">
                                <label for="quantity" class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Quantity <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="number" class="form-control" id="quantityEdit" name="quantityEdit" min="0"
                                    step="0.01" required placeholder="Enter quantity" style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                              font-family: var(--secondaryFont); background: var(--card-bg-color);
                                              color: var(--text-color-dark); padding: 12px;">
                            </div>

                            <!-- Unit -->
                            <div class="mb-4">
                                <label for="unit" class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Unit <span style="color: #dc3545;">*</span>
                                </label>
                                <select class="form-select" id="unitEdit" name="unitEdit" required style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                               font-family: var(--secondaryFont); background: var(--card-bg-color);
                                               color: var(--text-color-dark); padding: 12px;">
                                    <option value="">Select Unit</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="g">Gram (g)</option>
                                    <option value="lbs">Pounds (lbs)</option>
                                    <option value="oz">Ounce (oz)</option>
                                    <option value="L">Liter (L)</option>
                                    <option value="ml">Milliliter (ml)</option>
                                    <option value="pcs">Pieces (pcs)</option>
                                    <option value="bags">Bags</option>
                                    <option value="bottles">Bottles</option>
                                    <option value="cans">Cans</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Expiration Date -->
                            <div class="mb-4">
                                <label for="expirationDate" class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Expiration Date <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="date" class="form-control" id="expirationEdit" name="expirationEdit"
                                    required style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                              font-family: var(--secondaryFont); background: var(--card-bg-color);
                                              color: var(--text-color-dark); padding: 12px;">
                            </div>

                            <!-- Threshold -->
                            <div class="mb-4">
                                <label for="threshold" class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Low Stock Threshold <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="number" class="form-control" id="thresholdEdit" name="thresholdEdit" min="0"
                                    step="0.01" required placeholder="Enter threshold amount" style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                              font-family: var(--secondaryFont); background: var(--card-bg-color);
                                              color: var(--text-color-dark); padding: 12px;">
                                <div class="form-text mt-2"
                                    style="font-family: var(--secondaryFont); color: var(--gray); font-size: 0.85rem;">
                                    System will alert when stock falls below this amount
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <!-- Cancel Button -->
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

                        <!-- Edit Item Button -->
                        <button type="submit" class="btn fw-bold px-4 py-2" style="
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
                            <i class="bi bi-plus-circle-fill me-2"></i>UPDATE ITEM
                        </button>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Existing ingredients data 
        const ingredients = [
            <?php foreach ($ingredients as $ingredient): ?>
            {
                    ingredientID: <?= $ingredient['ingredientID'] ?>,
                    ingredientName: '<?= addslashes($ingredient['ingredientName']) ?>'
                },
            <?php endforeach; ?>
        ];

        const ingredientInput = document.getElementById('ingredientName');
        const ingredientIDInput = document.getElementById('ingredientID');
        const dropdown = document.getElementById('ingredientDropdown');
        let selectedIndex = -1;

        // Show/hide dropdown and filter ingredients
        ingredientInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();

            if (query === '') {
                hideDropdown();
                ingredientIDInput.value = '';
                return;
            }

            // Filter existing ingredients
            const filtered = ingredients.filter(ingredient =>
                ingredient.ingredientName.toLowerCase().includes(query)
            );

            showDropdown(filtered, query);
            selectedIndex = -1;
        });

        // Handle keyboard navigation
        ingredientInput.addEventListener('keydown', function (e) {
            const items = dropdown.querySelectorAll('.autocomplete-item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                updateSelection(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                updateSelection(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    selectItem(items[selectedIndex]);
                }
            } else if (e.key === 'Escape') {
                hideDropdown();
            }
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!ingredientInput.contains(e.target) && !dropdown.contains(e.target)) {
                hideDropdown();
            }
        });

        function showDropdown(filtered, query) {
            dropdown.innerHTML = '';

            // Show existing ingredients
            filtered.forEach(ingredient => {
                const item = createDropdownItem(
                    ingredient.ingredientName,
                    false,
                    ingredient.ingredientID
                );
                dropdown.appendChild(item);
            });

            // Show "Add new ingredient" option if query doesn't exactly match any existing ingredient
            const exactMatch = filtered.some(ingredient =>
                ingredient.ingredientName.toLowerCase() === query.toLowerCase()
            );

            if (!exactMatch && query.length > 0) {
                const newItem = createDropdownItem(
                    `Add new: "${query}"`,
                    true,
                    null,
                    query
                );
                dropdown.appendChild(newItem);
            }

            if (dropdown.children.length > 0) {
                dropdown.style.display = 'block';
            } else {
                hideDropdown();
            }
        }

        function createDropdownItem(text, isNew, ingredientID, newIngredientName = null) {
            const item = document.createElement('div');
            item.className = 'autocomplete-item' + (isNew ? ' new-ingredient-item' : '');
            item.textContent = text;
            item.setAttribute('data-ingredient-id', ingredientID || '');
            item.setAttribute('data-ingredient-name', newIngredientName || text);
            item.setAttribute('data-is-new', isNew ? 'true' : 'false');
            item.setAttribute('data-ingredient-name', newIngredientName || text);


            item.addEventListener('click', function () {
                selectItem(this);
            });

            return item;
        }

        function selectItem(item) {
            const isNew = item.getAttribute('data-is-new') === 'true';
            const ingredientName = item.getAttribute('data-ingredient-name');
            const ingredientID = item.getAttribute('data-ingredient-id');

            ingredientInput.value = ingredientName;
            ingredientIDInput.value = isNew ? '' : ingredientID;

            // Add a data attribute to indicate if this is a new ingredient
            ingredientInput.setAttribute('data-is-new-ingredient', isNew ? 'true' : 'false');

            hideDropdown();
            selectedIndex = -1;
        }

        function updateSelection(items) {
            items.forEach((item, index) => {
                item.classList.toggle('selected', index === selectedIndex);
            });
        }

        function hideDropdown() {
            dropdown.style.display = 'none';
            selectedIndex = -1;
        }


    });
</script>