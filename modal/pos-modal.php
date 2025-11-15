<!-- Quantity Modal -->
<div class="modal fade" id="quantityModal" data-bs-backdrop="true" tabindex="-1" aria-labelledby="quantityModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">
      <form id="addToOrderForm" method="POST">

        <!-- Header -->
        <div class="modal-header border-0 pb-0">
          <h1 class="modal-title fs-4 fw-bold" id="quantityModalLabel"
            style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
            Select Quantity
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
            style="filter: invert(50%);"></button>
        </div>

        <div class="modal-body">
          <!-- Available Stock Display (Optional - add if you want) -->
          <div class="text-center mb-3">
            <small class="text-muted">Available Stock: <strong id="availableStock">0</strong> pcs</small>
          </div>

          <!-- Quantity Selector -->
          <div class="mb-4 text-center" style="font-family: var(--primaryFont);">
            <label class="form-label fw-bold d-block mb-3"
              style="color: var(--text-color-dark); font-size: 1.1rem; letter-spacing: 0.5px;">
              Quantity
            </label>
            <div class="d-flex justify-content-center align-items-center gap-3">
              <button type="button" onclick="decreaseQuantity()"
                style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); color: var(--text-color-light); border: none; font-weight: bold; font-size: 1.3rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"
                onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.3)';"
                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.2)';">
                −
              </button>
              <input type="number" id="quantity" value="1" min="1" max="999" oninput="validateQuantityInput(event)"
                onblur="validateOnBlur()" style="
    width: 80px; 
    height: 45px; 
    text-align: center; 
    font-weight: bold;
    font-size: 1.2rem;
    border-radius: 10px; 
    border: 2px solid var(--primary-color); 
    font-family: var(--secondaryFont);
    outline: none; 
    background: var(--card-bg-color);
    color: var(--text-color-dark);
">
              <button type="button" onclick="increaseQuantity()"
                style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); color: var(--text-color-light); border: none; font-weight: bold; font-size: 1.3rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"
                onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.3)';"
                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.2)';">
                +
              </button>
            </div>
          </div>

          <!-- Hidden Fields for DB -->
          <input type="hidden" name="add_to_order" value="1">
          <input type="hidden" name="product_id" id="modal-product-id">
          <input type="hidden" name="product_name" id="modal-product-name">
          <input type="hidden" name="price" id="modal-product-price">
          <input type="hidden" name="category" id="modal-category">
          <input type="hidden" name="quantity" id="modal-quantity-input" value="1">
          <input type="hidden" name="sugar" id="modal-sugar-input" value="">
          <input type="hidden" name="ice" id="modal-ice-input" value="">
          <input type="hidden" name="notes" id="modal-notes" value="">
          <input type="hidden" name="available_quantity" id="modal-available-quantity" value="0">
        </div>

        <div class="modal-footer border-0 pt-0">
          <div class="d-flex gap-3 justify-content-center w-100">
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

            <!-- Add to Order Button -->
            <button type="submit" class="btn fw-bold px-4 py-2" style="
              background: var(--text-color-dark); 
              color: white; 
              border: none;
              border-radius: 10px; 
              font-family: var(--primaryFont); 
              letter-spacing: 1px; 
              box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); 
              transition: all 0.3s ease;
              min-width: 120px;
            " onmouseover="
              this.style.background='var(--primary-color)';  
              this.style.transform='translateY(-2px)';    
              this.style.boxShadow='0 6px 12px rgba(0, 0, 0, 0.4)';
            " onmouseout="
              this.style.background='var(--text-color-dark)'; 
              this.style.transform='translateY(0)';
              this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)';
            ">
              ADD TO ORDER
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  let selectedProduct = {};

  function openQuantityModal(id, name, price, image, stock) {
    selectedProduct = { id, name, price, image, stock };

    document.getElementById('selectedName').textContent = name;
    document.getElementById('selectedImage').src = image;
    document.getElementById('availableStock').textContent = stock;

    // Update hidden fields
    document.getElementById('modal-product-id').value = id;
    document.getElementById('modal-product-name').value = name;
    document.getElementById('modal-product-price').value = price;
    document.getElementById('modal-available-quantity').value = stock;

    // 🔥 THE FIX — set available stock globally and reset quantity
    initializeQuantityModal(stock);

    const modal = new bootstrap.Modal(document.getElementById('quantityModal'));
    modal.show();
  }

  // + / – button logic
  function updateQuantity(change) {
    const input = document.getElementById('modal-quantity-input');
    const min = parseInt(input.min);
    const max = parseInt(input.max);

    let newValue = parseInt(input.value) + change;

    if (newValue < min) newValue = min;
    if (newValue > max) newValue = max;

    input.value = newValue;

    // Trigger validation events (optional but helps with UI consistency)
    input.dispatchEvent(new Event('input'));
  }

  function confirmAdd() {
    const qty = parseInt(document.getElementById('modal-quantity-input').value);
    const notes = document.getElementById('modal-instructions').value;

    console.log("Add to cart:", selectedProduct, "Qty:", qty, "Notes:", notes);

    // >>> Your add-to-cart PHP/JS logic goes here <<<

    bootstrap.Modal.getInstance(document.getElementById('quantityModal')).hide();
  }
