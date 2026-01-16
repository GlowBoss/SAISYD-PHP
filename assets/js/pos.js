document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('quantityModal');
    const addToOrderForm = document.getElementById('addToOrderForm');
    const receiptContainer = document.getElementById('receipt');
    const totalValue = document.getElementById('totalValue');
    const orderNowBtn = document.getElementById('orderNowBtn');
    const confirmModalEl = document.getElementById('confirmModal');
    const orderSummaryList = document.getElementById('orderSummaryList');
    const orderNotesInput = document.getElementById('orderNotes');
    const paymentModeSelect = document.getElementById('paymentMode');
    const refNumberContainer = document.getElementById('refNumberContainer');

    let cartItems = [];

    // CATEGORY FILTERING
    const categoryPills = document.querySelectorAll('.category-pill');
    const productGrid = document.getElementById('maincontainer');
    let allProductsData = [];

    document.querySelectorAll('#maincontainer > div').forEach(col => {
        const item = col.querySelector('.menu-item');
        const categoryInput = col.querySelector('input[name="category"]');
        allProductsData.push({
            element: col,
            name: item.querySelector('.menu-name').textContent.trim(),
            price: parseFloat(item.querySelector('.menu-price').textContent.replace(/[^\d.]/g, '')),
            category: categoryInput ? categoryInput.value.trim() : ''
        });
    });

    categoryPills.forEach(pill => {
        pill.addEventListener('click', function (e) {
            e.preventDefault();
            const selectedCategory = this.textContent.trim();
            categoryPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            smoothFilterProducts(selectedCategory);
        });
    });

    function smoothFilterProducts(selectedCategory) {
        allProductsData.forEach(data => {
            const match = selectedCategory === 'All' || data.category === selectedCategory;
            data.element.style.display = match ? 'block' : 'none';
            if (match) {
                data.element.style.opacity = 0;
                data.element.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    data.element.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    data.element.style.opacity = 1;
                    data.element.style.transform = 'translateY(0)';
                }, 0);
            }
        });
    }

    // UPDATE HIDDEN SUGAR/ICE INPUTS
    document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const value = this.dataset.value;
            const dropdown = this.closest('.dropdown');
            const hiddenInput = dropdown.querySelector('input[type="hidden"]');
            const btn = dropdown.querySelector('button');

            if (hiddenInput) hiddenInput.value = value;
            if (btn) btn.textContent = value;
        });
    });

    // OPEN QUANTITY MODAL ON CLICK
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('add-to-order-btn')) {
            const btn = e.target;
            const productId = btn.dataset.id;

            document.getElementById('modal-product-id').value = productId;
            document.getElementById('modal-product-name').value = btn.dataset.name;
            document.getElementById('modal-product-price').value = btn.dataset.price;
            document.getElementById('modal-category').value = btn.dataset.category;

            const sugarInput = document.getElementById('sugarInput' + productId);
            const iceInput = document.getElementById('iceInput' + productId);

            document.getElementById('modal-sugar-input').value = sugarInput ? sugarInput.value : '';
            document.getElementById('modal-ice-input').value = iceInput ? iceInput.value : '';

            document.getElementById('quantity').value = 1;
            document.getElementById('modal-quantity-input').value = 1;

            const modalInstance = new bootstrap.Modal(modalEl);
            modalInstance.show();
        }
    });

    // ADD TO ORDER FORM (AJAX + CART STORAGE)
    addToOrderForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        const product = {
            id: formData.get('product_id'),
            name: formData.get('product_name'),
            price: parseFloat(formData.get('product_price')),
            qty: parseInt(formData.get('quantity')),
            sugar: formData.get('sugar'),
            ice: formData.get('ice')
        };
        cartItems.push(product);

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newReceipt = doc.querySelector('#receipt');
                const newTotal = doc.querySelector('#totalValue');

                if (newReceipt) receiptContainer.innerHTML = newReceipt.innerHTML;
                if (newTotal) totalValue.textContent = newTotal.textContent;

                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            })
            .catch(err => console.error(err));
    });

    // ORDER NOW BUTTON - MODIFIED TO CHECK FOR EMPTY CART
    if (orderNowBtn) {
        orderNowBtn.addEventListener('click', function () {
            // Check if cart is empty
            const cartItems = document.querySelectorAll('#receipt .receipt-item');
            if (cartItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No items in cart',
                    text: 'Please add items to your order before proceeding.',
                    showConfirmButton: true,
                    confirmButtonColor: 'var(--primary-color)',
                    didOpen: () => {
                        const btn = Swal.getConfirmButton();
                        btn.style.transition = 'background 0.3s';
                        btn.addEventListener('mouseenter', () => {
                            btn.style.background = 'var(--btn-hover1)';
                        });
                        btn.addEventListener('mouseleave', () => {
                            btn.style.background = 'var(--primary-color)';
                        });
                    },
                    timerProgressBar: false,
                    toast: false,
                    position: 'center'
                });
                return;
            }




            orderSummaryList.innerHTML = '';

            document.querySelectorAll('#receipt .receipt-item').forEach(item => {
                const name = item.querySelector('.item-name')?.textContent.trim() || 'Item';
                const qty = item.querySelector('.item-qty')?.textContent.trim() || '1x';
                const price = item.querySelector('.item-price')?.textContent.trim() || '₱0';
                const sugar = item.querySelector('.item-sugar')?.textContent.trim();
                const ice = item.querySelector('.item-ice')?.textContent.trim();

                let details = `${qty} ${name}`;
                if (sugar || ice) {
                    const opts = [];
                    if (sugar) opts.push(`Sugar: ${sugar}`);
                    if (ice) opts.push(`Ice: ${ice}`);
                    details += ` (${opts.join(', ')})`;
                }

                const li = document.createElement('li');
                li.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                li.innerHTML = `<span>${details}</span><span>${price}</span>`;
                orderSummaryList.appendChild(li);
            });

            const confirmModal = new bootstrap.Modal(confirmModalEl);
            confirmModal.show();
        });
    }

    // REMOVE ITEM
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item-btn')) {
            const index = e.target.dataset.index;
            const formData = new FormData();
            formData.append('remove_item', index);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newReceipt = doc.querySelector('#receipt');
                    const newTotal = doc.querySelector('#totalValue');

                    if (newReceipt) receiptContainer.innerHTML = newReceipt.innerHTML;
                    if (newTotal) totalValue.textContent = newTotal.textContent;
                })
                .catch(err => console.error(err));
        }
    });

    // QUANTITY CONTROL
    const qtyInput = document.getElementById('quantity');
    const hiddenQtyInput = document.getElementById('modal-quantity-input');

    if (qtyInput) qtyInput.value = 1;
    if (hiddenQtyInput) hiddenQtyInput.value = 1;

    window.increaseQuantity = function () {
        const val = parseInt(qtyInput.value || 1) + 1;
        qtyInput.value = val;
        hiddenQtyInput.value = val;
    }

    window.decreaseQuantity = function () {
        let val = parseInt(qtyInput.value || 1);
        if (val > 1) val--;
        qtyInput.value = val;
        hiddenQtyInput.value = val;
    }

