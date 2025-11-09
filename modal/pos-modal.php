<!-- Order Confirmation Modal -->
<div class="modal fade" id="confirmModal" data-bs-backdrop="true" tabindex="-1" aria-labelledby="confirmModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

      <!-- Header -->
      <div class="modal-header border-0 pb-0">
        <h1 class="modal-title fs-4 fw-bold" id="confirmModalLabel"
          style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
          Confirm Order
        </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
          style="filter: invert(50%);"></button>
      </div>

      <form method="POST" action="" id="confirmOrderForm">
        <div class="modal-body">
          <!-- Order Type Selection -->
          <div class="mb-3">
            <label class="form-label fw-bold"
              style="font-family: var(--primaryFont); color: var(--text-color-dark);">Order Type</label>
            <div class="dropdown">
              <button class="btn dropdown-toggle w-100" type="button" id="orderTypeDropdown" data-bs-toggle="dropdown"
                aria-expanded="false"
                style="background: var(--card-bg-color); color: var(--text-color-dark); border: 2px solid var(--primary-color); border-radius: 10px; font-family: var(--secondaryFont); padding: 10px;">
                Dine-In
              </button>
              <ul class="dropdown-menu w-100" aria-labelledby="orderTypeDropdown">
                <li><a class="dropdown-item" href="#" data-value="Dine-In">Dine-In</a></li>
                <li><a class="dropdown-item" href="#" data-value="Take-Out">Take-Out</a></li>
              </ul>
              <input type="hidden" name="orderType" id="orderTypeInput" value="Dine-In">
            </div>
          </div>

          <!-- Payment Mode Selection -->
          <div class="mb-3">
            <label class="form-label fw-bold"
              style="font-family: var(--primaryFont); color: var(--text-color-dark);">Payment Method</label>
            <div class="dropdown">
              <button class="btn dropdown-toggle w-100" type="button" id="paymentModeDropdown" data-bs-toggle="dropdown"
                aria-expanded="false"
                style="background: var(--card-bg-color); color: var(--text-color-dark); border: 2px solid var(--primary-color); border-radius: 10px; font-family: var(--secondaryFont); padding: 10px;">
                Cash
              </button>
              <ul class="dropdown-menu w-100" aria-labelledby="paymentModeDropdown">
                <li><a class="dropdown-item" href="#" data-value="CASH">Cash</a></li>
                <li><a class="dropdown-item" href="#" data-value="GCASH">GCash</a></li>
              </ul>
              <input type="hidden" name="paymentMode" id="paymentModeInput" value="CASH">
            </div>
          </div>

          <!-- Reference Number -->
          <div class="mb-3 d-none" id="refNumberContainer">
            <label for="refNumber" class="form-label fw-bold"
              style="font-family: var(--primaryFont); color: var(--text-color-dark);">Reference Number</label>
            <input type="text" name="refNumber" id="refNumber" class="form-control" placeholder="Enter reference number"
              style="border-radius: 10px; border: 1.5px solid var(--primary-color); font-family: var(--secondaryFont); padding: 10px;">
          </div>

          <!-- Order Summary -->
          <div class="rounded-3 p-3 mb-4"
            style="background: var(--card-bg-color); border: 1px solid var(--primary-color);">
            <h6 class="fw-bold mb-3" style="font-family: var(--primaryFont); color: var(--primary-color);">Order Summary
            </h6>
            <ul class="list-group list-unstyled" id="orderSummaryList"
              style="font-family: var(--secondaryFont); color: var(--text-color-dark);">
              <!-- Dynamic items will be inserted here -->
            </ul>
          </div>

          <!-- Action Buttons -->
          <div class="d-flex gap-3 justify-content-center">
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
              <i class="bi bi-x-circle me-2"></i>CANCEL
            </button>

            <!-- Confirm Button -->
            <button type="button" name="confirm_order" class="btn fw-bold px-4 py-2" onclick="confirmOrder()" style="
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
              <i class="bi bi-check-circle me-2"></i>CONFIRM ORDER
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Success Toast Notification -->
<div id="orderToast" class="toast align-items-center position-fixed bottom-0 end-0 m-3" role="alert"
  aria-live="assertive" aria-atomic="true"
  style="background: var(--text-color-dark); color: white; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
  <div class="d-flex">
    <div class="toast-body" style="font-family: var(--secondaryFont);">
      <i class="bi bi-check-circle-fill me-2"></i>
      Order placed successfully! Receipt <span id="receiptNumber" style="font-weight: bold;"></span>
    </div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
      aria-label="Close"></button>
  </div>
</div>

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
              <input type="number" id="quantity" value="1" min="1" max="999"
                oninput="document.getElementById('modal-quantity-input').value=this.value" class="no-spinner" style="
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
// Initialize modal dropdowns with auto-close functionality
document.addEventListener('DOMContentLoaded', function() {
    // Wait for modal to be fully loaded
    setTimeout(function() {
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
            item.addEventListener('click', function(e) {
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
            item.addEventListener('click', function(e) {
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
document.getElementById('confirmModal')?.addEventListener('shown.bs.modal', function() {
    initializeModalDropdowns();
});
</script>