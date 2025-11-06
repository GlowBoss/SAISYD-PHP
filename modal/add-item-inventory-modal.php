<?php
// Get ingredients for autocomplete 
if (!isset($ingredients)) {
    $ingredientsQuery = "SELECT ingredientID, ingredientName FROM ingredients ORDER BY ingredientName";
    $ingredientsResult = executeQuery($ingredientsQuery);
    $ingredients = [];
    if ($ingredientsResult && mysqli_num_rows($ingredientsResult) > 0) {
        while ($row = mysqli_fetch_assoc($ingredientsResult)) {
            $ingredients[] = $row;
        }
    }
}
?>

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

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" data-bs-backdrop="true" tabindex="-1" aria-labelledby="addItemModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h1 class="modal-title fs-4 fw-bold" id="addItemModalLabel"
                    style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
                    Add Inventory Item
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="filter: invert(50%);"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form method="POST" action="" id="addItemForm">
                    <input type="hidden" name="action" value="create">
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
                                <input type="text" class="form-control" id="ingredientName" name="ingredientName"
                                    required placeholder="Type ingredient name..." autocomplete="off" style="border: 2px solid var(--primary-color); border-radius: 10px; 
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
                                <input type="number" class="form-control" id="quantity" name="quantity" min="0"
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
                                <select class="form-select" id="unit" name="unit" required style="border: 2px solid var(--primary-color); border-radius: 10px; 
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
                                <div class="unit-warning" id="unitWarningAdd">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    <span class="unit-warning-text" id="unitWarningTextAdd"></span>
                                </div>
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
                                <input type="date" class="form-control" id="expirationDate" name="expirationDate"
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
                                <input type="number" class="form-control" id="threshold" name="threshold" min="0"
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

                        <!-- Add Item Button -->
                        <button type="submit" class="btn fw-bold px-4 py-2" id="addItemBtn" style="
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

