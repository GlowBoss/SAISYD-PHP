<!-- Order Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="confirmModalLabel">Confirm Pickup Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p class="fw-semibold">Please enter your name for pickup:</p>
                <div class="mb-3">
                    <input type="text" class="form-control" id="pickupCustomerName" placeholder="Your Name" required>
                </div>
                <div class="mb-3">
                    <label for="pickupCustomerPhone" class="form-label fw-semibold">Phone Number</label>
                    <input type="text" class="form-control" id="pickupCustomerPhone"
                        placeholder="Enter your phone number" pattern="\d{10,11}" maxlength="11" inputmode="numeric"
                        oninput="this.value = this.value.replace(/[^0-9]/g,'').slice(0,11);">
                </div>

                <div class="border rounded p-3 bg-light text-dark mb-3">
                    <h6 class="fw-bold mb-2">Order Summary</h6>
                    <ul class="list-unstyled mb-0" id="orderSummaryList">
                        <!-- Filled dynamically by JS -->
                    </ul>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn addbtn btn-success confirm-order-btn">Yes, Confirm</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Success Toast -->
<div id="orderToast" class="toast align-items-center text-bg-success position-fixed bottom-0 end-0 m-3 z-3" role="alert"
    aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
        <div class="toast-body">
            Your order has been placed successfully!
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
            aria-label="Close"></button>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const pickupRadio = document.querySelector('input[name="order_type"][value="pickup"]');
        const modalEl = document.getElementById('confirmModal');
        const confirmModal = new bootstrap.Modal(modalEl);
        const orderSummaryList = document.getElementById("orderSummaryList");
        const confirmBtn = modalEl.querySelector(".confirm-order-btn");
        const hiddenCustomerInput = document.getElementById('hiddenCustomerName');
        const hiddenPhoneInput = document.getElementById('hiddenCustomerPhone');

        pickupRadio.addEventListener("change", () => {
            if (pickupRadio.checked) {
                // Fill order summary
                orderSummaryList.innerHTML = "";
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        orderSummaryList.innerHTML += `<li><?= $item['quantity'] ?>x <?= htmlspecialchars($item['product_name']) ?></li>`;
                    <?php endforeach; ?>
                <?php endif; ?>

                // Show modal
                confirmModal.show();
            }
        });

        confirmBtn.addEventListener("click", () => {
            const customerNameInput = document.getElementById('pickupCustomerName');
            const customerPhoneInput = document.getElementById('pickupCustomerPhone');

            hiddenCustomerInput.value = customerNameInput.value.trim();
            hiddenPhoneInput.value = customerPhoneInput.value.trim();

            confirmModal.hide();
        });
    });
</script>