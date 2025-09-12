<!-- Quantity -->
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<!-- Customize Order Modal -->
<div class="modal fade" id="item-customization" data-bs-backdrop="true" tabindex="-1"
    aria-labelledby="itemCustomizationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h1 class="modal-title fs-4 fw-bold"
                    style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
                    Customize Order
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="filter: invert(50%);"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <form method="POST" action="" id="orderForm">
                    <!-- Hidden fields -->
                    <input type="hidden" id="modal_product_id" name="product_id">
                    <input type="hidden" id="modal_product_name" name="product_name">
                    <input type="hidden" id="modal_price" name="price">
                    <input type="hidden" id="modal_category" name="category">

                    <!-- Item name & price -->
                    <h2 class="itemName text-center fw-bold"
                        style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                        Product Name
                    </h2>
                    <h4 class="itemPrice text-center mb-4"
                        style="color: var(--primary-color); font-family: var(--secondaryFont); font-weight: 600;">
                        ₱0.00
                    </h4>

                    <!-- Sugar -->
                    <div class="mb-3" id="sugarOption">
                        <label class="form-label fw-bold"
                            style="font-family: var(--primaryFont); color: var(--text-color-dark);">Sugar Level</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check"><input class="form-check-input" type="radio" name="sugar"
                                    value="No Sugar">
                                <label class="form-check-label" style="font-family: var(--secondaryFont);">No
                                    Sugar</label>
                            </div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="sugar"
                                    value="25% Sugar">
                                <label class="form-check-label" style="font-family: var(--secondaryFont);">25%</label>
                            </div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="sugar"
                                    value="50% Sugar">
                                <label class="form-check-label" style="font-family: var(--secondaryFont);">50%</label>
                            </div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="sugar"
                                    value="75% Sugar">
                                <label class="form-check-label" style="font-family: var(--secondaryFont);">75%</label>
                            </div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="sugar"
                                    value="100% Sugar" checked>
                                <label class="form-check-label" style="font-family: var(--secondaryFont);">100%</label>
                            </div>
                        </div>
                    </div>

                    <!-- Ice -->
                    <div class="mb-3" id="iceOption">
                        <label class="form-label fw-bold"
                            style="font-family: var(--primaryFont); color: var(--text-color-dark);">Ice Options</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check"><input class="form-check-input" type="radio" name="ice"
                                    value="Extra Ice">
                                <label class="form-check-label" style="font-family: var(--secondaryFont);">Extra Ice</label>
                            </div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="ice"
                                    value="Less Ice">
                                <label class="form-check-label" style="font-family: var(--secondaryFont);">Less Ice</label>
                            </div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="ice"
                                    value="Default Ice" checked>
                                <label class="form-check-label"
                                    style="font-family: var(--secondaryFont);">Default Ice</label>
                            </div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="ice"
                                    value="No Ice">
                                <label class="form-check-label" style="font-family: var(--secondaryFont);">None Ice</label>
                            </div>
                        </div>
                    </div>


                    <div class="mb-4 text-center" style="font-family: var(--primaryFont);">
                        <label class="form-label fw-bold d-block mb-2"
                            style="color: var(--text-color-dark); font-size: var(--subheading);">Quantity</label>
                        <div class="d-flex justify-content-center align-items-center gap-3">
                            <button type="button" onclick="decreaseQuantity()"
                                style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-color); color: var(--text-color-light); border: none; font-weight: bold; font-size: 1.2rem; transition: all 0.3s ease;"
                                onmouseover="this.style.background='var(--btn-hover1)'; this.style.transform='translateY(-2px)';"
                                onmouseout="this.style.background='var(--primary-color)'; this.style.transform='translateY(0)';">
                                −
                            </button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="999"
                                style="width: 70px; height: 40px; text-align: center; font-weight: bold; font-size: 1.1rem; border: 2px solid var(--primary-color); border-radius: 10px; font-family: var(--secondaryFont); color: var(--text-color-dark); background: var(--card-bg-color); box-shadow: inset 0 1px 3px rgba(0,0,0,0.1); -moz-appearance: textfield; -webkit-appearance: none; margin: 0;">
                            <button type="button" onclick="increaseQuantity()"
                                style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-color); color: var(--text-color-light); border: none; font-weight: bold; font-size: 1.2rem; transition: all 0.3s ease;"
                                onmouseover="this.style.background='var(--btn-hover1)'; this.style.transform='translateY(-2px)';"
                                onmouseout="this.style.background='var(--primary-color)'; this.style.transform='translateY(0)';">
                                +
                            </button>
                        </div>
                    </div>


                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="customer_notes" class="form-label fw-bold"
                            style="font-family: var(--primaryFont); color: var(--text-color-dark);">Customer
                            Notes</label>
                        <textarea class="form-control" name="notes" id="customer_notes" rows="3"
                            placeholder="Add any notes..."
                            style="resize: none; border-radius: 10px; border: 1.5px solid var(--primary-color); font-family: var(--secondaryFont);"></textarea>
                    </div>

                    <!-- Error Message -->
                    <div id="formError" class="alert alert-danger py-2 px-3 d-none"
                        style="font-size:0.9rem; border-radius:8px;">
                        Please check your selections before adding to cart.
                    </div>

                    <!-- Add to Cart -->
                    <button type="submit" name="add_to_cart"
                        class="btn w-100 fw-bold d-flex align-items-center justify-content-center gap-2" style="
                            background: var(--primary-color); 
                            color: var(--text-color-light); 
                            border-radius: 10px; 
                            font-family: var(--primaryFont); 
                            letter-spacing: 1px; 
                            box-shadow: 0 4px 8px rgba(0,0,0,0.25); 
        t                   ransition: all 0.3s ease;
                        " onmouseover="
                            this.style.boxShadow='0 6px 12px rgba(0,0,0,0.35)'; 
                            this.style.transform='translateY(-2px)';
                            this.style.background='var(--btn-hover1)';
                        " onmouseout="
                            this.style.boxShadow='0 4px 8px rgba(0,0,0,0.25)'; 
                            this.style.transform='translateY(0)';
                            this.style.background='var(--primary-color)';
                        ">
                        <i class="bi bi-cart-plus-fill"></i> ADD TO CART
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<?php if (isset($_SESSION['cart_message'])): ?>
    <div class="toast-container position-fixed bottom-0 end-0 p-3 z-3">
        <div id="orderToast" class="toast align-items-center border-0 fade" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="3000" data-bs-autohide="true"
            style="background-color: var(--text-color-dark); color: var(--text-color-light); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.25);">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill ms-3" style="font-size: 1.2rem; color: var(--accent-color);"></i>
                <div class="toast-body" style="font-family: var(--secondaryFont);">
                    <?php echo $_SESSION['cart_message']; ?>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"
                    style="filter: invert(1);"></button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toastEl = document.getElementById('orderToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    </script>
    <?php unset($_SESSION['cart_message']); ?>
<?php endif; ?>

<script>
    // Validation before submit
    document.getElementById('orderForm').addEventListener('submit', function (e) {
        let qty = document.getElementById('quantity').value;
        if (qty <= 0) {
            e.preventDefault();
            document.getElementById('formError').classList.remove('d-none');
            document.getElementById('formError').innerText = "Quantity must be at least 1.";
        }
    });
</script>