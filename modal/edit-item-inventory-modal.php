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

    /* Unit Mismatch Warning Styles */
    .unit-warning {
        background-color: #fff3cd;
        border: 2px solid #ffc107;
        border-radius: 8px;
        padding: 10px 15px;
        margin-top: 10px;
        display: none;
    }

    .unit-warning.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    .unit-warning i {
        color: #856404;
        font-size: 1.2rem;
        margin-right: 8px;
    }

    .unit-warning-text {
        color: #856404;
        font-weight: 600;
        font-family: var(--secondaryFont);
        font-size: 0.9rem;
    }

    #ingredientNameEdit {
        pointer-events: none;
        background-color: #e0e0e0;
        color: #6c6c6c;
        border-color: #c0c0c0;
        cursor: not-allowed;

    }

    #ingredientNameEdit:focus {
        border-color: #c0c0c0;
        box-shadow: none;
        outline: none;
    }

    #ingredientDropdownEdit:focus {
        border-color: var(--primary-color) !important;
        box-shadow: none;
    }

    #quantityEdit:focus {
        border-color: var(--primary-color) !important;
        box-shadow: none;
    }

    #unitEdit:focus {
        border-color: var(--primary-color) !important;
        box-shadow: none;
    }

    #expirationEdit:focus {
        border-color: var(--primary-color) !important;
        box-shadow: none;
    }

    #thresholdEdit:focus {
        border-color: var(--primary-color) !important;
        box-shadow: none;
    }


    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
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
                    <input type="hidden" id="ingredientIDEdit" name="ingredientIDEdit" value="">
                    <div class="row g-4">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Ingredient Search Input -->
                            <div class="mb-4 position-relative">
                                <label for="ingredientName" class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Ingredient
                                </label>
                                <input type="text" class="form-control" id="ingredientNameEdit"
                                    name="ingredientNameEdit" required placeholder="Type ingredient name..."
                                    autocomplete="on" readonly style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                    font-family: var(--secondaryFont); 
                                    background-color: #e0e0e0; 
                                    color: #6c6c6c;           
                                    padding: 12px;
                                    cursor: not-allowed;">

                                <!-- Autocomplete Dropdown -->
                                <div id="ingredientDropdownEdit" class="autocomplete-dropdown position-absolute w-100"
                                    style=" left: 0; z-index: 1050; max-height: 200px; overflow-y: auto;
                                            background: var(--card-bg-color); border: 2px solid var(--primary-color); 
                                            border-top: none; border-radius: 0 0 10px 10px; display: none; ">
                                </div>


                            </div>

                            <!-- Quantity -->
                            <div class="mb-4">
                                <label for="quantity" class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Quantity 
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
                                    Unit 
                                </label>
                                <select class="form-select" id="unitEdit" name="unitEdit" required style="border: 2px solid var(--primary-color); border-radius: 10px; 
                                               font-family: var(--secondaryFont); background: var(--card-bg-color);
                                               color: var(--text-color-dark); padding: 12px;">
                                    <option value="">Select Unit</option>
                                    <optgroup label="Weight">
                                        <option value="kg">Kilogram (kg)</option>
                                        <option value="g">Gram (g)</option>
                                        <option value="lbs">Pounds (lbs)</option>
                                        <option value="oz">Ounce (oz)</option>
                                    </optgroup>
                                    <optgroup label="Volume - Metric">
                                        <option value="L">Liter (L)</option>
                                        <option value="ml">Milliliter (ml)</option>
                                    </optgroup>
                                    <optgroup label="Volume - Coffee Shop">
                                        <option value="pump">Pump</option>
                                        <option value="tbsp">Tablespoon (tbsp)</option>
                                        <option value="tsp">Teaspoon (tsp)</option>
                                        <option value="cup">Cup</option>
                                        <option value="shot">Shot</option>
                                    </optgroup>
                                    <optgroup label="Count">
                                        <option value="pcs">Pieces (pcs)</option>
                                        <option value="bags">Bags</option>
                                        <option value="bottles">Bottles</option>
                                        <option value="cans">Cans</option>
                                        <option value="packs">Packs</option>
                                        <option value="boxes">Boxes</option>
                                    </optgroup>
                                </select>

                                <!-- Unit Mismatch Warning -->
                                <div class="unit-warning" id="unitWarningEdit">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <span class="unit-warning-text" id="unitWarningTextEdit"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Expiration Date -->
                            <div class="mb-4">
                                <label for="expirationDate" class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Expiration Date 
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
                                    Low Stock Threshold 
                                </label>
                                <input type="number" class="form-control" id="thresholdEdit" name="thresholdEdit"
                                    min="0" step="0.01" required placeholder="Enter threshold amount" style="border: 2px solid var(--primary-color); border-radius: 10px; 
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

                        <!-- Update Item Button -->
                        <button type="submit" class="btn fw-bold px-4 py-2" id="updateItemBtn" style="
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
                            <i class="bi bi-check-circle-fill me-2"></i>UPDATE ITEM
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unit Mismatch Error Modal -->
<div class="modal fade" id="unitMismatchModal" tabindex="-1" aria-labelledby="unitMismatchModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">
            <div class="modal-header border-0 pb-2"
                style="background: linear-gradient(135deg, #ff6b6b 0%, #dc3545 100%);">
                <h5 class="modal-title fw-bold text-white" id="unitMismatchModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Unit Mismatch Error
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-x-circle-fill" style="font-size: 4rem; color: #dc3545;"></i>
                </div>
                <h5 class="fw-bold mb-3" style="color: var(--text-color-dark); font-family: var(--primaryFont);">
                    Incompatible Unit Selected
                </h5>
                <p class="mb-2" style="color: var(--text-color-dark); font-family: var(--secondaryFont);"
                    id="mismatchMessage">
                    <!-- Dynamic message here -->
                </p>
                <div class="alert alert-warning mt-3" role="alert" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Allowed units:</strong> <span id="allowedUnitsText"></span>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn fw-bold px-4 py-2" id="fixUnitMismatchBtn" style="
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
                    <i class="bi bi-tools me-2"></i>FIX THIS NOW
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ==================== UNIT VALIDATION SYSTEM ====================

        // Ingredient type detection based on keywords
        function detectIngredientType(ingredientName) {
            const name = ingredientName.toLowerCase();

            // LIQUIDS - anything liquid/fluid
            const liquidKeywords = ['milk', 'water', 'syrup', 'juice', 'espresso',
                'cream', 'latte', 'tea', 'oil', 'sauce', 'broth', 'soda',
                'coke', 'sprite', 'drink', 'beverage', 'liquid', 'coffee',
                'mocha', 'vanilla', 'caramel', 'hazelnut', 'chocolate sauce'];

            // SOLIDS - dry/powder/granular/solid items
            const solidKeywords = ['sugar', 'flour', 'powder', 'salt', 'beans', 'leaves',
                'cocoa', 'chocolate', 'spice', 'grain', 'rice', 'cheese',
                'butter', 'matcha', 'taro', 'ube', 'caramel powder', 'cookie',
                'biscuit', 'pastry', 'cake', 'bread'];

            // COUNTABLE - by pieces/whole items
            const countableKeywords = ['egg', 'potato', 'fries', 'cup', 'piece', 'slice',
                'patty', 'bun', 'bottle', 'can', 'bag', 'pack', 'box',
                'sachet', 'tablet', 'capsule', 'carrot', 'muffin', 'donut',
                'croissant', 'sandwich', 'wrap'];

            // Check which category (priority: countable > liquid > solid)
            if (countableKeywords.some(keyword => name.includes(keyword))) {
                return 'countable';
            }
            if (liquidKeywords.some(keyword => name.includes(keyword))) {
                return 'liquid';
            }
            if (solidKeywords.some(keyword => name.includes(keyword))) {
                return 'solid';
            }


            return 'solid';
        }

        // Define allowed units per category - UPDATED WITH NEW UNITS
        const allowedUnits = {
            liquid: ['L', 'ml', 'bottles', 'pump', 'tbsp', 'tsp', 'cup', 'shot'],
            solid: ['kg', 'g', 'lbs', 'oz', 'bags', 'packs', 'boxes', 'tbsp', 'tsp', 'cup'],
            countable: ['pcs', 'bags', 'bottles', 'cans', 'packs', 'boxes']
        };

        // Get unit display names 
        function getUnitDisplayName(unit) {
            const unitNames = {
                'kg': 'Kilogram',
                'g': 'Gram',
                'lbs': 'Pounds',
                'oz': 'Ounce',
                'L': 'Liter',
                'ml': 'Milliliter',
                'pump': 'Pump',
                'tbsp': 'Tablespoon',
                'tsp': 'Teaspoon',
                'cup': 'Cup',
                'shot': 'Shot',
                'pcs': 'Pieces',
                'bags': 'Bags',
                'bottles': 'Bottles',
                'cans': 'Cans',
                'packs': 'Packs',
                'boxes': 'Boxes'
            };
            return unitNames[unit] || unit;
        }


        function validateUnitMatch(ingredientName, selectedUnit) {
            if (!ingredientName || !selectedUnit) {
                return { valid: true };
            }

            const ingredientType = detectIngredientType(ingredientName);
            const allowed = allowedUnits[ingredientType];

            if (!allowed.includes(selectedUnit)) {
                const allowedUnitsDisplay = allowed.map(u => getUnitDisplayName(u)).join(', ');
                return {
                    valid: false,
                    type: ingredientType,
                    message: `"${ingredientName}" is a ${ingredientType} ingredient and cannot use "${getUnitDisplayName(selectedUnit)}" as unit.`,
                    allowedUnits: allowedUnitsDisplay
                };
            }

            return { valid: true };
        }


        function showUnitMismatchModal(validation) {
            document.getElementById('mismatchMessage').textContent = validation.message;
            document.getElementById('allowedUnitsText').textContent = validation.allowedUnits;

            const mismatchModal = new bootstrap.Modal(document.getElementById('unitMismatchModal'), {
                backdrop: 'static',
                keyboard: false
            });
            mismatchModal.show();
        }


        document.addEventListener('click', function (e) {
            if (e.target && e.target.id === 'fixUnitMismatchBtn') {
                const mismatchModal = bootstrap.Modal.getInstance(document.getElementById('unitMismatchModal'));
                if (mismatchModal) {
                    mismatchModal.hide();
                }

                document.getElementById('unitEdit').focus();
                document.getElementById('unitEdit').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });


        function showUnitWarning(message) {
            const warning = document.getElementById('unitWarningEdit');
            const warningText = document.getElementById('unitWarningTextEdit');

            warningText.textContent = message;
            warning.classList.add('show');
        }


        function hideUnitWarning() {
            const warning = document.getElementById('unitWarningEdit');
            warning.classList.remove('show');
        }

        // ==================== AUTOCOMPLETE SYSTEM ====================

        const ingredients = [
            <?php foreach ($ingredients as $ingredient): ?>
                                        {
                    ingredientID: <?= $ingredient['ingredientID'] ?>,
                    ingredientName: '<?= addslashes($ingredient['ingredientName']) ?>'
                },
            <?php endforeach; ?>
        ];

        const ingredientInputEdit = document.getElementById('ingredientNameEdit');
        const ingredientIDInputEdit = document.getElementById('ingredientIDEdit');
        const dropdownEdit = document.getElementById('ingredientDropdownEdit');
        const unitSelectEdit = document.getElementById('unitEdit');
        let selectedIndexEdit = -1;


        ingredientInputEdit.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();

            if (query === '') {
                hideDropdownEdit();
                ingredientIDInputEdit.value = '';
                hideUnitWarning();
                return;
            }

            const filtered = ingredients.filter(ingredient =>
                ingredient.ingredientName.toLowerCase().includes(query)
            );

            showDropdownEdit(filtered, query);
            selectedIndexEdit = -1;


            const currentUnit = unitSelectEdit.value;
            if (currentUnit) {
                const validation = validateUnitMatch(this.value, currentUnit);
                if (!validation.valid) {
                    showUnitWarning(validation.message);
                } else {
                    hideUnitWarning();
                }
            }
        });


        unitSelectEdit.addEventListener('change', function () {
            const ingredientName = ingredientInputEdit.value.trim();
            const selectedUnit = this.value;

            if (!ingredientName || !selectedUnit) {
                hideUnitWarning();
                return;
            }

            const validation = validateUnitMatch(ingredientName, selectedUnit);
            if (!validation.valid) {
                showUnitWarning(validation.message);
            } else {
                hideUnitWarning();
            }
        });


        ingredientInputEdit.addEventListener('keydown', function (e) {
            const items = dropdownEdit.querySelectorAll('.autocomplete-item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndexEdit = Math.min(selectedIndexEdit + 1, items.length - 1);
                updateSelectionEdit(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndexEdit = Math.max(selectedIndexEdit - 1, -1);
                updateSelectionEdit(items);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedIndexEdit >= 0 && items[selectedIndexEdit]) {
                    selectItemEdit(items[selectedIndexEdit]);
                }
            } else if (e.key === 'Escape') {
                hideDropdownEdit();
            }
        });


        document.addEventListener('click', function (e) {
            if (!ingredientInputEdit.contains(e.target) && !dropdownEdit.contains(e.target)) {
                hideDropdownEdit();
            }
        });

        function showDropdownEdit(filtered, query) {
            dropdownEdit.innerHTML = '';

            filtered.forEach(ingredient => {
                const item = createDropdownItemEdit(
                    ingredient.ingredientName,
                    false,
                    ingredient.ingredientID
                );
                dropdownEdit.appendChild(item);
            });

            const exactMatch = filtered.some(ingredient =>
                ingredient.ingredientName.toLowerCase() === query.toLowerCase()
            );

            if (!exactMatch && query.length > 0) {
                const newItem = createDropdownItemEdit(
                    `Add new: "${query}"`,
                    true,
                    null,
                    query
                );
                dropdownEdit.appendChild(newItem);
            }

            if (dropdownEdit.children.length > 0) {
                dropdownEdit.style.display = 'block';
            } else {
                hideDropdownEdit();
            }
        }

        function createDropdownItemEdit(text, isNew, ingredientID, newIngredientName = null) {
            const item = document.createElement('div');
            item.className = 'autocomplete-item' + (isNew ? ' new-ingredient-item' : '');
            item.textContent = text;
            item.setAttribute('data-ingredient-id', ingredientID || '');
            item.setAttribute('data-ingredient-name', newIngredientName || text);
            item.setAttribute('data-is-new', isNew ? 'true' : 'false');

            item.addEventListener('click', function () {
                selectItemEdit(this);
            });

            return item;
        }

        function selectItemEdit(item) {
            const isNew = item.getAttribute('data-is-new') === 'true';
            const ingredientName = item.getAttribute('data-ingredient-name');
            const ingredientID = item.getAttribute('data-ingredient-id');

            ingredientInputEdit.value = ingredientName;
            ingredientIDInputEdit.value = isNew ? '' : ingredientID;
            ingredientInputEdit.setAttribute('data-is-new-ingredient', isNew ? 'true' : 'false');

            hideDropdownEdit();
            selectedIndexEdit = -1;


            const currentUnit = unitSelectEdit.value;
            if (currentUnit) {
                const validation = validateUnitMatch(ingredientName, currentUnit);
                if (!validation.valid) {
                    showUnitWarning(validation.message);
                } else {
                    hideUnitWarning();
                }
            }
        }

        function updateSelectionEdit(items) {
            items.forEach((item, index) => {
                item.classList.toggle('selected', index === selectedIndexEdit);
            });
        }

        function hideDropdownEdit() {
            dropdownEdit.style.display = 'none';
            selectedIndexEdit = -1;
        }

        // ==================== FORM SUBMIT WITH VALIDATION ====================

        document.getElementById('updateForm').addEventListener('submit', function (e) {

            e.preventDefault();
            e.stopPropagation();

            const ingredientName = ingredientInputEdit.value.trim();
            const selectedUnit = unitSelectEdit.value;

            console.log('Edit Form - Validating:', ingredientName, 'with unit:', selectedUnit);


            const validation = validateUnitMatch(ingredientName, selectedUnit);

            console.log('Edit Form - Validation result:', validation);

            if (!validation.valid) {
                // Show error modal and BLOCK submission completely
                console.log('BLOCKING EDIT SUBMISSION - Unit mismatch detected!');
                showUnitMismatchModal(validation);


                unitSelectEdit.style.borderColor = '#dc3545';
                unitSelectEdit.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';


                return false;
            }

            console.log('Edit Form - Validation passed - Proceeding with submission');


            unitSelectEdit.style.borderColor = 'var(--primary-color)';
            unitSelectEdit.style.boxShadow = 'none';


            const formData = new FormData(this);

            fetch("../assets/inventory-update-product.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(resp => {
                    if (resp.success) {
                        showToast("Inventory updated successfully", "success");
                        bootstrap.Modal.getInstance(document.getElementById("editItemModal")).hide();
                        setTimeout(() => {
                            location.reload();
                        }, 800);
                    } else {
                        showToast("Error: " + resp.message, "danger");
                    }
                })
                .catch(err => {
                    console.error("AJAX Error:", err);
                    showToast("Error updating inventory", "danger");
                });
        });

        // ==================== POPULATE EDIT MODAL ====================

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const inventoryID = this.dataset.id;
                const ingredientID = this.dataset.ingredientId;
                const ingredient = this.dataset.ingredient;
                const quantity = this.dataset.quantity;
                const unit = this.dataset.unit;
                const expiration = this.dataset.expiration;
                const threshold = this.dataset.threshold;

                document.getElementById('inventoryID').value = inventoryID;
                document.getElementById('ingredientIDEdit').value = ingredientID;
                document.getElementById('ingredientNameEdit').value = ingredient;
                document.getElementById('quantityEdit').value = quantity;
                document.getElementById('unitEdit').value = unit;
                document.getElementById('expirationEdit').value = expiration;
                document.getElementById('thresholdEdit').value = threshold;


                hideUnitWarning();


                unitSelectEdit.style.borderColor = 'var(--primary-color)';
                unitSelectEdit.style.boxShadow = 'none';


                const validation = validateUnitMatch(ingredient, unit);
                if (!validation.valid) {
                    showUnitWarning(validation.message);
                }
            });
        });

        document.getElementById('editItemModal').addEventListener('hidden.bs.modal', function () {
            hideUnitWarning();


            unitSelectEdit.style.borderColor = 'var(--primary-color)';
            unitSelectEdit.style.boxShadow = 'none';
        });
    });
</script>