<!-- Unit Mismatch Error Modal (Shared) -->
<div class="modal fade" id="unitMismatchModalAdd" tabindex="-1" aria-labelledby="unitMismatchModalAddLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">
            <div class="modal-header border-0 pb-2"
                style="background: linear-gradient(135deg, #ff6b6b 0%, #dc3545 100%);">
                <h5 class="modal-title fw-bold text-white" id="unitMismatchModalAddLabel">
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
                    id="mismatchMessageAdd">
                    <!-- Dynamic message here -->
                </p>
                <div class="alert alert-warning mt-3" role="alert" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Allowed units:</strong> <span id="allowedUnitsTextAdd"></span>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn fw-bold px-4 py-2" id="fixUnitMismatchBtnAdd" style="
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

        function detectIngredientType(ingredientName) {
            const name = ingredientName.toLowerCase().trim();

            // ==================== DUAL ITEMS (Both countable AND solid) ====================
            const dualKeywords = [
                'ice' 
            ];

            // ==================== COUNTABLE ITEMS ) ====================

            const countableKeywords = [
                // Eggs 
                'egg', 'eggs', 'eggs medium',

                // Bread 
                'loaf', 'loaf brioche bread', 'pita bread',

                // Packaging/Service items 
                'bottle', 'can',
                'cup lid', 'cup sleeve', 'straw',
                'tissue', 'napkin', 'fork', 'spoon',
                'tray', 'plate', 'container',
                'cup holder', 'stirrer', 'plastic cup', 'take-out box'
            ];

            // ==================== LIQUID ITEMS (ML/L measurement) ====================

            const liquidKeywords = [
                // ALL Milk variants 
                'milk', 'full cream milk', 'oatside milk', 'soy milk', 'almond milk',
                'evaporated milk', 'condensed milk',
                'heavy whipping cream', 'whipping cream', 'all purpose cream', 'cream',

                // Coffee liquids 
                'espresso', 'espresso shot', 'coffee liquid', 'cold brew',

                // ALL Syrups
                'syrup', 'vanilla syrup', 'caramel syrup', 'hazelnut syrup',
                'brown sugar syrup', 'blueberry syrup', 'green apple syrup',
                'lychee syrup', 'peach honey syrup', 'lemon syrup',

                // ALL Sauces 
                'chocolate sauce', 'caramel sauce',
                'soy sauce', 'fish sauce', 'oystersauce', 'oyster sauce',
                'worcestershire sauce',
                'banana ketchup', 'ketchup',
                'sriracha sauce', 'sriracha',

                // Purees and juices
                'puree', 'strawberry puree',
                'juice', 'lemonade',

                // Beverages
                'water', 'soda', 'sprite', 'sprite soda',

                // Oils and liquid condiments
                'oil', 'cooking oil',
                'vinegar', 'gravy',

                // Sweet liquids
                'fructose', 'honey',

                // Creamy liquids
                'yogurt',
                'kewpie mayo', 'mayo', 'mayonnaise'
            ];

            // ==================== SOLID ITEMS (G/KG measurement) ====================

            const solidKeywords = [
                // Meats 
                'chicken wings', 'wings', 'chicken',
                'beef samgyup', 'beef giniling', 'beef', 'giniling',
                'porkloin', 'pork',
                'ham', 'spam', 'bacon',
                'patty', 'nugget', 'sausage',

                // Vegetables 
                'onion', 'garlic', 'cucumber', 'tomato',
                'lettuce', 'lemon',

                // Sugar and sweeteners
                'sugar', 'brown sugar', 'white sugar',

                // ALL Powders - measured by weight
                'powder',
                'cocoa powder', 'chocolate powder', 'caramel powder',
                'cookies and cream powder', 'milo powder',
                'matcha powder', 'ceremonial matcha powder',

                // Coffee shop special bases
                'frappe base',

                'taro', 'ube', 'nata de coco',

                // Coffee beans 
                'beans', 'arabica beans', 'coffee beans',

                // Tea leaves 
                'tea', 'black tea', 'assam black tea', 'tea leaves',

                // Baking/Cooking ingredients 
                'flour', 'all purpose flour',
                'cornstarch', 'breadcrumbs',
                'rice', 'jasmine rice', 'grain', 'oats',

                // Dairy solids 
                'cheese', 'cheese block', 'parmesan cheese', 'parmesan',
                'butter',

                // Baked goods in bulk
                'biscuit', 'cookies', 'muffin', 'toast',
                'waffle', 'croissant', 'brownie', 'cake', 'pastry',

                // Snacks and sides 
                'chips', 'french fries', 'fries', 'onion rings',

                // Nuts 
                'nuts', 'almond', 'cashew', 'hazelnut',

                // Seasonings 
                'salt', 'pepper', 'seasoning'
            ];

            // ==================== SPECIAL CASE DETECTION ====================

            // 0. Check DUAL items first (highest priority)
            if (dualKeywords.some(keyword => name.includes(keyword))) {
                return 'dual';
            }

            // 1. ALL TEA (leaves/powder) = SOLID
            if (name.includes('tea') && !name.includes('milktea')) {
                return 'solid';
            }

            // 2. MEATS are ALWAYS SOLID 
            const meatKeywords = ['chicken', 'wings', 'beef', 'pork', 'ham', 'spam', 'bacon'];
            if (meatKeywords.some(keyword => name.includes(keyword))) {
                return 'solid';
            }

            // 3. Flavor powders (not syrups/sauces) = SOLID
            if (
                (name.includes('vanilla') ||
                    name.includes('caramel') ||
                    name.includes('hazelnut') ||
                    name.includes('mocha') ||
                    name.includes('chocolate')) &&
                !name.includes('syrup') &&
                !name.includes('sauce') &&
                !name.includes('milk') &&
                !name.includes('cream')
            ) {
                return 'solid';
            }

            // 4. VEGETABLES = SOLID 
            const veggieKeywords = ['onion', 'garlic', 'cucumber', 'tomato', 'lettuce', 'lemon'];
            if (veggieKeywords.some(keyword => name.includes(keyword))) {
                return 'solid';
            }

            // ==================== DETECTION PRIORITY ====================

            // Check countable first (most specific)
            if (countableKeywords.some(keyword => name.includes(keyword))) {
                return 'countable';
            }

            // Check liquid (very specific keywords)
            if (liquidKeywords.some(keyword => name.includes(keyword))) {
                return 'liquid';
            }

            // Check solid (catch-all for most ingredients)
            if (solidKeywords.some(keyword => name.includes(keyword))) {
                return 'solid';
            }

            // Default: SOLID (safest for coffee shop inventory)
            return 'solid';
        }

        // ==================== ALLOWED UNITS PER CATEGORY ====================
        const allowedUnits = {
            // Liquids - volume measurements + bottles for inventory
            liquid: ['L', 'ml', 'pump', 'tbsp', 'tsp', 'cup', 'shot', 'bottles'],

            // Solids - weight measurements + packaging for inventory purchases
            solid: ['kg', 'g', 'lbs', 'oz', 'tbsp', 'tsp', 'cup', 'bags', 'packs', 'boxes'],

            // Countables - only counting units
            countable: ['pcs', 'bags', 'bottles', 'cans', 'packs', 'boxes'],

            // Dual - combination of solid AND countable units
            dual: ['kg', 'g', 'lbs', 'oz', 'pcs', 'bags', 'packs', 'boxes']
        };

        // ==================== UNIT DISPLAY NAMES ====================
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

        // ==================== VALIDATION FUNCTION ====================
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
            document.getElementById('mismatchMessageAdd').textContent = validation.message;
            document.getElementById('allowedUnitsTextAdd').textContent = validation.allowedUnits;

            const mismatchModal = new bootstrap.Modal(document.getElementById('unitMismatchModalAdd'), {
                backdrop: 'static',
                keyboard: false
            });
            mismatchModal.show();
        }


        document.addEventListener('click', function (e) {
            if (e.target && e.target.id === 'fixUnitMismatchBtnAdd') {
                const mismatchModal = bootstrap.Modal.getInstance(document.getElementById('unitMismatchModalAdd'));
                if (mismatchModal) {
                    mismatchModal.hide();
                }

                document.getElementById('unit').focus();
                document.getElementById('unit').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });


        function showUnitWarning(message) {
            const warning = document.getElementById('unitWarningAdd');
            const warningText = document.getElementById('unitWarningTextAdd');

            warningText.textContent = message;
            warning.classList.add('show');
        }


        function hideUnitWarning() {
            const warning = document.getElementById('unitWarningAdd');
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

        const ingredientInput = document.getElementById('ingredientName');
        const ingredientIDInput = document.getElementById('ingredientID');
        const dropdown = document.getElementById('ingredientDropdown');
        const unitSelect = document.getElementById('unit');
        let selectedIndex = -1;


        ingredientInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();

            if (query === '') {
                hideDropdown();
                ingredientIDInput.value = '';
                hideUnitWarning();
                return;
            }

            const filtered = ingredients.filter(ingredient =>
                ingredient.ingredientName.toLowerCase().includes(query)
            );

            showDropdown(filtered, query);
            selectedIndex = -1;

            const currentUnit = unitSelect.value;
            if (currentUnit) {
                const validation = validateUnitMatch(this.value, currentUnit);
                if (!validation.valid) {
                    showUnitWarning(validation.message);
                } else {
                    hideUnitWarning();
                }
            }
        });


        unitSelect.addEventListener('change', function () {
            const ingredientName = ingredientInput.value.trim();
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


        document.addEventListener('click', function (e) {
            if (!ingredientInput.contains(e.target) && !dropdown.contains(e.target)) {
                hideDropdown();
            }
        });

        function showDropdown(filtered, query) {
            dropdown.innerHTML = '';

            filtered.forEach(ingredient => {
                const item = createDropdownItem(
                    ingredient.ingredientName,
                    false,
                    ingredient.ingredientID
                );
                dropdown.appendChild(item);
            });

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
            ingredientInput.setAttribute('data-is-new-ingredient', isNew ? 'true' : 'false');

            hideDropdown();
            selectedIndex = -1;


            const currentUnit = unitSelect.value;
            if (currentUnit) {
                const validation = validateUnitMatch(ingredientName, currentUnit);
                if (!validation.valid) {
                    showUnitWarning(validation.message);
                } else {
                    hideUnitWarning();
                }
            }
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

        // ==================== FORM SUBMIT WITH VALIDATION ====================

        const addItemForm = document.getElementById('addItemForm');
        const addItemBtn = document.getElementById('addItemBtn');

        addItemForm.addEventListener('submit', function (e) {

            e.preventDefault();
            e.stopPropagation();

            const ingredientName = ingredientInput.value.trim();
            const selectedUnit = unitSelect.value;

            console.log('Add Form - Validating:', ingredientName, 'with unit:', selectedUnit);


            const validation = validateUnitMatch(ingredientName, selectedUnit);

            console.log('Add Form - Validation result:', validation);

            if (!validation.valid) {
                // Show error modal and BLOCK submission completely
                console.log('BLOCKING ADD SUBMISSION - Unit mismatch detected!');
                showUnitMismatchModal(validation);


                unitSelect.style.borderColor = '#dc3545';
                unitSelect.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';


                addItemBtn.disabled = true;

                setTimeout(() => {
                    addItemBtn.disabled = false;
                }, 2000);


                return false;
            }

            console.log('Add Form - Validation passed - Proceeding with submission');


            unitSelect.style.borderColor = 'var(--primary-color)';
            unitSelect.style.boxShadow = 'none';


            this.submit();
        });


        document.getElementById('addItemModal').addEventListener('hidden.bs.modal', function () {
            ingredientInput.value = '';
            ingredientIDInput.value = '';
            hideDropdown();
            hideUnitWarning();
            selectedIndex = -1;


            unitSelect.style.borderColor = 'var(--primary-color)';
            unitSelect.style.boxShadow = 'none';
        });
    });
</script>