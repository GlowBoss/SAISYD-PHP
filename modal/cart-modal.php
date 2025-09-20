<!-- Order Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="confirmModalLabel">Confirm Pickup Order</h5>
                <button type="button" class="btn-close" onclick="window.location.href='cart.php'" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p class="fw-semibold">Please enter your name for pickup:</p>
                <div class="mb-3">
                    <input type="text" class="form-control" id="pickupCustomerName" placeholder="Your Name" required>
                </div>
                <div class="mb-3">
                    <label for="pickupCustomerPhone" class="form-label fw-semibold">Phone Number</label>
                    <input type="text" class="form-control" id="pickupCustomerPhone"
                        placeholder="09XXXXXXXXX" pattern="\d{11}" maxlength="11" inputmode="numeric"
                        oninput="formatPhoneNumber(this)" value="09" required>
                </div>

                <div class="border rounded p-3 bg-light text-dark mb-3">
                    <h6 class="fw-bold mb-2">Order Summary</h6>
                    <ul class="list-unstyled mb-0" id="orderSummaryList">
                        <!-- Filled dynamically by JS -->
                    </ul>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn addbtn buy-btn confirm-order-btn">Yes, Confirm</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='cart.php'">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function formatPhoneNumber(input) {
        // Remove all non-digits
        let value = input.value.replace(/[^0-9]/g, '');
        
        // If user tries to delete the '09', restore it
        if (!value.startsWith('09')) {
            if (value === '' || value === '0') {
                value = '09';
            } else if (value.startsWith('9')) {
                value = '0' + value;
            } else {
                value = '09' + value;
            }
        }
        
        // Limit to 11 digits
        value = value.slice(0, 11);
        
        input.value = value;
    }

    document.addEventListener("DOMContentLoaded", () => {
        const pickupRadio = document.querySelector('input[name="order_type"][value="pickup"]');
        const modalEl = document.getElementById('confirmModal');
        const confirmModal = new bootstrap.Modal(modalEl, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        const orderSummaryList = document.getElementById("orderSummaryList");
        const confirmBtn = modalEl.querySelector(".confirm-order-btn");
        const customerNameInput = document.getElementById('pickupCustomerName');
        const customerPhoneInput = document.getElementById('pickupCustomerPhone');
        const hiddenCustomerInput = document.getElementById('hiddenCustomerName');
        const hiddenPhoneInput = document.getElementById('hiddenCustomerPhone');

        // Disable confirm button initially
        confirmBtn.disabled = true;

        function validateForm() {
            const nameValid = customerNameInput.value.trim() !== "";
            const phoneValid = /^09\d{9}$/.test(customerPhoneInput.value.trim());
            confirmBtn.disabled = !(nameValid && phoneValid);
        }

        // Validate live while typing
        customerNameInput.addEventListener("input", validateForm);
        customerPhoneInput.addEventListener("input", validateForm);

        pickupRadio.addEventListener("change", () => {
            if (pickupRadio.checked) {
                // Fill order summary
                orderSummaryList.innerHTML = "";
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        orderSummaryList.innerHTML += `<li><?= $item['quantity'] ?>x <?= htmlspecialchars($item['product_name']) ?></li>`;
                    <?php endforeach; ?>
                <?php endif; ?>

                confirmModal.show();
            }
        });

        confirmBtn.addEventListener("click", () => {
            // Allow click even if disabled for closing purposes
            if (!confirmBtn.disabled) {
                // Save values to hidden inputs only if validation passes
                hiddenCustomerInput.value = customerNameInput.value.trim();
                hiddenPhoneInput.value = customerPhoneInput.value.trim();
            }
            
            // Blur focus from the button before closing modal
            confirmBtn.blur();
            // Always close the modal when confirm is clicked
            confirmModal.hide();
        });

        // Handle close button and backdrop clicks
        modalEl.addEventListener("hide.bs.modal", () => {
            // Remove focus from any focused element within the modal before closing
            const focusedElement = modalEl.querySelector(':focus');
            if (focusedElement) {
                focusedElement.blur();
            }
        });

        // Handle all modal close events consistently
        modalEl.addEventListener("hidden.bs.modal", () => {
            // Reset pickup radio if not properly confirmed
            if (!hiddenCustomerInput.value || !hiddenPhoneInput.value) {
                pickupRadio.checked = false;
            }

            // Clear any lingering modal effects
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            
            // Reset body styles that might be stuck
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Reset form validation state
            validateForm();
        });

        // Reset form state when modal opens
        modalEl.addEventListener("shown.bs.modal", () => {
            validateForm();
            customerNameInput.focus();
        });

        // Additional safety: handle backdrop clicks
        modalEl.addEventListener("click", (e) => {
            if (e.target === modalEl) {
                confirmModal.hide();
            }
        });
    });
</script>