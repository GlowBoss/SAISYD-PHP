<!-- Order Confirmation Modal -->
<div class="modal fade" id="orderConfirmModal" tabindex="-1" aria-labelledby="orderConfirmModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-2">
                <h1 class="modal-title fs-5 fw-bold" id="orderConfirmModalLabel"
                    style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
                    <i class="bi bi-clipboard-check me-2"></i>Confirm Order
                </h1>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" style="filter: invert(1);"></button>
            </div>

            <!-- Body -->
            <div class="modal-body py-2">
                <!-- Quick Summary -->
                <div class="rounded-3 p-3 mb-3 text-center"
                    style="background: transparent; color: var(--text-color-dark;">
                    <div class="row g-0">
                        <div class="col-4">
                            <div class="fw-bold" id="confirmTotalItems">0</div>
                            <small class="opacity-75">Items</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold fs-5" id="confirmTotal">₱0.00</div>
                            <small class="opacity-75">Total</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold small" id="orderMethodBanner">-</div>
                            <small class="opacity-75">Method</small>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <div class="rounded-3 p-2"
                            style="background: var(--card-bg-color); border: 1px solid var(--secondary-color);">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="fw-bold"
                                    style="color: var(--primary-color); font-family: var(--primaryFont);">
                                    <i class="bi bi-bag me-1"></i>Items
                                </small>
                                <small class="text-muted" id="confirmSubtotal">₱0.00</small>
                            </div>
                            <div id="confirmOrderItems"
                                style="font-size: 0.8rem; color: var(--text-color-dark); max-height: 80px; overflow-y: auto;">
                                <!-- Filled dynamically -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="rounded-3 p-2" style="background: transparent;">
                            <small class="fw-bold d-block"
                                style="color: var(--primary-color); font-family: var(--primaryFont);">
                                <i class="bi bi-clipboard-data me-1"></i>Method
                            </small>
                            <small id="confirmOrderMethod" style="color: var(--text-color-dark);">-</small>
                            <div id="confirmCustomerInfo"
                                style="display: none; font-size: 0.7rem; color: var(--text-color-dark); margin-top: 4px;">
                                <!-- Customer info -->
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rounded-3 p-2" style="background: transparent;">
                            <small class="fw-bold d-block"
                                style="color: var(--primary-color); font-family: var(--primaryFont);">
                                <i class="bi bi-credit-card me-1"></i>Payment
                            </small>
                            <small id="confirmPaymentMethod" style="color: var(--text-color-dark);">-</small>
                            <div id="confirmGcashRef"
                                style="display: none; font-size: 0.7rem; color: var(--text-color-dark); margin-top: 4px;">
                                <!-- GCash ref -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Message -->
                <div class="text-center mb-3">
                    <p class="mb-0"
                        style="font-family: var(--primaryFont); color: var(--text-color-dark); font-size: 0.95rem; font-weight: 600;">
                        Place this order?
                    </p>

                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn fw-bold px-3 py-2 flex-fill" data-bs-dismiss="modal" style="
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
                            " onmouseout="
                                this.style.background='var(--card-bg-color)'; 
                                this.style.color='var(--text-color-dark)';
                            ">
                        CANCEL
                    </button>

                    <button type="button" class="btn fw-bold px-3 py-2 flex-fill" id="finalConfirmBtn" style="
                        background: var(--text-color-dark); 
                        color: white; 
                        border: none;
                        border-radius: 10px; 
                        font-family: var(--primaryFont); 
                        letter-spacing: 1px; 
                        transition: all 0.3s ease;
                        font-size: 0.85rem;
                    " onmouseover="
                        this.style.background='var(--primary-color)';
                    " onmouseout="
                        this.style.background='var(--text-color-dark)';
                    ">
                        <i class="bi bi-check-circle me-1"></i>CONFIRM
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

    #confirmOrderItems::-webkit-scrollbar {
        width: 3px;
    }

    #confirmOrderItems::-webkit-scrollbar-track {
        background: transparent;
    }

    #confirmOrderItems::-webkit-scrollbar-thumb {
        background: var(--secondary-color);
        border-radius: 3px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const orderConfirmModal = new bootstrap.Modal(document.getElementById('orderConfirmModal'), {
            backdrop: true,
            keyboard: true,
            focus: true
        });

        const checkoutBtn = document.getElementById('checkoutBtn');
        const checkoutForm = document.getElementById('checkoutForm');
        const finalConfirmBtn = document.getElementById('finalConfirmBtn');

        if (checkoutBtn && checkoutForm) {
            checkoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation(); 

                
                const formData = new FormData(checkoutForm);
                const orderType = formData.get('order_type');
                const paymentMethod = formData.get('payment_method');
                const refNumber = formData.get('ref_number');

                let hasError = false;
                let errorMessage = '';

                if (!orderType || !paymentMethod) {
                    errorMessage = 'Please select both order method and payment method.';
                    hasError = true;
                } else if (paymentMethod === 'gcash') {
                   
                    if (!refNumber || refNumber.trim() === '') {
                        errorMessage = 'Please enter the last 4 digits of GCash reference number.';
                        hasError = true;
                    } else if (!/^\d{4}$/.test(refNumber.trim())) {
                        
                        errorMessage = 'GCash reference number must be exactly 4 digits.';
                        hasError = true;
                    }
                } else if (<?= json_encode(empty($_SESSION['cart'])) ?>) {
                    errorMessage = 'Your cart is empty.';
                    hasError = true;
                }

                if (hasError) {
                    showErrorMessage(errorMessage);
                    return;
                }

           
                populateOrderConfirmation(formData);

       
                orderConfirmModal.show();
            });
        }

  
        if (finalConfirmBtn) {
            finalConfirmBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const orderType = document.querySelector('input[name="order_type"]:checked')?.value;
                const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
                const refNumberInput = document.getElementById('refNumber');
                
       
                if (paymentMethod === 'gcash') {
                    const refNumber = refNumberInput ? refNumberInput.value.trim() : '';
                    
                    if (!refNumber || !/^\d{4}$/.test(refNumber)) {
                        showErrorMessage('Invalid GCash reference number. Must be exactly 4 digits.');
                        return;
                    }
                }

                if (orderType === 'pickup') {
                    const hiddenNameInput = checkoutForm.querySelector('input[name="customer_name"]');
                    const hiddenPhoneInput = checkoutForm.querySelector('input[name="customer_phone"]');

                    if (!hiddenNameInput || !hiddenPhoneInput || !hiddenNameInput.value.trim() || !hiddenPhoneInput.value.trim()) {
                        showErrorMessage('Customer information is missing for pickup order. Please select pickup option again.');
                        return;
                    }
                }

                finalConfirmBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Processing...';
                finalConfirmBtn.disabled = true;

                const confirmedInput = document.createElement('input');
                confirmedInput.type = 'hidden';
                confirmedInput.name = 'confirmed_checkout';
                confirmedInput.value = '1';
                checkoutForm.appendChild(confirmedInput);

                const checkoutInput = document.createElement('input');
                checkoutInput.type = 'hidden';
                checkoutInput.name = 'checkout';
                checkoutInput.value = '1';
                checkoutForm.appendChild(checkoutInput);

                setTimeout(() => {
                    orderConfirmModal.hide();
                    checkoutForm.submit();
                }, 500);
            });
        }

        const refNumberInput = document.getElementById('refNumber');
        if (refNumberInput) {
            refNumberInput.addEventListener('input', function(e) {
                
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value.length > 4) {
                    value = value.slice(0, 4);
                }
                e.target.value = value;

                const gcashField = document.getElementById('gcashField');
                if (gcashField) {
                    if (value.length === 4) {

                        e.target.style.borderColor = '#28a745';
                        e.target.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
                    } else if (value.length > 0) {
                        
                        e.target.style.borderColor = '#dc3545';
                        e.target.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
                    } else {
                        
                        e.target.style.borderColor = '';
                        e.target.style.boxShadow = '';
                    }
                }
            });

        }

        function populateOrderConfirmation(formData) {
           
            const cartItems = <?= json_encode($_SESSION['cart'] ?? []) ?>;

            // Populate order items 
            const orderItemsList = document.getElementById('confirmOrderItems');
            orderItemsList.innerHTML = '';

            cartItems.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'd-flex justify-content-between mb-1';

                let customizations = '';
                if (item.sugar && item.sugar !== '' && item.sugar !== '0') {
                    customizations += ` (${item.sugar})`;
                }
                if (item.ice && item.ice !== '') {
                    customizations += ` (${item.ice})`;
                }
                if (item.notes && item.notes !== '') {
                    customizations += ` *${item.notes}`;
                }

                itemDiv.innerHTML = `
                    <span>${item.quantity}x ${item.product_name}${customizations}</span>
                    <span>₱${(item.price * item.quantity).toFixed(2)}</span>
                `;

                orderItemsList.appendChild(itemDiv);
            });

            // Populate totals
            const cartTotal = <?= getCartTotal() ?>;
            const cartCount = <?= getCartItemCount() ?>;

            document.getElementById('confirmTotalItems').textContent = cartCount;
            document.getElementById('confirmSubtotal').textContent = '₱' + cartTotal.toFixed(2);
            document.getElementById('confirmTotal').textContent = '₱' + cartTotal.toFixed(2);

            // Populate order method
            const orderType = formData.get('order_type');
            let orderMethodText = '';
            let bannerText = '';
            const confirmCustomerInfo = document.getElementById('confirmCustomerInfo');

            switch (orderType) {
                case 'dine-in':
                    orderMethodText = '<i class="bi bi-shop me-1"></i>Dine-in';
                    bannerText = 'Dine-in';
                    confirmCustomerInfo.style.display = 'none';
                    break;
                case 'takeout':
                    orderMethodText = '<i class="bi bi-bag-check me-1"></i>Takeout';
                    bannerText = 'Takeout';
                    confirmCustomerInfo.style.display = 'none';
                    break;
                case 'pickup':
                    orderMethodText = '<i class="bi bi-clock me-1"></i>Pickup';
                    bannerText = 'Pickup';

                    const hiddenNameInput = checkoutForm.querySelector('input[name="customer_name"]');
                    const hiddenPhoneInput = checkoutForm.querySelector('input[name="customer_phone"]');

                    if (hiddenNameInput && hiddenPhoneInput && hiddenNameInput.value && hiddenPhoneInput.value) {
                        confirmCustomerInfo.innerHTML = `${hiddenNameInput.value}<br>${hiddenPhoneInput.value}`;
                        confirmCustomerInfo.style.display = 'block';
                    }
                    break;
            }
            document.getElementById('confirmOrderMethod').innerHTML = orderMethodText;
            document.getElementById('orderMethodBanner').textContent = bannerText;

            // Populate payment method
            const paymentMethod = formData.get('payment_method');
            let paymentMethodText = '';
            const gcashRef = document.getElementById('confirmGcashRef');

            switch (paymentMethod) {
                case 'cash':
                    paymentMethodText = '<i class="bi bi-cash me-1"></i>Cash';
                    gcashRef.style.display = 'none';
                    break;
                case 'gcash':
                    paymentMethodText = '<i class="bi bi-phone me-1"></i>GCash';

                    const refNumber = formData.get('ref_number');
                    if (refNumber) {
                        gcashRef.innerHTML = `Ref: ****${refNumber}`;
                        gcashRef.style.display = 'block';
                    }
                    break;
            }
            document.getElementById('confirmPaymentMethod').innerHTML = paymentMethodText;
        }

        function showErrorMessage(message) {
            
            const existingToasts = document.querySelectorAll('.toast[role="alert"]');
            existingToasts.forEach(toast => {
                if (toast.querySelector('.bi-x-circle-fill')) { 
                    toast.remove();
                }
            });

            const toastContainer = document.querySelector('.toast-container') || createToastContainer();

            const errorToast = document.createElement('div');
            errorToast.className = 'toast align-items-center border-0 fade show';
            errorToast.setAttribute('role', 'alert');
            errorToast.setAttribute('aria-live', 'assertive');
            errorToast.setAttribute('aria-atomic', 'true');
            errorToast.setAttribute('data-bs-delay', '8000');
            errorToast.setAttribute('data-bs-autohide', 'true');
            errorToast.style.cssText = `
                background-color: var(--text-color-dark); 
                color: var(--text-color-light); 
                border-radius: 12px; 
                box-shadow: 0 4px 12px rgba(0,0,0,0.25);
                z-index: 9999;
                position: relative;
            `;

            errorToast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-x-circle-fill ms-3 me-3" style="font-size: 1.2rem; color: #dc3545;"></i>
                    <div class="toast-body py-3" style="font-family: var(--secondaryFont);">
                        ${message}
                    </div>
                </div>
            `;

            toastContainer.appendChild(errorToast);

            const toast = new bootstrap.Toast(errorToast);
            toast.show();

            errorToast.addEventListener('hidden.bs.toast', () => {
                errorToast.remove();
            });
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3 z-3';
            document.body.appendChild(container);
            return container;
        }
    });
</script>