</script>

<script>
  console.log('POS Modal script loaded!');

  // Global variable to store current product's available quantity
  let currentAvailableQuantity = 0;
  console.log('Initial currentAvailableQuantity:', currentAvailableQuantity);

  // Function to initialize quantity modal when opened
  function initializeQuantityModal(availableQty) {
    // Set the available quantity
    currentAvailableQuantity = parseInt(availableQty) || 0;

    console.log('Initializing modal with available quantity:', currentAvailableQuantity); // Debug

    // Update available stock display if it exists
    const availableStockSpan = document.getElementById('availableStock');
    if (availableStockSpan) {
      availableStockSpan.textContent = availableQty;
    }

    // Reset quantity to 1
    const quantityInput = document.getElementById('quantity');
    const modalQuantityInput = document.getElementById('modal-quantity-input');
    if (quantityInput) {
      quantityInput.value = 1;
      quantityInput.setAttribute('max', availableQty); // Set max attribute dynamically
    }
    if (modalQuantityInput) modalQuantityInput.value = 1;

    // Update button states immediately
    updateButtonStates(1);
  }

  // Update button states based on current quantity
  function updateButtonStates(currentQty) {
    const decreaseBtn = document.querySelector('#quantityModal button[onclick="decreaseQuantity()"]');
    const increaseBtn = document.querySelector('#quantityModal button[onclick="increaseQuantity()"]');

    console.log('Updating button states - Current:', currentQty, 'Max:', currentAvailableQuantity); // Debug

    // Disable decrease button if at minimum (1)
    if (decreaseBtn) {
      if (currentQty <= 1) {
        decreaseBtn.disabled = true;
        decreaseBtn.style.opacity = '0.5';
        decreaseBtn.style.cursor = 'not-allowed';
      } else {
        decreaseBtn.disabled = false;
        decreaseBtn.style.opacity = '1';
        decreaseBtn.style.cursor = 'pointer';
      }
    }

    // Disable increase button if at maximum (available stock)
    if (increaseBtn) {
      if (currentQty >= currentAvailableQuantity) {
        increaseBtn.disabled = true;
        increaseBtn.style.opacity = '0.5';
        increaseBtn.style.cursor = 'not-allowed';
      } else {
        increaseBtn.disabled = false;
        increaseBtn.style.opacity = '1';
        increaseBtn.style.cursor = 'pointer';
      }
    }
  }

  // Increase quantity function
  function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const modalQuantityInput = document.getElementById('modal-quantity-input');

    console.log('Increase clicked - Current max:', currentAvailableQuantity);

    if (quantityInput && modalQuantityInput) {
      let currentValue = parseInt(quantityInput.value) || 1;

      // Check if we can increase (stock limit) ✓ THIS IS THE KEY PART
      if (currentValue < currentAvailableQuantity) {
        const newValue = currentValue + 1;
        quantityInput.value = newValue;
        modalQuantityInput.value = newValue;
        updateButtonStates(newValue);
      }
    }
  }

  function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const modalQuantityInput = document.getElementById('modal-quantity-input');

    if (quantityInput && modalQuantityInput) {
      let currentValue = parseInt(quantityInput.value) || 1;

      if (currentValue > 1) {
        const newValue = currentValue - 1;
        quantityInput.value = newValue;
        modalQuantityInput.value = newValue;
        updateButtonStates(newValue);
      }
    }
  }

  // Validate manual input - fires on every keystroke
  function validateQuantityInput(event) {
    const quantityInput = document.getElementById('quantity');
    const modalQuantityInput = document.getElementById('modal-quantity-input');

    console.log('Validating input - Max allowed:', currentAvailableQuantity, 'Current value:', quantityInput?.value); // Debug

    if (quantityInput && modalQuantityInput) {
      let value = quantityInput.value;

      // Remove any non-digit characters
      value = value.replace(/\D/g, '');

      // If empty, don't set anything yet (let user type)
      if (value === '') {
        quantityInput.value = '';
        return;
      }

      // Convert to number
      let numValue = parseInt(value);

      // If exceeds stock, immediately cap it
      if (numValue > currentAvailableQuantity) {
        console.log('Exceeded max! Capping at:', currentAvailableQuantity); // Debug
        numValue = currentAvailableQuantity;
      }

      // If less than 1, set to 1
      if (numValue < 1) {
        numValue = 1;
      }

      // Update both inputs immediately
      quantityInput.value = numValue;
      modalQuantityInput.value = numValue;
      updateButtonStates(numValue);
    }
  }

  // Additional validation on blur (when user leaves the field)
  function validateOnBlur() {
    const quantityInput = document.getElementById('quantity');
    const modalQuantityInput = document.getElementById('modal-quantity-input');

    if (quantityInput && modalQuantityInput) {
      let value = parseInt(quantityInput.value);

      // If empty or invalid, set to 1
      if (isNaN(value) || value < 1 || quantityInput.value === '') {
        value = 1;
      }

      // If exceeds stock, cap at max
      if (value > currentAvailableQuantity) {
        value = currentAvailableQuantity;
      }

      quantityInput.value = value;
      modalQuantityInput.value = value;
      updateButtonStates(value);
    }
  }

  // Form validation before submit
  document.addEventListener('DOMContentLoaded', function () {
    const addToOrderForm = document.getElementById('addToOrderForm');
    if (addToOrderForm) {
      addToOrderForm.addEventListener('submit', function (e) {
        const qty = parseInt(document.getElementById('quantity').value);

        // Validate quantity
        if (isNaN(qty) || qty <= 0) {
          e.preventDefault();
          alert("Quantity must be at least 1.");
          return false;
        }

        if (qty > currentAvailableQuantity) {
          e.preventDefault();
          alert(`Only ${currentAvailableQuantity} pcs available. Please reduce quantity.`);
          return false;
        }
      });
    }
  });

  // Initialize modal dropdowns with auto-close functionality
  document.addEventListener('DOMContentLoaded', function () {
    // Wait for modal to be fully loaded
    setTimeout(function () {
      initializeModalDropdowns();
    }, 100);
  });

  function initializeModalDropdowns() {
    // Handle Order Type dropdown
    const orderTypeDropdown = document.getElementById('orderTypeDropdown');
    const orderTypeInput = document.getElementById('orderTypeInput');
    const orderTypeItems = document.querySelectorAll('#confirmModal .dropdown-menu[aria-labelledby="orderTypeDropdown"] .dropdown-item');

    if (orderTypeDropdown && orderTypeInput) {
      // Initialize dropdown if not already initialized
      if (!bootstrap.Dropdown.getInstance(orderTypeDropdown)) {
        new bootstrap.Dropdown(orderTypeDropdown);
      }

      orderTypeItems.forEach(item => {
        item.addEventListener('click', function (e) {
          e.preventDefault();
          const value = this.getAttribute('data-value');
          orderTypeDropdown.textContent = value;
          orderTypeInput.value = value;

          // Close the dropdown
          const dropdown = bootstrap.Dropdown.getInstance(orderTypeDropdown);
          if (dropdown) {
            dropdown.hide();
          }
        });
      });
    }

    // Handle Payment Mode dropdown
    const paymentModeDropdown = document.getElementById('paymentModeDropdown');
    const paymentModeInput = document.getElementById('paymentModeInput');
    const paymentModeItems = document.querySelectorAll('#confirmModal .dropdown-menu[aria-labelledby="paymentModeDropdown"] .dropdown-item');

    if (paymentModeDropdown && paymentModeInput) {
      // Initialize dropdown if not already initialized
      if (!bootstrap.Dropdown.getInstance(paymentModeDropdown)) {
        new bootstrap.Dropdown(paymentModeDropdown);
      }

      paymentModeItems.forEach(item => {
        item.addEventListener('click', function (e) {
          e.preventDefault();
          const value = this.getAttribute('data-value');
          paymentModeDropdown.textContent = value;
          paymentModeInput.value = value;

          // Close the dropdown
          const dropdown = bootstrap.Dropdown.getInstance(paymentModeDropdown);
          if (dropdown) {
            dropdown.hide();
          }
        });
      });
    }
  }

  // Re-initialize when confirm modal is shown
  document.getElementById('confirmModal')?.addEventListener('shown.bs.modal', function () {
    initializeModalDropdowns();
  });

  // Reset modal when closed
  document.getElementById('quantityModal')?.addEventListener('hidden.bs.modal', function () {
    const quantityInput = document.getElementById('quantity');
    const modalQuantityInput = document.getElementById('modal-quantity-input');

    // Reset quantity
    if (quantityInput) quantityInput.value = 1;
    if (modalQuantityInput) modalQuantityInput.value = 1;

    // Reset available quantity
    currentAvailableQuantity = 0;

    // Reset button states
    const decreaseBtn = document.querySelector('#quantityModal button[onclick="decreaseQuantity()"]');
    const increaseBtn = document.querySelector('#quantityModal button[onclick="increaseQuantity()"]');

    if (decreaseBtn) {
      decreaseBtn.disabled = false;
      decreaseBtn.style.opacity = '1';
      decreaseBtn.style.cursor = 'pointer';
    }

    if (increaseBtn) {
      increaseBtn.disabled = false;
      increaseBtn.style.opacity = '1';
      increaseBtn.style.cursor = 'pointer';
    }
  });
</script>