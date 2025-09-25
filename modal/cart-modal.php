<!-- Pickup Customer -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-2">
                <h1 class="modal-title fs-5 fw-bold" id="confirmModalLabel"
                    style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
                    <i class="bi bi-person-fill me-2"></i>Customer Info
                </h1>
                <button type="button" class="btn-close" onclick="window.location.href='cart.php'" aria-label="Close"
                    style="filter: invert(50%);"></button>
            </div>

            <!-- Body -->
            <div class="modal-body py-3">
                <!-- Pickup Icon -->
                <div class="text-center mb-3">
                    <i class="bi bi-clock-history" style="font-size: 3rem; color: var(--primary-color);"></i>
                </div>

                <!-- Info Text -->
                <div class="text-center mb-3">
                    <p class="mb-1" style="font-family: var(--secondaryFont); color: var(--text-color-dark); font-size: 1rem;">
                        Please provide your information for pickup
                    </p>
                    <small class="text-muted" style="font-family: var(--secondaryFont);">
                        We'll contact you when ready
                    </small>
                </div>

                <!-- Customer Information Form -->
                <div class="mb-3">
                    <div class="mb-3">
                        <label for="pickupCustomerName" class="form-label fw-semibold" 
                               style="font-family: var(--primaryFont); color: var(--primary-color); font-size: 0.9rem;">
                            <i class="bi bi-person me-1"></i>Full Name
                        </label>
                        <input type="text" class="form-control" id="pickupCustomerName" placeholder="Enter your full name" required
                               style="border-radius: 10px; border: 2px solid var(--secondary-color); 
                                      font-family: var(--secondaryFont); padding: 10px; font-size: 0.9rem;">
                    </div>
                    
                    <div class="mb-3">
                        <label for="pickupCustomerPhone" class="form-label fw-semibold"
                               style="font-family: var(--primaryFont); color: var(--primary-color); font-size: 0.9rem;">
                            <i class="bi bi-telephone me-1"></i>Phone Number
                        </label>
                        <input type="text" class="form-control" id="pickupCustomerPhone"
                            placeholder="09XXXXXXXXX" pattern="\d{11}" maxlength="11" inputmode="numeric"
                            oninput="formatPhoneNumber(this)" value="09" required
                            style="border-radius: 10px; border: 2px solid var(--secondary-color); 
                                   font-family: var(--secondaryFont); padding: 10px; font-size: 0.9rem;">
                    </div>
                </div>

                <!-- Quick Order Summary -->
                <div class="rounded-3 p-3 mb-3" style="background: var(--card-bg-color); border: 2px solid var(--secondary-color);">
                    <h6 class="fw-bold mb-2" style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px; font-size: 0.9rem;">
                        <i class="bi bi-list-check me-2"></i>Your Order
                    </h6>
                    <div id="orderSummaryList" style="font-family: var(--secondaryFont); color: var(--text-color-dark); font-size: 0.8rem; max-height: 80px; overflow-y: auto;">
                        <!-- Filled dynamically by JS -->
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn fw-bold px-3 py-2 flex-fill" onclick="window.location.href='cart.php'" style="
                                background: var(--card-bg-color); 
                                color: var(--text-color-dark); 
                                border: 2px solid var(--primary-color);
                                border-radius: 10px; 
                                font-family: var(--primaryFont); 
                                letter-spacing: 1px; 
                                transition: all 0.3s ease;
                                font-size: 0.85rem;
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

                    <button type="button" class="btn fw-bold px-3 py-2 confirm-order-btn flex-fill" style="
                        background: var(--text-color-dark); 
                        color: white; 
                        border: none;
                        border-radius: 10px; 
                        font-family: var(--primaryFont); 
                        letter-spacing: 1px; 
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); 
                        transition: all 0.3s ease;
                        font-size: 0.85rem;
                    " onmouseover="
                        this.style.background='var(--primary-color)';  
                        this.style.transform='translateY(-2px)';    
                        this.style.boxShadow='0 6px 12px rgba(0, 0, 0, 0.4)';
                    " onmouseout="
                        this.style.background='var(--text-color-dark)'; 
                        this.style.transform='translateY(0)';
                        this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.3)';
                    ">
                        <i class="bi bi-check-circle me-1"></i>CONFIRM
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom scrollbar for order summary */
#orderSummaryList::-webkit-scrollbar {
    width: 3px;
}

#orderSummaryList::-webkit-scrollbar-track {
    background: transparent;
}

#orderSummaryList::-webkit-scrollbar-thumb {
    background: var(--secondary-color);
    border-radius: 3px;
}
</style>

<script>
    function formatPhoneNumber(input) {
        
        let value = input.value.replace(/[^0-9]/g, '');
        
        if (!value.startsWith('09')) {
            if (value === '' || value === '0') {
                value = '09';
            } else if (value.startsWith('9')) {
                value = '0' + value;
            } else {
                value = '09' + value;
            }
        }
        
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

    
        confirmBtn.disabled = true;

        function validateForm() {
            const nameValid = customerNameInput.value.trim() !== "";
            const phoneValid = /^09\d{9}$/.test(customerPhoneInput.value.trim());
            const isValid = nameValid && phoneValid;
            
            confirmBtn.disabled = !isValid;
            
           
            if (confirmBtn.disabled) {
                confirmBtn.style.opacity = '0.6';
                confirmBtn.style.cursor = 'not-allowed';
            } else {
                confirmBtn.style.opacity = '1';
                confirmBtn.style.cursor = 'pointer';
            }
        }

        customerNameInput.addEventListener("input", validateForm);
        customerPhoneInput.addEventListener("input", validateForm);

        pickupRadio.addEventListener("change", () => {
            if (pickupRadio.checked) {
                
                orderSummaryList.innerHTML = "";
                <?php if (!empty($_SESSION['cart'])): ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        orderSummaryList.innerHTML += `<div style="padding: 3px 0; border-bottom: 1px solid var(--secondary-color); display: flex; justify-content-between;"><span><i class="bi bi-dot"></i><?= $item['quantity'] ?>x <?= htmlspecialchars($item['product_name']) ?></span><span>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span></div>`;
                    <?php endforeach; ?>
                <?php endif; ?>

                confirmModal.show();
            }
        });

        confirmBtn.addEventListener("click", () => {
            if (!confirmBtn.disabled) {
              
                const originalText = confirmBtn.innerHTML;
                confirmBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Saving...';
                confirmBtn.disabled = true;
                
               
                hiddenCustomerInput.value = customerNameInput.value.trim();
                hiddenPhoneInput.value = customerPhoneInput.value.trim();
                
             
                setTimeout(() => {
                    confirmBtn.innerHTML = originalText;
                    confirmBtn.disabled = false;
                    confirmModal.hide();
                }, 800);
            }
            
            confirmBtn.blur();
        });

        modalEl.addEventListener("hide.bs.modal", () => {
            const focusedElement = modalEl.querySelector(':focus');
            if (focusedElement) {
                focusedElement.blur();
            }
        });

        modalEl.addEventListener("hidden.bs.modal", () => {
          
            if (!hiddenCustomerInput.value || !hiddenPhoneInput.value) {
                pickupRadio.checked = false;
            }

            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            
           
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            
            validateForm();
        });

        
        modalEl.addEventListener("shown.bs.modal", () => {
            validateForm();
            customerNameInput.focus();
        });

       
        modalEl.addEventListener("click", (e) => {
            if (e.target === modalEl) {
                confirmModal.hide();
            }
        });
    });
</script>