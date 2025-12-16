<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow-lg border-0" style="background: var(--bg-color);">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h1 class="modal-title fs-4 fw-bold" id="editModalLabel"
                    style="font-family: var(--primaryFont); color: var(--primary-color); letter-spacing: 1px;">
                    Edit Product
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="filter: invert(50%);"></button>
            </div>

            <!-- Body -->
            <div class="modal-body modalText text-start">
                <form id="editItemForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" id="edit_product_id">

                    <div class="row g-4">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Item Name <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" class="form-control" name="item_name" id="edit_item_name" required
                                    style="border: 2px solid var(--primary-color); border-radius: 10px;
                              font-family: var(--secondaryFont); background: var(--card-bg-color);
                              color: var(--text-color-dark); padding: 12px;">
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Item Group <span style="color: #dc3545;">*</span>
                                </label>
                                <select class="form-select" name="item_group" id="edit_item_group" required style="border: 2px solid var(--primary-color); border-radius: 10px;
                               font-family: var(--secondaryFont); background: var(--card-bg-color);
                               color: var(--text-color-dark); padding: 12px;">
                                    <?php
                                    $categoriesQuery = "SELECT * FROM categories";
                                    $categoriesResult = mysqli_query($conn, $categoriesQuery);
                                    while ($cat = mysqli_fetch_assoc($categoriesResult)) {
                                        echo '<option value="' . $cat['categoryID'] . '">' . htmlspecialchars($cat['categoryName']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>  

                            <!-- Availability Toggle Section -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Availability Status</label>
                                <div class="d-flex align-items-center gap-3">
                                    <label class="availability-toggle">
                                        <input type="checkbox" id="availabilityToggle" name="isAvailable" value="1">
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <span id="availabilityStatus" class="status-badge status-available">Available</span>
                                </div>
                                <small class="text-muted">Toggle to enable/disable this item in POS and menu</small>
                            </div>

                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Price <span style="color: #dc3545;">*</span>
                                </label>
                                <input type="text" class="form-control" name="menu_price" id="edit_menu_price" required
                                    style="border: 2px solid var(--primary-color); border-radius: 10px;
                              font-family: var(--secondaryFont); background: var(--card-bg-color);
                              color: var(--text-color-dark); padding: 12px;">
                            </div>


                            <div class="mb-4">
                                <label class="form-label fw-bold mb-2"
                                    style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                                    Attachment
                                </label>
                                <input type="file" class="form-control" name="attachment" id="edit_current_image" style="border: 2px solid var(--primary-color); border-radius: 10px;
                              font-family: var(--secondaryFont); background: var(--card-bg-color);
                              color: var(--text-color-dark); padding: 12px;">
                                <input type="text" class="form-control mt-2" id="edit_current_image_text" readonly
                                    style="border: 2px solid var(--primary-color); border-radius: 10px;
                              font-family: var(--secondaryFont); background: var(--card-bg-color);
                              color: var(--text-color-dark); padding: 12px;">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6 class="text-start mb-3 fw-bold"
                        style="font-family: var(--primaryFont); color: var(--text-color-dark);">
                        Ingredients
                    </h6>

                    <!-- Ingredients Container -->
                    <div id="edit-ingredients-container"
                        style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
                        <!-- JS will populate rows here -->
                    </div>

                    <!-- Add Ingredient Button -->
                    <div class="text-center mt-3">

                        <button type="button" class="btn d-flex align-items-center justify-content-center mx-auto"
                            id="edit-add-ingredient"
                            style="background: transparent; border: none; color: var(--text-color-dark); transition: all 0.3s ease;">
                            <i class="bi bi-plus-circle-fill"
                                style="font-size: 2rem; color: var(--text-color-dark); transition: all 0.3s ease;"></i>
                        </button>
                    </div>




                    <div class="d-flex gap-3 justify-content-end mt-4">
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

                        <button type="submit" id="refresh" class="btn fw-bold px-4 py-2" style="
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
                            <i class="bi bi-save-fill me-2"></i>SAVE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<style>
    /* Delete button styling */
    .btn-del {
        background: rgba(231, 76, 60, 0.15);
        color: #e74c3c;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-del:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        background-color: #e74c3c;
        color: var(--text-color-light);
    }

    /* Add Ingredient Button Hover */
    #edit-add-ingredient:hover {
        color: var(--primary-color);
        transform: scale(1.15);
    }

    #edit-ingredients-container {
        max-height: 250px;
        overflow-y: auto;
        padding-right: 5px;
        scrollbar-width: thin;
        scrollbar-color: var(--primary-color) var(--card-bg-color);
    }

    /* For Chrome, Edge, Safari */
    #edit-ingredients-container::-webkit-scrollbar {
        width: 8px;
    }

    #edit-ingredients-container::-webkit-scrollbar-track {
        background: var(--card-bg-color);
        border-radius: 10px;
    }

    #edit-ingredients-container::-webkit-scrollbar-thumb {
        background-color: var(--primary-color);
        border-radius: 10px;
        border: 2px solid var(--card-bg-color);
    }

    #edit-ingredients-container::-webkit-scrollbar-thumb:hover {
        background-color: var(--text-color-dark);
    }