// DROPDOWN BUTTONS AND TOGGLE REF NUMBER
document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
  item.addEventListener('click', function (e) {
    e.preventDefault();
    const value = this.dataset.value;
    const dropdown = this.closest('.dropdown');
    const hiddenInput = dropdown.querySelector('input[type="hidden"]');
    const btn = dropdown.querySelector('button');

    if (hiddenInput) hiddenInput.value = value;
    if (btn) btn.textContent = value;

    if (hiddenInput && hiddenInput.id === 'paymentModeInput') {
      const refNumberContainer = document.getElementById('refNumberContainer');
      if (value === 'Mobile Payment') {
        refNumberContainer.classList.remove('d-none');
      } else {
        refNumberContainer.classList.add('d-none');
      }
    }
  });
});

    // CANCEL ORDER BUTTON
    const cancelOrderBtn = document.getElementById('cancelOrderBtn');

    if (cancelOrderBtn) {
        cancelOrderBtn.addEventListener('click', function () {
            if (cartItems.length === 0) return;

            Swal.fire({
                title: 'Cancel Order?',
                text: "This will remove all items from the cart.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--primary-color)',
                cancelButtonColor: 'var(--btn-hover1)',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                  
                    cartItems = [];
                    receiptContainer.innerHTML = '';
                    totalValue.textContent = '0.00';

                    const formData = new FormData();
                    formData.append('cancel_order', '1');

                    fetch(window.location.href, {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.text())
                        .catch(err => console.error(err));
                    Swal.fire({
                        icon: 'success',
                        title: 'Order canceled',
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            });
        });
    }

});