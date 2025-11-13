<!-- Delete Confirmation Modal - CASCADE VERSION -->
<div class='modal fade' id='deleteConfirmModal' data-bs-backdrop='true' tabindex='-1'
    aria-labelledby='deleteConfirmModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-dialog-centered'>
        <div class='modal-content rounded-4 shadow-lg border-0' style='background: var(--bg-color);'>

            <div class='modal-header border-0 pb-0'>
                <h1 class='modal-title fs-4 fw-bold' id='deleteConfirmModalLabel'
                    style='font-family: var(--primaryFont); color: #dc3545; letter-spacing: 1px;'>
                    <i class='bi bi-exclamation-octagon-fill me-2'></i>DANGER: Permanent Delete
                </h1>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'
                    style='filter: invert(50%);'></button>
            </div>

            <div class='modal-body'>
                <div class='text-center mb-4'>
                    <i class='bi bi-trash3-fill' style='font-size: 4rem; color: #dc3545;'></i>
                </div>

                <div class='text-center mb-4'>
                    <p class='mb-2'
                        style='font-family: var(--secondaryFont); color: var(--text-color-dark); font-size: 1.1rem;'>
                        Are you sure you want to <strong style='color: #dc3545;'>permanently delete</strong>
                    </p>
                    <p class='fw-bold mb-2'
                        style='font-family: var(--primaryFont); color: var(--text-color-dark); font-size: 1.3rem;'>
                        <strong id='deleteItemName'></strong>
                    </p>
                    <p style='font-family: var(--secondaryFont); color: var(--text-color-dark);'>
                        from your menu?
                    </p>
                </div>

                <!-- ⚠️ CRITICAL WARNING ALERT -->
                <div class='alert alert-danger d-flex align-items-start mb-4' role='alert' 
                     style='border-left: 4px solid #dc3545; background: rgba(220, 53, 69, 0.1); 
                            border-radius: 8px; font-family: var(--secondaryFont);'>
                    <i class='bi bi-exclamation-triangle-fill me-2 mt-1' style='font-size: 1.5rem; color: #dc3545;'></i>
                    <div style='font-size: 0.9rem; color: var(--text-color-dark);'>
                        <strong style='color: #dc3545;'>WARNING:</strong> This will also delete:
                        <ul class='mb-0 mt-2' style='font-size: 0.85rem;'>
                            <li><strong>All orders</strong> containing this product</li>
                            <li><strong>Payment records</strong> for those orders</li>
                            <li><strong>Order history</strong> permanently lost</li>
                        </ul>
                        <p class='mb-0 mt-2' style='color: #dc3545; font-weight: 600;'>
                            <i class='bi bi-exclamation-circle me-1'></i>This action CANNOT be undone!
                        </p>
                    </div>
                </div>

                <form method='POST' action='menu-management.php'>
                    <input type='hidden' name='btnDeleteProduct' value='1'>
                    <input type='hidden' name='productID' id='deleteProductID' value=''>

                    <div class='d-flex gap-3 justify-content-center'>
                        <button type='button' class='btn fw-bold px-4 py-2' data-bs-dismiss='modal' 
                                style='background: var(--card-bg-color); color: var(--text-color-dark); 
                                       border: 2px solid var(--primary-color); border-radius: 10px;
                                       font-family: var(--primaryFont); letter-spacing: 1px;
                                       transition: all 0.3s ease; min-width: 120px;'
                                onmouseover='this.style.background="var(--primary-color)"; this.style.color="var(--text-color-light)";'
                                onmouseout='this.style.background="var(--card-bg-color)"; this.style.color="var(--text-color-dark)";'>
                            <i class='bi bi-x-circle me-1'></i>CANCEL
                        </button>

                        <button type='submit' class='btn fw-bold px-4 py-2' 
                                style='background: #dc3545; color: white; border: none; border-radius: 10px;
                                       font-family: var(--primaryFont); letter-spacing: 1px;
                                       box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3); transition: all 0.3s ease;
                                       min-width: 120px;' 
                                onmouseover='this.style.background="#b02a37"; this.style.transform="scale(1.02)";'
                                onmouseout='this.style.background="#dc3545"; this.style.transform="scale(1)";'>
                            <i class='bi bi-trash3-fill me-2'></i>DELETE ALL
                        </button>
                    </div>
                </form>

                <p class='text-center mt-3 mb-0' style='font-size: 0.75rem; color: #6c757d; font-family: var(--secondaryFont);'>
                    <i class='bi bi-shield-exclamation me-1'></i>
                    Deleting this product will cascade delete all related data
                </p>
            </div>
        </div>
    </div>
</div>