</style>

<script>
document.getElementById('refresh').addEventListener('click', function () {
    setTimeout(() => {
        location.reload();
    }, 800); 
});
</script>

<script>
    // Availability Toggle Logic
    document.addEventListener('DOMContentLoaded', function () {
        const availabilityToggle = document.getElementById('availabilityToggle');
        const availabilityStatus = document.getElementById('availabilityStatus');
        const availabilityToggleLabel = document.querySelector('.availability-toggle');
        let currentProductId = null;

        // When modal opens
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            currentProductId = button.getAttribute('data-id');
            const isAvailable = button.getAttribute('data-available') === '1';
            const possibleCount = button.closest('.menu-item')
                .querySelector('.menu-stock').textContent.match(/\d+/)[0];

            // Set initial toggle + status
            availabilityToggle.checked = isAvailable;
            updateAvailabilityStatus(isAvailable);

            // Hide toggle if stock = 0
            if (parseInt(possibleCount) === 0) {
                availabilityToggleLabel.style.display = 'none';
                availabilityStatus.textContent = 'Unavailable (No Stock)';
                availabilityStatus.className = 'status-badge status-unavailable';

                // Auto-save as "No" in DB if not already
                updateProductAvailability(currentProductId, 'No');
            } else {
                availabilityToggleLabel.style.display = 'inline-block';
            }
        });

        // Handle toggle change
        availabilityToggle.addEventListener('change', function () {
            const isChecked = this.checked;
            updateAvailabilityStatus(isChecked);
            if (currentProductId) {
                updateProductAvailability(currentProductId, isChecked ? 'Yes' : 'No');
            }
        });

        function updateAvailabilityStatus(isAvailable) {
            availabilityStatus.textContent = isAvailable ? 'Available' : 'Unavailable';
            availabilityStatus.className = 'status-badge ' + (isAvailable ? 'status-available' : 'status-unavailable');
        }

        function updateProductAvailability(productId, availabilityValue) {
            fetch('../assets/menu-availability.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `btnToggleAvailability=1&productID=${productId}&newAvailability=${availabilityValue}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const productCard = document.querySelector(`.edit-btn[data-id="${productId}"]`).closest('.menu-item');
                        const badge = productCard.querySelector('.status-badge');
                        const editBtn = document.querySelector(`.edit-btn[data-id="${productId}"]`);

                        if (availabilityValue === 'Yes') {
                            productCard.classList.remove('unavailable');
                            badge.textContent = 'Available';
                            badge.className = 'status-badge status-available';
                            if (editBtn) editBtn.setAttribute('data-available', '1');
                        } else {
                            productCard.classList.add('unavailable');
                            badge.textContent = 'Unavailable';
                            badge.className = 'status-badge status-unavailable';
                            if (editBtn) editBtn.setAttribute('data-available', '0');
                        }

                        const toastEl = document.getElementById('updateToast');
                        if (toastEl) {
                            const bsToast = new bootstrap.Toast(toastEl);
                            bsToast.show();
                        }
                    } else {
                        console.error('Failed to update availability:', data.message);
                    }
                })
                .catch(err => console.error('Error updating availability:', err));
        }
    });
</script>

<script>
    // Disable Unit Dropdowns but keep them in form submission
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('edit-ingredients-container');
        
        // Function to disable unit dropdowns
        function disableUnitDropdowns() {
            const unitSelects = container.querySelectorAll('select[name="unit[]"]');
            unitSelects.forEach(select => {
                select.disabled = true;
                select.style.backgroundColor = '#e9ecef';
                select.style.cursor = 'not-allowed';
                select.style.opacity = '0.6';
                select.style.pointerEvents = 'none';
                // Also disable via attribute
                select.setAttribute('disabled', 'disabled');
            });
        }
        
        // Watch for new ingredients being added
        const observer = new MutationObserver(function() {
            disableUnitDropdowns();
        });
        
        observer.observe(container, {
            childList: true,
            subtree: true
        });
        
        // Initial disable on modal open
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function() {
            setTimeout(disableUnitDropdowns, 200);
        });
        
        // Re-enable before form submit para ma-include sa POST data
        document.getElementById('editItemForm').addEventListener('submit', function(e) {
            const unitSelects = container.querySelectorAll('select[name="unit[]"]');
            unitSelects.forEach(select => {
                select.disabled = false;
                select.removeAttribute('disabled');
            });
        });
    });
</script>