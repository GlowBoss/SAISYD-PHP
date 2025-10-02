<!-- Order Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="confirmModalLabel">Confirm Order</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" action="" id="confirmOrderForm">
        <div class="modal-body">
          <p class="fw-semibold">Please review your order details:</p>

          <!-- Order Type Selection -->
<div class="mb-3">
  <label class="form-label fw-bold">Order Type</label>
  <div class="dropdown">
    <button class="btn btn-outline-dark dropdown-toggle w-100" type="button" id="orderTypeDropdown"
      data-bs-toggle="dropdown" aria-expanded="false">
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
  <label class="form-label fw-bold">Payment Method</label>
  <div class="dropdown">
    <button class="btn btn-outline-dark dropdown-toggle w-100" type="button" id="paymentModeDropdown"
      data-bs-toggle="dropdown" aria-expanded="false">
      Cash
    </button>
    <ul class="dropdown-menu w-100" aria-labelledby="paymentModeDropdown">
      <li><a class="dropdown-item" href="#" data-value="Cash">Cash</a></li>
      <li><a class="dropdown-item" href="#" data-value="Mobile Payment">Mobile Payment</a></li>
    </ul>
    <input type="hidden" name="paymentMode" id="paymentModeInput" value="Cash">
  </div>
</div>

<!-- Reference Number -->
<div class="mb-3 d-none" id="refNumberContainer">
  <label for="refNumber" class="form-label fw-bold">Reference Number</label>
  <input type="text" name="refNumber" id="refNumber" class="form-control"
    placeholder="Enter reference number">
</div>


          <!-- Notes -->
          <!-- <div class="mb-3" id="orderNotesInput">
            <label for="orderNotes" class="form-label fw-bold">Notes</label>
            <textarea class="form-control" name="notes" id="orderNotes" rows="2" placeholder="Add any notes..."
              style="resize: none; border-radius: 10px; border: 1.5px solid var(--primary-color); font-family: var(--secondaryFont);"></textarea>
          </div> -->

          <!-- Order Summary -->
          <div class="border rounded p-3 bg-light text-dark mb-3">
            <h6 class="fw-bold mb-3">Order Summary</h6>
            <ul class="list-group" id="orderSummaryList">
              <!-- Dynamic items will be inserted here -->
            </ul>
          </div>

          <!-- Action Buttons -->
          <div class="d-grid gap-2">
            <button type="button" name="confirm_order" class="btn btnConfirm">
              <i class="bi bi-check-circle me-2"></i>Confirm Order
            </button>
            <button type="button" class="btn btnCancel" data-bs-dismiss="modal">
              <i class="bi bi-x-circle me-2"></i>Cancel
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Success Toast Notification -->
<div id="orderToast" class="toast align-items-center text-white bg-success position-fixed bottom-0 end-0 m-3"
  role="alert" aria-live="assertive" aria-atomic="true">
  <div class="d-flex">
    <div class="toast-body">
      <i class="bi bi-check-circle-fill me-2"></i>
      Order placed successfully! Receipt <span id="receiptNumber"></span>
    </div>
    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
      aria-label="Close"></button>
  </div>
</div>


<!-- Quantity Modal -->
<div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="quantityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="addToOrderForm" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="quantityModalLabel">Enter Quantity</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Quantity Selector -->
          <div class="mb-4 text-center" style="font-family: var(--primaryFont);">
            <label class="form-label fw-bold d-block mb-2"
              style="color: var(--text-color-dark); font-size: var(--subheading);">
              Quantity
            </label>
            <div class="d-flex justify-content-center align-items-center gap-3">
              <button type="button" onclick="decreaseQuantity()"
                style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-color); color: var(--text-color-light); border: none; font-weight: bold; font-size: 1.2rem;">
                −
              </button>
              <input type="number" id="quantity" value="1" min="1" max="999"
                oninput="document.getElementById('modal-quantity-input').value=this.value" class="no-spinner" style="
    width: 70px; 
    height: 40px; 
    text-align: center; 
    font-weight: bold;
    border-radius: 10px; 
    border: 1.5px solid var(--primary-color); 
    font-family: var(--secondaryFont);
    outline: none; 
  ">


              <button type="button" onclick="increaseQuantity()"
                style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-color); color: var(--text-color-light); border: none; font-weight: bold; font-size: 1.2rem;">
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

        <div class="modal-footer d-flex justify-content-between">
          <button type="submit" class="btn btnOrder w-100 w-md-auto">Add to Order</button>
          <button type="button" class="btn btnOrder w-100 w-md-auto" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>